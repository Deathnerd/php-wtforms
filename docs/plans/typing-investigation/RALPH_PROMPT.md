# RALPH_PROMPT.md - PHP-WTForms Typing Investigation Loop

## Current Objective

Systematically investigate all questions in `typing-prd.md` and document findings in `investigation-answers.md`.

## Your Task This Iteration

1. **Check Progress**: Read `investigation-answers.md` to see which sections are complete
2. **Find Next Question**: Look at `typing-prd.md` for the next unchecked question
3. **Investigate**: Use the appropriate method (code inspection, commands, web search, documentation)
4. **Document**: Update `investigation-answers.md` with your findings
5. **Mark Complete**: Check off the question in `typing-prd.md`
6. **Commit**: Save your progress to git

## Investigation Workflow

For each question, follow this pattern:

```bash
# 1. First, check what question you're on
grep -n "Needs Investigation" investigation-answers.md | head -1

# 2. Investigate using appropriate tools
# (varies by question type)

# 3. Update the answers file with findings
# (edit investigation-answers.md)

# 4. Mark the question complete in the PRD
# (edit typing-prd.md, change [ ] to [x])

# 5. Commit progress
git add investigation-answers.md typing-prd.md
git commit -m "Investigate Q[X.Y]: [brief finding summary]"
```

## Useful Commands for Investigation

### PHPStan Analysis
```bash
# Current level
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M

# Specific level
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M -l 6

# JSON output for parsing
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M --error-format=json 2>/dev/null

# With strict rules
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M -c phpstan-strict.neon
```

### Psalm Analysis
```bash
# Install if needed
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer require --dev vimeo/psalm

# Initialize
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --init

# Run analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm

# Taint analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --taint-analysis

# Show all info
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --show-info=true
```

### Code Pattern Search
```bash
# Find template annotations
grep -r "@template" src/ --include="*.php"

# Find PHPStan-specific annotations
grep -r "@phpstan-" src/ --include="*.php"

# Find Psalm-specific annotations
grep -r "@psalm-" src/ --include="*.php"

# Find mixed types
grep -r "mixed" src/ --include="*.php"

# Find files with return types
find src -name "*.php" -exec grep -l "function.*:" {} \;

# Count methods with return types
grep -rh "public function\|protected function\|private function" src/ | wc -l
grep -rh "function.*): " src/ | wc -l
```

### Configuration Files
```bash
cat phpstan.neon 2>/dev/null || cat phpstan.neon.dist
cat psalm.xml 2>/dev/null
cat composer.json | grep -A 20 '"require-dev"'
```

### Check PHP Version Features
```bash
# Verify PHP version
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 php -v

# Check available types
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 php -r "echo PHP_VERSION;"
```

## Common Investigation Patterns

### For "What is the current state?" questions:
1. Run the relevant tool
2. Capture the output
3. Count/categorize errors
4. Document the findings

### For "How do we handle X?" questions:
1. Search PHPStan/Psalm docs
2. Search for existing patterns in codebase
3. Search GitHub issues for similar problems
4. Propose a solution based on findings

### For "What is the type of X?" questions:
1. Read the relevant code
2. Check how the value is used
3. Check what types are valid inputs
4. Propose the most specific type that works

## Progress Checkpoints

After completing each section (10 questions), do a summary commit:

```bash
git add -A
git commit -m "Complete Section X: [Section Name] - [key findings summary]"
```

## When to Continue vs Stop

**CONTINUE** investigating if:
- There are still unchecked questions in the PRD
- You've made progress this iteration (at least 1 question answered)

**STOP** and output completion promise if:
- ALL questions in ALL sections have been investigated
- The Investigation Summary section is filled out
- You've verified the completion checklist

## Completion Signal

When ALL investigation criteria are met, output:

<promise>INVESTIGATION_COMPLETE</promise>

This signals to the Ralph loop that the investigation phase is done and we can proceed to implementation.

## Important Reminders

- **Be thorough**: Vague answers don't help implementation
- **Show evidence**: Every answer needs command output or documentation references
- **Note uncertainties**: If something is experimental or might change, say so
- **Follow-up questions are valuable**: They'll be addressed in the next phase
- **Git commits are your memory**: Commit frequently so progress persists
- **PHP 7.4 constraint**: Remember we cannot use PHP 8 features

## Context Refresh

If you're starting fresh after a context reset, begin by:

```bash
# See overall progress
grep -c "\[x\]" typing-prd.md
grep -c "\[ \]" typing-prd.md

# See which section you're in
grep -B2 "Needs Investigation" investigation-answers.md | head -10

# Review recent commits
git log --oneline -10
```

Now begin your investigation. Find the next unanswered question and investigate it thoroughly.
