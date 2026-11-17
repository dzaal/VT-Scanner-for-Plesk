#!/bin/bash
# Deploy VirusTotal Dashboard als Plesk extension op een Plesk-server
# Usage: VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh
set -e

SERVER="${VT_DEPLOY_HOST:?Set VT_DEPLOY_HOST, e.g. VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh}"
HOSTNAME_ONLY="${SERVER#*@}"
EXT_ID="virustotal-extension"
ZIP_FILE="/tmp/${EXT_ID}.zip"

echo "→ Bouwen van extensie-pakket..."
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR"

# Verwijder vorige zip en tijdelijke buildmap
rm -f "$ZIP_FILE"
rm -rf "/tmp/${EXT_ID}-build"

# Plesk 18 verwacht: zip → <ext-id>/ → meta.xml + rest (GEEN META_INF submap)
mkdir -p "/tmp/${EXT_ID}-build/${EXT_ID}"
cp -r htdocs plib "/tmp/${EXT_ID}-build/${EXT_ID}/"
# meta.xml plat in de root (Plesk zoekt <ext-id>/meta.xml, niet META_INF/)
cp META_INF/meta.xml "/tmp/${EXT_ID}-build/${EXT_ID}/meta.xml"

cd "/tmp/${EXT_ID}-build"
zip -r "$ZIP_FILE" "${EXT_ID}/" \
    --exclude "*.DS_Store" \
    > /dev/null
cd "$SCRIPT_DIR"
rm -rf "/tmp/${EXT_ID}-build"

echo "→ Uploaden naar $SERVER..."
scp -q "$ZIP_FILE" "$SERVER:/tmp/"

echo "→ Installeren als Plesk extension..."
ssh "$SERVER" "plesk bin extension --install /tmp/${EXT_ID}.zip && rm /tmp/${EXT_ID}.zip"

echo "→ Navigatieknop registreren..."
ssh "$SERVER" "
  # Remove existing VT Site Scanner nav buttons to avoid duplicates
  IDS=\$(/usr/local/psa/bin/custombutton --list 2>/dev/null | awk '/VT Site Scanner/{print \$1}')
  if [ -n \"\$IDS\" ]; then /usr/local/psa/bin/custombutton --remove \$IDS 2>/dev/null; fi
  # Create fresh navigation button
  /usr/local/psa/bin/custombutton --create \
    -owner admin \
    -place navigation \
    -text 'VT Site Scanner' \
    -url '/modules/virustotal-extension/' \
    -file '/usr/local/psa/admin/htdocs/modules/virustotal-extension/images/icon.png' \
    -internal true \
  -public true \
    2>&1
"

echo ""
echo "✓ Geïnstalleerd!"
echo "→ Zichtbaar in Plesk onder: Tools & Settings → Extensions → VT Site Scanner by Digizaal"
echo "→ Direct URL: https://${HOSTNAME_ONLY}:8443/modules/virustotal-extension/"
echo ""
echo "Cron instellen (eenmalig, als nog niet gedaan):"
echo "  ssh $SERVER"
echo "  crontab -e"
echo "  0 3 * * * php /usr/local/psa/admin/plib/modules/virustotal-extension/scripts/background_scan.php >> /usr/local/psa/var/modules/virustotal-extension/data/cron_admin.log 2>&1"
