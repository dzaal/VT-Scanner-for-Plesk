#!/bin/bash
# Deploy VirusTotal Dashboard als Plesk extension op een Plesk-server
# Usage: VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh
set -e

SERVER="${VT_DEPLOY_HOST:?Set VT_DEPLOY_HOST, e.g. VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh}"
HOSTNAME_ONLY="${SERVER#*@}"
EXT_ID="stingyscan"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "→ Bouwen van extensie-pakket..."
ZIP_FILE="$SCRIPT_DIR/$(bash "$SCRIPT_DIR/build.sh")"

echo "→ Uploaden naar $SERVER..."
scp -q "$ZIP_FILE" "$SERVER:/tmp/${EXT_ID}.zip"

echo "→ Installeren als Plesk extension..."
ssh "$SERVER" "plesk bin extension --install /tmp/${EXT_ID}.zip && rm /tmp/${EXT_ID}.zip"

echo ""
echo "✓ Geïnstalleerd!"
echo "→ Zichtbaar in Plesk onder: Tools & Settings → Extensions → StingyScan by Digizaal"
echo "→ Direct URL: https://${HOSTNAME_ONLY}:8443/modules/stingyscan/"
