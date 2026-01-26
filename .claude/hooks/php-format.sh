#!/bin/bash

# PHP CS Fixer Auto-Format Hook
# Automatically formats PHP files using php-cs-fixer after editing
# Gracefully skips if php-cs-fixer or config are not available

# Determine project root (relative to this script's location)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

# Paths to php-cs-fixer and config
PHP_CS_FIXER="$PROJECT_ROOT/vendor/bin/php-cs-fixer"
PHP_CS_CONFIG="$PROJECT_ROOT/.php-cs-fixer.dist.php"

# Check if php-cs-fixer binary exists
if [ ! -x "$PHP_CS_FIXER" ]; then
    exit 0
fi

# Check if config file exists
if [ ! -f "$PHP_CS_CONFIG" ]; then
    exit 0
fi

# Read JSON input from stdin
input=$(cat)

# Extract the file path
file_path=$(echo "$input" | jq -r '.tool_input.file_path // empty')

# Only proceed if we have a file path
if [ -z "$file_path" ]; then
    exit 0
fi

# Only format PHP files
if [[ ! "$file_path" =~ \.php$ ]]; then
    exit 0
fi

echo "🎨 Auto-formatting with php-cs-fixer..."

# Run php-cs-fixer using system php
if php "$PHP_CS_FIXER" fix --config="$PHP_CS_CONFIG" --quiet "$file_path" 2>/dev/null; then
    echo "✅ Formatted successfully"
else
    # Don't block on formatter errors, just notify
    echo "⚠️  php-cs-fixer had issues (continuing anyway)"
fi

exit 0
