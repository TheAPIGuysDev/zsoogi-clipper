#!/bin/bash
# Serve MkDocs docs locally with live-reload.
# Run from the project root or claude/ directory.
#
# Usage:
#   ./claude/mkdocs-serve.sh

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"
mkdocs serve
