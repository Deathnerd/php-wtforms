# CLAUDE.md - PHP-WTForms Typing Investigation

## Project Overview

This is a **comprehensive static analysis and typing initiative** for php-wtforms. The goal is to achieve maximum type safety using PHPStan, Psalm, and taint analysis while maintaining PHP 7.4 compatibility.

## Key Files

- `typing-prd.md` - The Product Requirements Document with all investigation questions
- `investigation-answers.md` - Template for documenting answers to each question
- `RALPH_PROMPT.md` - The prompt file for the Ralph Wiggum loop

## Your Mission

You are running in a **Ralph Wiggum loop** to systematically investigate every question in the PRD. Your goal is to:

1. Read the PRD and identify the next unanswered question
2. Investigate the question using appropriate methods
3. Document your findings in `investigation-answers.md`
4. Update the checkbox in `typing-prd.md`
5. Continue to the next question

## Investigation Methodology

### For PHPStan Questions
```bash
# Run PHPStan at different levels
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M -l 6
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M -l 9

# Check current config
cat phpstan.neon
cat phpstan.neon.dist

# Generate baseline
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M --generate-baseline

# Export errors for analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M --error-format=json 2>/dev/null > phpstan-errors.json
```

### For Psalm Questions
```bash
# Install Psalm (if not present)
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer require --dev vimeo/psalm

# Initialize Psalm
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --init

# Run Psalm at different levels
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --show-info=true

# Taint analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --taint-analysis
```

### For Code Inspection Questions
```bash
# Search for patterns
grep -r "@template" src/
grep -r "@phpstan-" src/
grep -r "@psalm-" src/
grep -r "mixed" src/
grep -r "@var" src/

# Count type coverage
find src -name "*.php" -exec grep -l "function.*:" {} \; | wc -l

# Check specific files
cat src/Fields/Core/Field.php
cat src/Validators/Validator.php
cat src/Widgets/Core/Widget.php
```

### For Documentation Research
- PHPStan docs: https://phpstan.org/
- Psalm docs: https://psalm.dev/
- Check GitHub issues for common patterns
- Search for extension packages on Packagist

## Progress Tracking

After investigating each question:

1. Update the answer in `investigation-answers.md` with:
   - Status: Answered | Partial | Blocked
   - Investigation method used
   - Detailed findings with evidence
   - Decision or recommendation
   - Any follow-up questions

2. Check off the question in `typing-prd.md`

3. Commit your progress:
   ```bash
   git add investigation-answers.md typing-prd.md
   git commit -m "Investigate Q[X.Y]: [brief summary]"
   ```

## Section Order

Work through sections in order:
1. Current State Assessment (Q1.1-Q1.10)
2. PHPStan Configuration & Baseline (Q2.1-Q2.10)
3. Psalm Setup & Configuration (Q3.1-Q3.10)
4. Taint Analysis (Q4.1-Q4.10)
5. Field Classes Typing (Q5.1-Q5.10)
6. Validator Classes Typing (Q6.1-Q6.10)
7. Widget Classes Typing (Q7.1-Q7.10)
8. Form & Meta Typing (Q8.1-Q8.10)
9. CSRF & Security Typing (Q9.1-Q9.10)
10. Generics & Templates Strategy (Q10.1-Q10.10)
11. Internal Utilities Typing (Q11.1-Q11.10)
12. Testing Type Coverage (Q12.1-Q12.10)

## Handling Blocked Questions

If you encounter a question you cannot answer:
1. Mark it as Blocked in the answers file
2. Document WHY it's blocked
3. Propose what would unblock it
4. Continue to the next question
5. Return to blocked questions at the end

## Quality Standards

**Good Answer:**
- Includes specific command output or code snippets
- References official documentation
- Makes a clear recommendation
- Identifies any caveats or trade-offs

**Bad Answer:**
- Vague statements without evidence
- Assumptions without verification
- Missing recommendations

## Completion Criteria

The investigation is COMPLETE when:
1. All 120 questions have been investigated
2. Each answer has documented evidence
3. Blocking issues are documented with solutions
4. Implementation priority is established

When complete, output: `<promise>INVESTIGATION_COMPLETE</promise>`

## Important Notes

- php-wtforms requires PHP >= 7.4 (no PHP 8 features like union types, attributes)
- PHPStan is already configured at level 5
- Some typing work has begun (generics on Field, Widget, Validator base classes)
- Tests are in `tests/` and use PHPUnit
- Docker is the recommended way to run analysis tools

## Context for Key Technologies

### PHPStan
- Currently at level 5
- Levels go from 0 (loose) to 9 (strict)
- Level 9 checks everything including mixed types
- Extensions available for PHPUnit, Carbon, etc.

### Psalm
- Not currently installed
- Levels go from 1 (strict) to 8 (loose) - opposite of PHPStan!
- Has built-in taint analysis for security
- Can coexist with PHPStan

### PHP 7.4 Type Constraints
- Native return types: yes
- Native property types: yes (typed properties)
- Union types: NO (PHP 8.0+)
- Named arguments: NO (PHP 8.0+)
- Attributes: NO (PHP 8.0+)
- Mixed type: NO (PHP 8.0+)
- Must use PHPDoc for unions: `@param string|int $value`

## Git Workflow

Commit frequently with meaningful messages:
```
git add -A
git commit -m "Section X: Investigated questions X.1-X.5"
```

This helps the Ralph loop maintain state across context resets.
