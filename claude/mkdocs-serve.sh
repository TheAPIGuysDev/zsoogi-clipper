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

# Ensure watchdog is installed — required for live reload on macOS.
# Without it mkdocs has no file system event listener and won't detect changes.
MKDOCS_PYTHON="$(dirname "$(command -v mkdocs)")/python3"
if [ -x "$MKDOCS_PYTHON" ]; then
    "$MKDOCS_PYTHON" -m pip install --quiet watchdog 2>/dev/null || true
fi

mkdocs serve --dev-addr="127.0.0.1:$PORT"
