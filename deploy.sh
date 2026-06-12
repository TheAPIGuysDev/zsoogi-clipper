#!/bin/bash
# Deploy Zsoogi Clipper to a remote site.
#
# Builds the plugin via build.sh (single source of truth for what ships),
# then rsyncs/mirrors the resulting build/zsoogi-clipper/ folder to the server.
#
# Usage:
#   ./deploy.sh         — SSH/rsync deploy (default)
#   ./deploy.sh sftp    — SFTP deploy (lftp)

set -e

if [ ! -f .env ]; then
    echo "Error: .env file not found in $(pwd)"
    exit 1
fi

# Load .env without exporting comments or blank lines.
set -a
# shellcheck disable=SC1091
source .env
set +a

# Canonical plugin slug — must match build.sh.
PLUGIN_SLUG="zsoogi-clipper"

# Colors.
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Argument parsing.
DEPLOY_MODE="ssh"
for arg in "$@"; do
    case "$arg" in
        ssh|sftp) DEPLOY_MODE="$arg" ;;
        *)
            echo -e "${RED}Unknown argument: $arg${NC}"
            echo "Usage: ./deploy.sh [ssh|sftp]"
            exit 1
            ;;
    esac
done

echo -e "${GREEN}=== Zsoogi Clipper Deployment ===${NC}"
echo -e "Mode: ${YELLOW}${DEPLOY_MODE}${NC}"

# Validate the configured target path ends in the canonical plugin slug.
# Guards against an .env tail segment landing the deploy in a sibling folder.
if [ "$DEPLOY_MODE" = "ssh" ]; then
    if [ -z "$TAG_SSH_PATH" ]; then
        echo -e "${RED}Error: TAG_SSH_PATH is not set in .env${NC}"
        exit 1
    fi
    if [ "$(basename "$TAG_SSH_PATH")" != "$PLUGIN_SLUG" ]; then
        echo -e "${RED}Error: TAG_SSH_PATH must end in /${PLUGIN_SLUG}${NC}"
        echo -e "       Current value tail: $(basename "$TAG_SSH_PATH")"
        echo -e "       Fix .env then retry."
        exit 1
    fi
fi

# Build first. KEEP_BUILD=1 leaves build/zsoogi-clipper/ on disk for rsync.
echo -e "${YELLOW}Building...${NC}"
KEEP_BUILD=1 ./build.sh
echo ""

deploy_ssh() {
    if [ ! -f "$TAG_SSH_PRIVATE_KEY" ]; then
        echo -e "${RED}Error: SSH private key not found at $TAG_SSH_PRIVATE_KEY${NC}"
        exit 1
    fi

    local ssh_cmd="ssh -i $TAG_SSH_PRIVATE_KEY -p ${TAG_SSH_PORT:-22}"

    echo -e "${YELLOW}Rsyncing build/${PLUGIN_SLUG}/ → ${TAG_SSH_HOST}:${TAG_SSH_PATH}/${NC}"
    /usr/local/bin/rsync -avz --delete \
        -e "$ssh_cmd" \
        "build/${PLUGIN_SLUG}/" \
        "${TAG_SSH_USER}@${TAG_SSH_HOST}:${TAG_SSH_PATH}/"

    echo -e "${GREEN}✓ Files synced${NC}"
}

deploy_sftp() {
    if [ -z "$TAG_SFTP_PATH" ]; then
        echo -e "${RED}Error: TAG_SFTP_PATH is not set in .env${NC}"
        exit 1
    fi
    if [ "$(basename "$TAG_SFTP_PATH")" != "$PLUGIN_SLUG" ]; then
        echo -e "${RED}Error: TAG_SFTP_PATH must end in /${PLUGIN_SLUG}${NC}"
        exit 1
    fi

    echo -e "${YELLOW}lftp mirror → ${TAG_SFTP_HOST}:${TAG_SFTP_PATH}/${NC}"
    lftp -u "$TAG_SFTP_USER,$TAG_SFTP_PASSWORD" "sftp://$TAG_SFTP_HOST" <<EOF
mirror -R --delete --verbose "build/${PLUGIN_SLUG}/" "${TAG_SFTP_PATH}/"
bye
EOF
    echo -e "${GREEN}✓ Files uploaded${NC}"
}

# Run.
case "$DEPLOY_MODE" in
    ssh)  deploy_ssh  ;;
    sftp) deploy_sftp ;;
esac

# Clean up build staging.
rm -rf build

echo ""
echo -e "${GREEN}=== Deployment Complete ===${NC}"
