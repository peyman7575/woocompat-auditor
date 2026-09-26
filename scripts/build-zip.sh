#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="$ROOT/dist"
BUILD_DIR="$(mktemp -d)"
TARGET_DIR="$BUILD_DIR/woocompat-auditor"

cleanup() {
  rm -rf "$BUILD_DIR"
}
trap cleanup EXIT

php "$ROOT/scripts/check-version.php" >/dev/null

VERSION="$(
  php -r '
    $contents = file_get_contents($argv[1]);
    if (!preg_match("/^ \\* Version:\\s*(\\S+)/m", $contents, $matches)) {
        exit(1);
    }
    echo $matches[1];
  ' "$ROOT/woocompat-auditor.php"
)"

if [[ -z "$VERSION" ]]; then
  echo "Unable to determine plugin version." >&2
  exit 1
fi

rm -rf "$DIST_DIR"
mkdir -p "$DIST_DIR" "$TARGET_DIR"

rsync -a "$ROOT/" "$TARGET_DIR/" \
  --exclude '.git/' \
  --exclude '.github/' \
  --exclude '.editorconfig' \
  --exclude 'composer.json' \
  --exclude 'composer.lock' \
  --exclude 'dist/' \
  --exclude 'phpcs.xml.dist' \
  --exclude 'phpunit.xml.dist' \
  --exclude 'scripts/' \
  --exclude 'tests/' \
  --exclude 'vendor/'

ZIP_FILE="$DIST_DIR/woocompat-auditor-$VERSION.zip"

(
  cd "$BUILD_DIR"
  zip -qr "$ZIP_FILE" woocompat-auditor
)

echo "$ZIP_FILE"
