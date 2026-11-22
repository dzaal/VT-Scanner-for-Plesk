"""Exercise the detached PHP update worker without Plesk or root privileges."""
import json
from pathlib import Path
import shutil
import subprocess
import tempfile
import time
import unittest

ROOT = Path(__file__).resolve().parents[1]


class UpdateWorkerTest(unittest.TestCase):
    def run_update(self, exit_code):
        with tempfile.TemporaryDirectory() as folder:
            base = Path(folder)
            stub = base / 'plesk'
            stub.write_text(f'#!/bin/sh\necho "Plesk test result"\nexit {exit_code}\n')
            stub.chmod(0o755)
            helper = base / 'extension-update'
            helper.write_text((ROOT / 'sbin/extension-update').read_text()
                              .replace('/run/stingyscan-update', str(base / 'state'))
                              .replace('/usr/sbin/plesk', str(stub)))
            php = shutil.which('php')
            def call(*args):
                return json.loads(subprocess.check_output([php, str(helper), *args], text=True))
            self.assertIn('error', call('start', '1.2.3;id'))
            self.assertEqual({'ok': True}, call('start', '1.22.2'))
            for _ in range(100):
                result = call('status')
                if result['state'] in ('done', 'failed'):
                    break
                time.sleep(.05)
            self.assertEqual('done' if exit_code == 0 else 'failed', result['state'])
            self.assertEqual('1.22.2', result['version'])
            if exit_code:
                self.assertIn('Plesk test result', result['message'])

    def test_success(self):
        self.run_update(0)

    def test_failure(self):
        self.run_update(1)


if __name__ == '__main__':
    unittest.main()
