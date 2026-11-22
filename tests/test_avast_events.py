"""Exercise real history/store code with local data and a fake AVG client."""
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]


class AvastHistoryTest(unittest.TestCase):
    def test_observations_and_changes(self):
        with tempfile.TemporaryDirectory() as folder:
            base = Path(folder)
            lib = base / 'lib'
            shutil.copytree(ROOT / 'plib/library', lib)
            (lib / 'VtDataDir.php').write_text('''<?php
class VtDataDir {
    public static function path(string $f): string { return __DIR__ . '/data/' . $f; }
    public static function ensureExists(): void {}
}''')
            (lib / 'data').mkdir()
            (lib / 'VtCheckRateLimiter.php').write_text('<?php class VtCheckRateLimiter { public static function reserve($source): int { return 0; } }')
            (lib / 'VtAvast.php').write_text('''<?php
class VtAvast {
    const PRODUCTS = [146 => 'Avast', 147 => 'AVG'];
    public static array $next;
    public static function check($domain, $product): array { return self::$next; }
}''')
            test = base / 'check.php'
            test.write_text('''<?php
require __DIR__ . '/lib/VtAvastStore.php';
function check($condition, $message) { if (!$condition) throw new Exception($message); }
$domain='bmsprocesfinanciering.nl'; $ts=time()-100;
function response($ts,$findings=[],$state='ok') {
    return ['state'=>$state,'checked_at'=>$ts,'findings'=>$findings,'http'=>$state==='ok'?200:503];
}
// Existing clear cache becomes a truthful first observation at its original time.
file_put_contents(VtDataDir::path('vt_avast.json'),json_encode([$domain=>[147=>response($ts)]]));
VtAvastStore::recordCachedObservations();
VtAvastStore::recordCachedObservations();
$history=VtSiteLog::history($domain);
check(count($history)===1 && $history[0]['observed'] && $history[0]['from']===null,'first clear is an observation');
check($history[0]['ts']===$ts,'preserve check timestamp');
// A classification then a failed request then a clear response must retain the baseline.
VtAvast::$next=response($ts+10,[['classification'=>'URL:Botnet','detection'=>'test']]);
VtAvastStore::live($domain,147,true);
VtAvast::$next=response($ts+20,[],'failed'); VtAvastStore::live($domain,147,true);
check(count(VtSiteLog::history($domain))===2,'failed check must not become clear');
VtAvast::$next=response($ts+30); VtAvastStore::live($domain,147,true);
$history=VtSiteLog::history($domain);
check(count($history)===3,'clear transition recorded');
check($history[0]['from']==='URL:Botnet' && $history[0]['to']==='' && !$history[0]['observed'],'true delisting retains previous classification');
VtAvastStore::live($domain,147,true);
VtSiteLog::avastResult($domain,'AVG','URL:Old','',$ts-10);
check(count(VtSiteLog::history($domain))===3,'unchanged and stale results do not duplicate');
$feed=VtSiteLog::recentChanges();
check($feed[0]['domain']===$domain && $feed[0]['product']==='AVG' && $feed[0]['type']==='av','feed retains domain and independent source');
// Upgrade with an old flagged cache must preserve the observed removal.
$other='other.example';
file_put_contents(VtDataDir::path('vt_avast.json'),json_encode([$other=>[147=>response($ts,[['classification'=>'URL:Phishing']])]]));
VtAvast::$next=response($ts+40); VtAvastStore::live($other,147,true);
$otherHistory=VtSiteLog::history($other);
check(count($otherHistory)===2 && $otherHistory[0]['from']==='URL:Phishing','upgrade preserves old cached evidence');
echo "AVG event regression checks passed\\n";
''')
            subprocess.run(['php', str(test)], check=True)


if __name__ == '__main__':
    unittest.main()
