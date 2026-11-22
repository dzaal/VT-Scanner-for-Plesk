#!/usr/bin/env bash
# Print the GitHub release notes for one version, taken from CHANGELOG.md:
# install instructions, the changes in that version, and all earlier versions.
# Usage: bash .github/release-notes.sh 1.15.6
set -euo pipefail
v="$1"
repo="${GITHUB_REPOSITORY:-dzaal/StingyScan-for-Plesk}"
changelog="$(dirname "$0")/../CHANGELOG.md"

# The section of this version (without its heading), and everything older.
this=$(awk -v v="$v" '/^## /{f=($2==v)} f && !/^## /' "$changelog")
older=$(awk -v v="$v" '/^## /{if(f)o=1; f=($2==v)} o' "$changelog")

cat <<NOTES
## What's new in $v
${this:-No notes for this version.}

## Install or update
On your Plesk server, as root:

\`\`\`
plesk bin extension --upgrade-url https://github.com/$repo/releases/latest/download/stingyscan.zip
\`\`\`

Or download \`stingyscan-$v.zip\` below and upload it in Plesk (Extensions → My Extensions → Upload Extension).

NOTES

if [ -n "$older" ]; then
  cat <<NOTES
## Earlier versions
<details>
<summary>Show all earlier versions</summary>

$older

</details>

NOTES
fi

echo "Full history: [CHANGELOG.md](https://github.com/$repo/blob/main/CHANGELOG.md)"
