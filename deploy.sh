#!/bin/bash
# Deploy zsoogi-clipper plugin to Production
# Usage: ./deploy.sh [ssh|sftp]
#   No argument: Deploy via SSH (rsync)
#   sftp:        Deploy via SFTP (lftp)

# Load environment variables from .env file
if [ ! -f .env ]; then
    echo "Error: .env file not found"
    exit 1
fi

export $(grep -v '^#' .env | xargs)

# Plugin directory name
PLUGIN_DIR="zsoogi-clipper"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

printf "${YELLOW}Starting deployment to Production...${NC}\n"

# Check if SSH key exists
if [ ! -f "$TAG_SSH_PRIVATE_KEY" ]; then
    printf "${RED}Error: SSH private key not found at $TAG_SSH_PRIVATE_KEY${NC}\n"
    exit 1
fi

# Deployment mode: ssh or sftp (default: ssh)
DEPLOY_MODE=${1:-ssh}

# Extract plugin version from main file
PLUGIN_VERSION=$(grep -i "Version:" zsoogi-clipper.php | head -1 | awk '{print $3}')

printf "${GREEN}=== Plugin Deployment ===${NC}\n"
printf "Plugin Version: ${YELLOW}${PLUGIN_VERSION}${NC}\n"
printf "Mode: ${YELLOW}${DEPLOY_MODE}${NC}\n"
printf "\n"

# Function to deploy via SSH/rsync
deploy_ssh() {
    printf "${GREEN}Deploying via SSH (rsync)...${NC}\n"

    # Check if SSH key exists
    if [ ! -f "$TAG_SSH_PRIVATE_KEY" ]; then
        printf "${RED}Error: SSH key not found at $TAG_SSH_PRIVATE_KEY${NC}\n"
        exit 1
    fi

    # Build SSH command with key
    SSH_CMD="ssh -i $TAG_SSH_PRIVATE_KEY -p ${TAG_SSH_PORT:-22}"

    # Execute rsync (prefer Homebrew rsync for protocol compatibility)
    RSYNC=$(command -v /usr/local/bin/rsync || command -v /opt/homebrew/bin/rsync || command -v rsync)
    printf "${YELLOW}Syncing files to $TAG_SSH_HOST...${NC}\n"
    "$RSYNC" -avz --delete --delete-excluded \
        --exclude='.git' \
        --exclude='.env' \
        --exclude='.env.bak' \
        --exclude='node_modules' \
        --exclude='.DS_Store' \
        --exclude='deploy.sh' \
        --exclude='deploy2.sh' \
        --exclude='*.zip' \
        --exclude='.claude' \
        --exclude='composer.lock' \
        --exclude='vendor' \
        --exclude='phpcs.xml.dist' \
        --exclude='claude/site' \
        --exclude='claude/__pycache__' \
        --exclude='claude/samples' \
        --exclude='assets/*.json' \
        -e "$SSH_CMD" \
        ./ \
        "${TAG_SSH_USER}@${TAG_SSH_HOST}:${TAG_SSH_PATH}/"

    if [ $? -eq 0 ]; then
        printf "${GREEN}✓ Deployment successful via SSH${NC}\n"
    else
        printf "${RED}✗ Deployment failed${NC}\n"
        exit 1
    fi

    # Regenerate physical PWA static files (manifest.json + sw.js) at document root.
    # Required because SiteGround nginx intercepts requests before WordPress rewrite rules run.
    printf "${YELLOW}Regenerating PWA static files...${NC}\n"
    WP_PATH=$(dirname "${TAG_SSH_PATH}")
    WP_PATH=$(dirname "${WP_PATH}")
    ssh -i "$TAG_SSH_PRIVATE_KEY" -p "${TAG_SSH_PORT:-22}" \
        "${TAG_SSH_USER}@${TAG_SSH_HOST}" \
        "wp zsoogi pwa-files --path=${WP_PATH} 2>&1" && \
        printf "${GREEN}✓ PWA files regenerated${NC}\n" || \
        printf "${YELLOW}⚠ PWA file regeneration failed (run manually: wp zsoogi pwa-files)${NC}\n"
}

# Function to deploy via SFTP
deploy_sftp() {
    printf "${GREEN}Deploying via SFTP...${NC}\n"

    # Create a temporary batch file for SFTP commands
    BATCH_FILE=$(mktemp)

    cat > $BATCH_FILE << EOF
cd $TAG_SFTP_PATH
put -r includes
put -r assets
put zsoogi-clipper.php
put uninstall.php
put composer.json
put README.md
put CLAUDE.md
quit
EOF

    printf "${YELLOW}Uploading files to $TAG_SFTP_HOST...${NC}\n"

    # Execute SFTP with batch file
    sshpass -p "$TAG_SFTP_PASSWORD" sftp -b $BATCH_FILE "${TAG_SFTP_USER}@${TAG_SFTP_HOST}"

    # Clean up
    rm -f $BATCH_FILE

    if [ $? -eq 0 ]; then
        printf "${GREEN}✓ Deployment successful via SFTP${NC}\n"
    else
        printf "${RED}✗ Deployment failed${NC}\n"
        exit 1
    fi
}

# Main deployment logic
case $DEPLOY_MODE in
    ssh)
        deploy_ssh
        ;;
    sftp)
        deploy_sftp
        ;;
    *)
        printf "${RED}Invalid deployment mode: $DEPLOY_MODE${NC}\n"
        echo "Usage: ./deploy.sh [ssh|sftp]"
        exit 1
        ;;
esac

printf "\n"
printf "${GREEN}=== Deployment Complete ===${NC}\n"
printf "Plugin Version: ${YELLOW}${PLUGIN_VERSION}${NC}\n"

if [ "$DEPLOY_MODE" = "ssh" ]; then
    printf "Plugin deployed to: ${YELLOW}${TAG_SSH_PATH}${NC}\n"
else
    printf "Plugin deployed to: ${YELLOW}${TAG_SFTP_PATH}${NC}\n"
fi
