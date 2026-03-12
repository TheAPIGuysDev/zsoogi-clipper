"""
MkDocs hook: inject plugin version into docs at build time.

Reads the version from the WordPress plugin header in zsoogi-clipper.php
and replaces {{ plugin_version }} wherever it appears in any page.
"""

import os
import re


def get_plugin_version():
    """Read the Version: line from the main plugin PHP file."""
    plugin_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    php_file = os.path.join(plugin_root, "zsoogi-clipper.php")
    with open(php_file, "r") as f:
        for line in f:
            m = re.search(r"\*\s*Version:\s*([\d.]+)", line)
            if m:
                return m.group(1)
    return "unknown"


PLUGIN_VERSION = get_plugin_version()


def on_page_markdown(markdown, **kwargs):
    """Replace {{ plugin_version }} in every page before rendering."""
    return markdown.replace("{{ plugin_version }}", PLUGIN_VERSION)
