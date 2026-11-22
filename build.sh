#!/bin/bash
# Build the installable Plesk extension package: dist/virustotal-extension-<version>.zip
# Usage: bash build.sh
set -e

EXT_ID="virustotal-extension"
cd "$(dirname "$0")"
VERSION=$(sed -n 's:.*<version>\(.*\)</version>.*:\1:p' META_INF/meta.xml)
OUT="dist/${EXT_ID}-${VERSION}.zip"
BUILD=$(mktemp -d)
trap 'rm -rf "$BUILD"' EXIT

# Package layout: <ext-id>/meta.xml + htdocs/ + plib/ + sbin/ (root helper) + _meta/ (catalog icons)
mkdir -p "$BUILD/$EXT_ID" dist
cp -r htdocs plib sbin _meta "$BUILD/$EXT_ID/"
chmod 755 "$BUILD/$EXT_ID"/sbin/*
cp META_INF/meta.xml "$BUILD/$EXT_ID/meta.xml"
cp LICENSE "$BUILD/$EXT_ID/LICENSE"
rm -f "$OUT"

if command -v zip >/dev/null 2>&1; then
    (cd "$BUILD" && zip -qr - "$EXT_ID" -x "*.DS_Store") > "$OUT"
else
    # Fallback for systems without zip (e.g. Git Bash on Windows)
    PY=$(command -v python3 || command -v python)
    "$PY" - "$BUILD" "$EXT_ID" "$OUT" <<'PYEOF'
import os, sys, zipfile
base, ext, out = sys.argv[1], sys.argv[2], sys.argv[3]
with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as z:
    for root, _, files in os.walk(os.path.join(base, ext)):
        for f in files:
            if f == ".DS_Store": continue
            p = os.path.join(root, f)
            arc = os.path.relpath(p, base).replace(os.sep, "/")
            zi = zipfile.ZipInfo.from_file(p, arc)
            zi.compress_type = zipfile.ZIP_DEFLATED
            # sbin/ scripts must stay executable (Windows has no exec bit)
            zi.external_attr = ((0o100755 if "/sbin/" in arc else 0o100644) << 16)
            with open(p, "rb") as fh:
                z.writestr(zi, fh.read())
PYEOF
fi

echo "$OUT"
