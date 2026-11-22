"""Check per-provider limits, settings changes and reservations across PHP processes."""
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]


class CheckRateTest(unittest.TestCase):
    def test_limits_and_concurrent_requests(self):
        with tempfile.TemporaryDirectory() as folder:
            base = Path(folder)
            lib = base / 'lib'
            shutil.copytree(ROOT / 'plib/library', lib)
            (lib / 'data').mkdir()
            (lib / 'VtDataDir.php').write_text('''<?php
class VtDataDir {
    public static function path(string $f): string { return __DIR__ . '/data/' . $f; }
    public static function ensureExists(): void {}
}''')
            test = base / 'check.php'
            test.write_text('''<?php
require __DIR__ . '/lib/VtCheckRateLimiter.php';
function check($ok,$message) { if (!$ok) throw new Exception($message); }
$s=new VtSettings('admin');
foreach (range(1,5) as $limit) {
    $s->set('check_rates',['avg'=>$limit]);
    @unlink(VtDataDir::path('vt_check_rate_avg.json'));
    check(VtCheckRateLimiter::reserve('avg',1000)===0,'initial reservation');
    check(VtCheckRateLimiter::reserve('avg',1000)===(int)ceil(60/$limit),'request spacing');
    check(VtCheckRateLimiter::reserve('avg',1000+60/$limit)===0,'next slot opens');
}
check(VtCheckRateLimiter::reserve('avast',1000)===0,'separate provider quota');
check(VtCheckRateLimiter::reserve('clamav',1000)===0,'internal scanner quota');
$s->set('check_rates',['avg'=>1]);
check(VtCheckRateLimiter::reserve('avg',1015)>0,'lower limit applies to existing reservations');
check(VtCheckRateLimiter::reserve('avg',1100)===0,'old reservations expire');
try { VtCheckRateLimiter::reserve('../bad',1000); throw new Exception('invalid source accepted'); }
catch (InvalidArgumentException $e) {}
$s->set('check_rates',['maldet'=>1]);
echo "Limit boundaries and independent scanner/provider settings passed\\n";
''')
            subprocess.run(['php', str(test)], check=True)
            worker = base / 'worker.php'
            worker.write_text("<?php require __DIR__ . '/lib/VtCheckRateLimiter.php'; echo VtCheckRateLimiter::reserve('maldet',2000);")
            workers = [subprocess.Popen(['php', str(worker)], stdout=subprocess.PIPE, text=True) for _ in range(5)]
            results = [w.communicate(timeout=10)[0] for w in workers]
            self.assertEqual(1, results.count('0'), results)
            self.assertEqual(4, results.count('60'), results)

    def test_cached_results_and_rate_waits(self):
        with tempfile.TemporaryDirectory() as folder:
            base = Path(folder)
            lib = base / 'lib'
            shutil.copytree(ROOT / 'plib/library', lib)
            (lib / 'data').mkdir()
            (lib / 'VtDataDir.php').write_text("<?php class VtDataDir { public static function path(string $f): string { return __DIR__ . '/data/' . $f; } public static function ensureExists(): void {} }")
            (lib / 'VtAvast.php').write_text("<?php class VtAvast { const PRODUCTS=[146=>'Avast',147=>'AVG']; public static int $calls=0; public static function check($d,$p): array { self::$calls++; return ['state'=>'ok','checked_at'=>time(),'http'=>200,'findings'=>[]]; } }")
            test = base / 'cache.php'
            test.write_text('''<?php
require __DIR__ . '/lib/VtAvastStore.php';
function check($ok,$m) { if (!$ok) throw new Exception($m); }
VtAvastStore::live('example.com',147,true);
$before=file_get_contents(VtDataDir::path('vt_avast.json'));
$r=VtAvastStore::live('example.com',147);
check(!empty($r['reused']) && VtAvast::$calls===1,'cached result costs no extra request');
$r=VtAvastStore::live('example.com',147,true);
check($r['state']==='rate_limited' && $r['retry_after']>0 && VtAvast::$calls===1,'rate limit stops upstream request');
check(file_get_contents(VtDataDir::path('vt_avast.json'))===$before,'rate wait preserves previous verdict');
echo "Cache reuse and throttled provider requests passed\\n";
''')
            subprocess.run(['php', str(test)], check=True)


if __name__ == '__main__':
    unittest.main()
