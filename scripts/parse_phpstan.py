# /// script
# requires-python = ">=3.10"
# dependencies = []
# ///
"""
Parse PHPStan JSON output and categorize errors for easier analysis.

Usage:
    uv run scripts/parse_phpstan.py phpstan-errors.json
    uv run scripts/parse_phpstan.py phpstan-errors.json --by-file
    uv run scripts/parse_phpstan.py phpstan-errors.json --by-identifier
"""

import json
import sys
from collections import defaultdict
from pathlib import Path


def parse_phpstan_json(filepath: str) -> dict:
    """Parse PHPStan JSON output, handling potential docker output prefix."""
    content = Path(filepath).read_text()

    # Find the start of JSON (in case there's docker output before it)
    json_start = content.find('{"totals"')
    if json_start == -1:
        # Try finding just the opening brace
        json_start = content.find('{')

    if json_start == -1:
        raise ValueError("Could not find JSON content in file")

    return json.loads(content[json_start:])


def categorize_by_identifier(data: dict) -> dict[str, list[dict]]:
    """Group errors by their identifier (error type)."""
    by_identifier = defaultdict(list)

    for filepath, file_data in data.get("files", {}).items():
        for msg in file_data.get("messages", []):
            identifier = msg.get("identifier", "unknown")
            by_identifier[identifier].append({
                "file": filepath.replace("/workspace/", ""),
                "line": msg.get("line"),
                "message": msg.get("message"),
                "tip": msg.get("tip"),
            })

    return dict(by_identifier)


def categorize_by_file(data: dict) -> dict[str, list[dict]]:
    """Group errors by file."""
    by_file = {}

    for filepath, file_data in data.get("files", {}).items():
        clean_path = filepath.replace("/workspace/", "")
        by_file[clean_path] = [
            {
                "line": msg.get("line"),
                "identifier": msg.get("identifier", "unknown"),
                "message": msg.get("message"),
            }
            for msg in file_data.get("messages", [])
        ]

    return by_file


def print_summary(data: dict):
    """Print a summary of errors."""
    totals = data.get("totals", {})
    print(f"Total errors: {totals.get('file_errors', 0)}")
    print()

    by_identifier = categorize_by_identifier(data)

    # Sort by count descending
    sorted_identifiers = sorted(
        by_identifier.items(),
        key=lambda x: len(x[1]),
        reverse=True
    )

    print("Errors by type:")
    print("-" * 60)
    for identifier, errors in sorted_identifiers:
        print(f"  {identifier}: {len(errors)}")
    print()


def print_by_identifier(data: dict, identifier: str | None = None):
    """Print errors grouped by identifier."""
    by_identifier = categorize_by_identifier(data)

    if identifier:
        # Filter to specific identifier
        if identifier in by_identifier:
            by_identifier = {identifier: by_identifier[identifier]}
        else:
            print(f"No errors with identifier: {identifier}")
            return

    for ident, errors in sorted(by_identifier.items()):
        print(f"\n{'=' * 60}")
        print(f"{ident} ({len(errors)} errors)")
        print('=' * 60)
        for err in errors:
            print(f"  {err['file']}:{err['line']}")
            print(f"    {err['message']}")
            if err.get('tip'):
                print(f"    Tip: {err['tip']}")
        print()


def print_by_file(data: dict):
    """Print errors grouped by file."""
    by_file = categorize_by_file(data)

    for filepath, errors in sorted(by_file.items()):
        print(f"\n{'=' * 60}")
        print(f"{filepath} ({len(errors)} errors)")
        print('=' * 60)
        for err in sorted(errors, key=lambda x: x['line'] or 0):
            print(f"  Line {err['line']}: [{err['identifier']}]")
            print(f"    {err['message']}")
        print()


def main():
    if len(sys.argv) < 2:
        print(__doc__)
        sys.exit(1)

    filepath = sys.argv[1]

    try:
        data = parse_phpstan_json(filepath)
    except Exception as e:
        print(f"Error parsing JSON: {e}")
        sys.exit(1)

    # Check for flags
    if "--by-file" in sys.argv:
        print_by_file(data)
    elif "--by-identifier" in sys.argv:
        # Check if specific identifier provided
        identifier = None
        idx = sys.argv.index("--by-identifier")
        if idx + 1 < len(sys.argv) and not sys.argv[idx + 1].startswith("--"):
            identifier = sys.argv[idx + 1]
        print_by_identifier(data, identifier)
    else:
        print_summary(data)


if __name__ == "__main__":
    main()
