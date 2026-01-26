#!/bin/bash
set -e

echo "Renaming src/ subdirectories..."

# Rename deepest directories first, then parents

# csrf
git mv src/csrf/core src/csrf/Core
git mv src/csrf/session src/csrf/Session
git mv src/csrf src/CSRF

# exceptions
git mv src/exceptions src/Exceptions

# fields
git mv src/fields/core src/fields/Core
git mv src/fields/html5 src/fields/HTML5
git mv src/fields/simple src/fields/Simple
git mv src/fields src/Fields

# validators
git mv src/validators/options src/validators/Options
git mv src/validators src/Validators

# widgets
git mv src/widgets/core src/widgets/Core
git mv src/widgets/html5 src/widgets/HTML5
git mv src/widgets src/Widgets

echo "Renaming tests/ subdirectories..."

# tests
git mv tests/common tests/Common
git mv tests/csrf tests/CSRF
git mv tests/fields tests/Fields
git mv tests/quirks tests/Quirks
git mv tests/supporting_classes tests/SupportingClasses
git mv tests/validators tests/Validators
git mv tests/widgets tests/Widgets

echo "Done! Now update composer.json and run 'composer dump-autoload'"