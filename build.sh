#!/bin/bash
# Build script for Zsoogi Clipper plugin

# Extract version from plugin file
VERSION=$(grep 'Version:' zsoogi-clipper.php | head -1 | sed 's/.*Version:[[:space:]]*\([0-9.]*\).*/\1/')

if [ -z "$VERSION" ]; then
    echo "Error: Could not extract version from zsoogi-clipper.php"
    exit 1
fi

echo "Building Zsoogi Clipper v${VERSION}..."

# Clean up previous builds
rm -rf build
rm -f dist/zsoogi-clipper-*.zip

# Create build directories
mkdir -p build/zsoogi-clipper
mkdir -p dist

# Copy files to build directory, excluding development files
rsync -av \
    --exclude='vendor/' \
    --exclude='.git/' \
    --exclude='node_modules/' \
    --exclude='build/' \
    --exclude='dist/' \
    --exclude='.claude/' \
    --exclude='.env' \
    --exclude='*.env' \
    --exclude='composer.json' \
    --exclude='composer.lock' \
    --exclude='phpcs.xml*' \
    --exclude='.gitignore' \
    --exclude='.DS_Store' \
    --exclude='.gitattributes' \
    --exclude='CLAUDE.md' \
    --exclude='build.sh' \
    --exclude='deploy.sh' \
    --exclude='README.md' \
    --exclude='claude/' \
    --exclude='assets/cpt-svg-icon.md' \
    --exclude='assets/screenshot-*.png' \
    --exclude='assets/banner-*.png' \
    --exclude='assets/icon-*.png' \
    --exclude='assets/screencasts/' \
    --exclude='assets/images/screencasts/' \
    --exclude='*.zip' \
    . build/zsoogi-clipper/

# Create the ZIP file
cd build
zip -r "../dist/zsoogi-clipper-${VERSION}.zip" zsoogi-clipper
cd ..

# Clean up build directory
rm -rf build

echo "Build complete: dist/zsoogi-clipper-${VERSION}.zip"
