#!/bin/bash
# Build script for Zsoogi Clipper plugin.
#
# Produces a production-ready folder at build/zsoogi-clipper/ and a
# distributable ZIP at dist/zsoogi-clipper-${VERSION}.zip.
#
# Usage:
#   ./build.sh                — build, zip, clean up build/
#   KEEP_BUILD=1 ./build.sh   — build, zip, leave build/ intact (used by deploy.sh)

set -e

# Plugin canonical folder name and main file. Used by deploy.sh too.
PLUGIN_SLUG="zsoogi-clipper"
PLUGIN_FILE="${PLUGIN_SLUG}.php"

# Extract version from plugin header.
VERSION=$(grep 'Version:' "${PLUGIN_FILE}" | head -1 | sed 's/.*Version:[[:space:]]*\([0-9.]*\).*/\1/')

if [ -z "$VERSION" ]; then
    echo "Error: Could not extract version from ${PLUGIN_FILE}"
    exit 1
fi

echo "Building ${PLUGIN_SLUG} v${VERSION}..."

# Clean previous outputs.
rm -rf build
rm -f "dist/${PLUGIN_SLUG}-"*.zip

mkdir -p "build/${PLUGIN_SLUG}"
mkdir -p dist

# Single source of truth for what ships to production.
# Anything not listed here lands in the build folder and the ZIP.
# WP.org listing assets (banner/icon/screenshots) live in the SVN /assets/
# directory, not the plugin trunk — excluded here so they don't bloat the ZIP.
rsync -av \
    --exclude='.git/' \
    --exclude='.gitignore' \
    --exclude='.gitattributes' \
    --exclude='.DS_Store' \
    --exclude='.env*' \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='build/' \
    --exclude='dist/' \
    --exclude='docs/' \
    --exclude='site/' \
    --exclude='samples/' \
    --exclude='__pycache__/' \
    --exclude='mkdocs.yml' \
    --exclude='hooks.py' \
    --exclude='mkdocs-*.sh' \
    --exclude='.claude/' \
    --exclude='CLAUDE.md' \
    --exclude='README.md' \
    --exclude='composer.json' \
    --exclude='composer.lock' \
    --exclude='phpcs.xml*' \
    --exclude='build.sh' \
    --exclude='deploy.sh' \
    --exclude='*.zip' \
    --exclude='assets/cpt-svg-icon.md' \
    --exclude='assets/screenshot-*.png' \
    --exclude='assets/banner-*.png' \
    --exclude='assets/icon-*.png' \
    --exclude='assets/screencasts/' \
    --exclude='assets/images/screencasts/' \
    . "build/${PLUGIN_SLUG}/"

# Zip from inside build/ so the archive has the plugin slug as its top-level dir.
( cd build && zip -qr "../dist/${PLUGIN_SLUG}-${VERSION}.zip" "${PLUGIN_SLUG}" )

echo "ZIP:   dist/${PLUGIN_SLUG}-${VERSION}.zip"

# Tidy up unless the caller (e.g. deploy.sh) wants to consume build/.
if [ "${KEEP_BUILD:-0}" != "1" ]; then
    rm -rf build
else
    echo "Build: build/${PLUGIN_SLUG}/ (KEEP_BUILD=1, left in place)"
fi

echo "Done. v${VERSION}"
