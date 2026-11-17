#!/bin/bash
# Deploy VirusTotal Dashboard als Plesk extension op een Plesk-server
# Usage: VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh
set -e

SERVER="${VT_DEPLOY_HOST:?Set VT_DEPLOY_HOST, e.g. VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh}"
HOSTNAME_ONLY="${SERVER#*@}"
EXT_ID="virustotal-extension"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "→ Bouwen van extensie-pakket..."
ZIP_FILE="$SCRIPT_DIR/$(bash "$SCRIPT_DIR/build.sh")"

echo "→ Uploaden naar $SERVER..."
scp -q "$ZIP_FILE" "$SERVER:/tmp/${EXT_ID}.zip"

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
echo "De nachtelijke scan wordt automatisch ingepland via de Plesk scheduler (03:00)."
