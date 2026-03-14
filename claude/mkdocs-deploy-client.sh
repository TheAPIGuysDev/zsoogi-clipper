#!/bin/bash
# mkdocs-deploy-client.sh
# Build and deploy the mkdocs site to the client staging server (AWS) via rsync.
# Served at https://vangeek.life/mkdocs/site.
#
# Usage (run from project root or claude/ directory):
#   ./claude/mkdocs-deploy-client.sh           # build + deploy
#   ./claude/mkdocs-deploy-client.sh --build   # build only, no deploy
#   ./claude/mkdocs-deploy-client.sh --deploy  # deploy existing site/, skip build
#
# Required env vars (in root .env):
#   DOCS_CLIENT_SSH_HOST         SSH host alias (from ~/.ssh/config)
#   DOCS_CLIENT_SSH_USER         remote SSH user
#   DOCS_CLIENT_SSH_PRIVATE_KEY  path to local private key
#   DOCS_CLIENT_SSH_PATH         remote path (storage/app/mkdocs inside the Laravel app)

set -e

# ── Paths ────────────────────────────────────────────────────────────────────

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
CLAUDE_DIR="$SCRIPT_DIR"
SITE_DIR="$CLAUDE_DIR/site"

# ── Colors ───────────────────────────────────────────────────────────────────

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# ── Load env ─────────────────────────────────────────────────────────────────

ENV_FILE="$PROJECT_ROOT/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo -e "${RED}Error: .env not found at $ENV_FILE${NC}"
    exit 1
fi

_env_tmp=$(mktemp)
grep -E '^[A-Za-z_][A-Za-z0-9_]*=' "$ENV_FILE" > "$_env_tmp"
set -a
# shellcheck disable=SC1090
source "$_env_tmp"
set +a
rm -f "$_env_tmp"

# ── Validate required vars ───────────────────────────────────────────────────

MISSING=()
[ -z "$DOCS_CLIENT_SSH_HOST" ]        && MISSING+=("DOCS_CLIENT_SSH_HOST")
[ -z "$DOCS_CLIENT_SSH_USER" ]        && MISSING+=("DOCS_CLIENT_SSH_USER")
[ -z "$DOCS_CLIENT_SSH_PRIVATE_KEY" ] && MISSING+=("DOCS_CLIENT_SSH_PRIVATE_KEY")
[ -z "$DOCS_CLIENT_SSH_PATH" ]        && MISSING+=("DOCS_CLIENT_SSH_PATH")

if [ ${#MISSING[@]} -gt 0 ]; then
    echo -e "${RED}Error: missing required env vars: ${MISSING[*]}${NC}"
    exit 1
fi

if [ ! -f "$DOCS_CLIENT_SSH_PRIVATE_KEY" ]; then
    echo -e "${RED}Error: SSH key not found at $DOCS_CLIENT_SSH_PRIVATE_KEY${NC}"
    exit 1
fi

SSH_PORT="${DOCS_CLIENT_SSH_PORT:-22}"

# ── Parse arguments ──────────────────────────────────────────────────────────

DO_BUILD=true
DO_DEPLOY=true

case "${1:-}" in
    --build)  DO_DEPLOY=false ;;
    --deploy) DO_BUILD=false  ;;
    "")       ;;
    *)
        echo -e "${RED}Unknown argument: $1${NC}"
        echo "Usage: $0 [--build|--deploy]"
        exit 1
        ;;
esac

# ── Header ───────────────────────────────────────────────────────────────────

echo -e "${GREEN}=== MkDocs Client Deploy ===${NC}"
echo -e "  Source : ${YELLOW}$SITE_DIR${NC}"
echo -e "  Remote : ${YELLOW}${DOCS_CLIENT_SSH_USER}@${DOCS_CLIENT_SSH_HOST}:${DOCS_CLIENT_SSH_PATH}${NC}"
echo -e "  URL    : ${YELLOW}https://staging.diligentdealers.net/mkdocs/${NC}"
echo -e "  Mode   : ${YELLOW}$([ "$DO_BUILD" = true ] && echo "build + " || echo "")$([ "$DO_DEPLOY" = true ] && echo "deploy" || echo "build only")${NC}"
echo ""

# ── Build ────────────────────────────────────────────────────────────────────

if [ "$DO_BUILD" = true ]; then
    echo -e "${YELLOW}Fixing markdown...${NC}"
    python3 ~/.claude/scripts/fix-markdown.py "$CLAUDE_DIR/docs"
    echo ""

    echo -e "${YELLOW}Building mkdocs site...${NC}"

    cd "$CLAUDE_DIR"
    mkdocs build --clean

    if [ ! -d "$SITE_DIR" ]; then
        echo -e "${RED}Error: build succeeded but site/ directory not found${NC}"
        exit 1
    fi

    echo -e "${GREEN}✓ Build complete → $SITE_DIR${NC}"
    echo ""
fi

# ── Deploy ───────────────────────────────────────────────────────────────────

if [ "$DO_DEPLOY" = true ]; then
    if [ ! -d "$SITE_DIR" ]; then
        echo -e "${RED}Error: site/ directory not found — run without --deploy to build first${NC}"
        exit 1
    fi

    echo -e "${YELLOW}Deploying to ${DOCS_CLIENT_SSH_HOST}...${NC}"

    # SSH config handles ProxyJump for diligent-staging automatically
    SSH_CMD="ssh -i $DOCS_CLIENT_SSH_PRIVATE_KEY -p $SSH_PORT -o StrictHostKeyChecking=no"

    RSYNC=$(command -v /usr/local/bin/rsync || command -v /opt/homebrew/bin/rsync || command -v rsync)

    "$RSYNC" -avz --delete \
        --exclude='.DS_Store' \
        -e "$SSH_CMD" \
        "$SITE_DIR/" \
        "${DOCS_CLIENT_SSH_USER}@${DOCS_CLIENT_SSH_HOST}:${DOCS_CLIENT_SSH_PATH}/"

    echo -e "${GREEN}✓ Deployed to ${DOCS_CLIENT_SSH_USER}@${DOCS_CLIENT_SSH_HOST}:${DOCS_CLIENT_SSH_PATH}${NC}"
fi

echo ""
echo -e "${GREEN}=== ${DOCS_CLIENT_SSH_PATH} ===${NC}"
echo -e "${GREEN}=== Done ===${NC}"
