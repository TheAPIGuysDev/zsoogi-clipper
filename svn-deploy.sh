#!/bin/bash
# Deploy Zsoogi Clipper to the WordPress.org plugin directory via SVN.
#
# Mirrors build/zsoogi-clipper/ into the SVN working copy's trunk/, copies the
# WP.org listing assets (banner/icon/screenshots) into assets/, and optionally
# tags the release. Stops short of committing unless --commit is passed.
#
# Usage:
#   ./svn-deploy.sh                  — sync trunk + assets, show what would commit
#   ./svn-deploy.sh --tag            — also create tags/${VERSION} from trunk
#   ./svn-deploy.sh --tag --commit   — sync, tag, and commit (prompts for password)
#
# Config (optional, in .env):
#   WP_SVN_DIR=~/svn/zsoogi-clipper
#   WP_SVN_USER=pbarthmaier

set -e

PLUGIN_SLUG="zsoogi-clipper"
PLUGIN_FILE="${PLUGIN_SLUG}.php"
SVN_URL="https://plugins.svn.wordpress.org/${PLUGIN_SLUG}"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Load .env if present, for WP_SVN_DIR / WP_SVN_USER.
if [ -f .env ]; then
    set -a
    # shellcheck disable=SC1091
    source .env
    set +a
fi

SVN_DIR="${WP_SVN_DIR:-$HOME/svn/${PLUGIN_SLUG}}"
SVN_DIR="${SVN_DIR/#\~/$HOME}"
SVN_USER="${WP_SVN_USER:-pbarthmaier}"

# Auth: prefer WP_SVN_PASSWORD from .env so releases run unattended, otherwise
# prompt. --force-interactive is needed because stdin is not always a TTY.
# Note: a --password value is briefly visible in `ps` output to other local
# users. After one successful auth svn caches the credential in the macOS
# Keychain, so you can drop WP_SVN_PASSWORD and rely on --non-interactive.
svn_auth=( --username "${SVN_USER}" )
if [ -n "${WP_SVN_PASSWORD:-}" ]; then
    svn_auth+=( --password "${WP_SVN_PASSWORD}" --non-interactive )
else
    svn_auth+=( --force-interactive )
fi

DO_TAG=0
DO_COMMIT=0
for arg in "$@"; do
    case "$arg" in
        --tag)    DO_TAG=1 ;;
        --commit) DO_COMMIT=1 ;;
        *)
            echo -e "${RED}Unknown argument: $arg${NC}"
            echo "Usage: ./svn-deploy.sh [--tag] [--commit]"
            exit 1
            ;;
    esac
done

command -v svn >/dev/null 2>&1 || {
    echo -e "${RED}Error: svn not installed. Run: brew install subversion${NC}"
    exit 1
}

VERSION=$(grep 'Version:' "${PLUGIN_FILE}" | head -1 | sed 's/.*Version:[[:space:]]*\([0-9.]*\).*/\1/')
STABLE=$(grep -i '^Stable tag:' readme.txt | head -1 | sed 's/.*:[[:space:]]*//' | tr -d '[:space:]')

echo -e "${GREEN}=== Zsoogi Clipper → WordPress.org ===${NC}"
echo -e "Version:    ${YELLOW}${VERSION}${NC}"
echo -e "Stable tag: ${YELLOW}${STABLE}${NC}"
echo -e "SVN dir:    ${YELLOW}${SVN_DIR}${NC}"
echo ""

# The readme's Stable tag is what WP.org actually serves to users. If it does
# not match the version being shipped, the release silently won't go live.
if [ "$VERSION" != "$STABLE" ]; then
    echo -e "${RED}Error: plugin header Version (${VERSION}) != readme Stable tag (${STABLE})${NC}"
    echo -e "       WP.org serves whatever Stable tag points at. Align them first."
    exit 1
fi

# Check out on first run, otherwise update.
if [ ! -d "${SVN_DIR}/.svn" ]; then
    echo -e "${YELLOW}Checking out ${SVN_URL}...${NC}"
    mkdir -p "$(dirname "$SVN_DIR")"
    svn checkout "${SVN_URL}" "${SVN_DIR}" "${svn_auth[@]}"
else
    echo -e "${YELLOW}Updating working copy...${NC}"
    svn update "${SVN_DIR}"
fi

mkdir -p "${SVN_DIR}/trunk" "${SVN_DIR}/assets" "${SVN_DIR}/tags"

# Build first. KEEP_BUILD=1 leaves build/zsoogi-clipper/ on disk to mirror.
echo -e "${YELLOW}Building...${NC}"
KEEP_BUILD=1 ./build.sh
echo ""

# Mirror the built plugin into trunk. --delete so removed files leave trunk too.
echo -e "${YELLOW}Syncing trunk/...${NC}"
rsync -a --delete --exclude='.svn/' "build/${PLUGIN_SLUG}/" "${SVN_DIR}/trunk/"

# WP.org listing assets live in the repo root assets/, not in trunk.
echo -e "${YELLOW}Syncing assets/...${NC}"
# Only the listing images, and only the ones that actually exist — an unmatched
# glob would otherwise be passed to rsync literally and abort the run.
shopt -s nullglob
listing_assets=( assets/banner-*.png assets/icon-*.png assets/screenshot-*.png )
shopt -u nullglob
if [ ${#listing_assets[@]} -eq 0 ]; then
    echo -e "${RED}Error: no banner/icon/screenshot files found in assets/${NC}"
    exit 1
fi
rsync -a --exclude='.svn/' "${listing_assets[@]}" "${SVN_DIR}/assets/"

rm -rf build

# Stage adds and deletes. The trailing @ guards filenames containing '@'.
cd "${SVN_DIR}"
svn status | grep '^!' | sed 's/^![[:space:]]*//' | while read -r f; do svn rm "${f}@"; done
svn status | grep '^?' | sed 's/^?[[:space:]]*//' | while read -r f; do svn add "${f}@"; done

if [ "$DO_TAG" = "1" ]; then
    if [ -e "tags/${VERSION}" ]; then
        echo -e "${YELLOW}tags/${VERSION} already exists — skipping tag.${NC}"
    else
        echo -e "${YELLOW}Tagging tags/${VERSION}...${NC}"
        svn copy trunk "tags/${VERSION}"
    fi
fi

echo ""
echo -e "${GREEN}=== Pending changes ===${NC}"
svn status
echo ""

if [ "$DO_COMMIT" = "1" ]; then
    svn commit -m "Release ${VERSION}" "${svn_auth[@]}"
    echo -e "${GREEN}✓ Committed ${VERSION}${NC}"
else
    echo -e "${YELLOW}Nothing committed. To publish:${NC}"
    echo "  ./svn-deploy.sh --tag --commit"
fi
