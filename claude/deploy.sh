#!/bin/bash

##
# Deploy MkDocs to WordPress
#
# This script builds the MkDocs site and deploys it to WordPress
##

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
DOCS_SOURCE="./site"

# Try to auto-detect WordPress content directory
# Option 1: ../../../zsoogi-clipper-docs (wp-content/zsoogi-clipper-docs)
# Option 2: ../../../plugins/zsoogi-clipper-docs (wp-content/plugins/zsoogi-clipper-docs)
if [ -d "../../../plugins" ]; then
    # We're in wp-content/plugins/zsoogi-clipper/claude
    WP_DOCS_DIR="../../../zsoogi-clipper-docs"
elif [ -d "../../plugins" ]; then
    # Alternative structure
    WP_DOCS_DIR="../../zsoogi-clipper-docs"
else
    # Default fallback
    WP_DOCS_DIR="../../../zsoogi-clipper-docs"
fi

# Allow override via environment variable
if [ ! -z "$ZSOOGI_DOCS_DIR" ]; then
    WP_DOCS_DIR="$ZSOOGI_DOCS_DIR"
fi

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Zsoogi Clipper Docs Deployment${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Source: $DOCS_SOURCE"
echo "Target: $WP_DOCS_DIR"
echo "Absolute target: $(cd "$(dirname "$WP_DOCS_DIR")" 2>/dev/null && pwd)/$(basename "$WP_DOCS_DIR")"
echo ""

# Step 1: Build MkDocs
echo -e "${YELLOW}Step 1: Building MkDocs site...${NC}"
if mkdocs build --clean; then
    echo -e "${GREEN}✓ Build successful${NC}"
else
    echo -e "${RED}✗ Build failed${NC}"
    exit 1
fi
echo ""

# Step 2: Create WordPress docs directory if it doesn't exist
echo -e "${YELLOW}Step 2: Preparing WordPress directory...${NC}"
if [ ! -d "$WP_DOCS_DIR" ]; then
    echo "Creating directory: $WP_DOCS_DIR"
    mkdir -p "$WP_DOCS_DIR"
fi
echo -e "${GREEN}✓ Directory ready${NC}"
echo ""

# Step 3: Copy files
echo -e "${YELLOW}Step 3: Copying files to WordPress...${NC}"
if rsync -av --delete "$DOCS_SOURCE/" "$WP_DOCS_DIR/"; then
    echo -e "${GREEN}✓ Files copied successfully${NC}"
else
    echo -e "${RED}✗ Copy failed${NC}"
    exit 1
fi
echo ""

# Step 4: Set permissions (optional, uncomment if needed)
# echo -e "${YELLOW}Step 4: Setting permissions...${NC}"
# chmod -R 755 "$WP_DOCS_DIR"
# echo -e "${GREEN}✓ Permissions set${NC}"
# echo ""

# Summary
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Documentation deployed to:"
echo "  → $WP_DOCS_DIR"
echo ""
echo "Access your docs at:"
echo "  → https://sites.theapiguys.com/zsoogi-clipper/"
echo ""
echo -e "${YELLOW}Note:${NC} Make sure the Zsoogi Docs Server plugin is activated"
echo ""
