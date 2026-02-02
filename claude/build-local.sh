#!/bin/bash

##
# Build MkDocs and copy to plugin directory
# This serves docs directly from the plugin - no separate upload needed!
##

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}Building MkDocs for plugin...${NC}"

# Build
mkdocs build --clean

# Copy to plugin directory
echo -e "${YELLOW}Copying to plugin directory...${NC}"
rm -rf docs-built
mkdir -p docs-built
cp -r site/* docs-built/

echo -e "${GREEN}✓ Done!${NC}"
echo ""
echo "Docs are now in: ./docs-built/"
echo "Just commit the plugin and the docs are included!"
echo ""
echo "Access at: https://sites.theapiguys.com/zsoogi-clipper/documentation/"
