#!/bin/bash
# Serve MkDocs docs locally with live-reload.
# Run from the project root or claude/ directory.
#
# Usage:
#   ./claude/mkdocs-serve.sh           # serves on port 8000
#   ./claude/mkdocs-serve.sh 8001      # serves on a custom port

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT="${1:-8000}"

# Kill any existing mkdocs process on the target port.
EXISTING_PID=$(lsof -ti :"$PORT" 2>/dev/null)
if [ -n "$EXISTING_PID" ]; then
    echo "Killing existing process on port $PORT (PID $EXISTING_PID)..."
    kill "$EXISTING_PID"
    sleep 1
fi

cd "$SCRIPT_DIR"
mkdocs serve --dev-addr="127.0.0.1:$PORT"
