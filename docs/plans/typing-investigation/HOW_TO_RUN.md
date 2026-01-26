# Running the PHP-WTForms Typing Investigation

## Prerequisites

1. **Install Claude Code** (if not already installed):
   ```bash
   curl -fsSL https://claude.ai/install.sh | bash
   ```

2. **Ensure Docker is running** (for PHP analysis):
   ```bash
   docker info
   docker-compose -f .devcontainer/docker-compose.yml ps
   ```

3. **Install composer dependencies**:
   ```bash
   docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer install
   ```

## Running the Investigation Loop

### Option 1: Interactive Ralph Loop (Recommended for First Run)

```bash
cd /path/to/php-wtforms
claude

# Inside Claude Code, start the Ralph loop:
/ralph-loop "$(cat docs/plans/typing-investigation/RALPH_PROMPT.md)" --max-iterations 50 --completion-promise "INVESTIGATION_COMPLETE"
```

This will:
1. Read the prompt from RALPH_PROMPT.md
2. Work on investigating questions
3. Commit progress to git
4. Continue until all questions are answered or 50 iterations are reached

### Option 2: Section-by-Section (More Control)

Run one section at a time for more control:

```bash
# Section 1 only - Current State Assessment
/ralph-loop "Focus on Section 1 (Current State Assessment) only. $(cat docs/plans/typing-investigation/RALPH_PROMPT.md)" \
  --max-iterations 15 \
  --completion-promise "SECTION_1_COMPLETE"

# Then Section 2 - PHPStan Configuration
/ralph-loop "Focus on Section 2 (PHPStan Configuration & Baseline) only. $(cat docs/plans/typing-investigation/RALPH_PROMPT.md)" \
  --max-iterations 15 \
  --completion-promise "SECTION_2_COMPLETE"
```

### Option 3: Manual Investigation

Run Claude Code and manually work through questions:

```bash
cd /path/to/php-wtforms
claude

# Then just say:
# "Let's investigate the typing PRD. Start with Q1.1"
```

## Monitoring Progress

### Check Progress During Run
```bash
# In another terminal
cd /path/to/php-wtforms

# Count completed vs remaining questions
grep -c "\[x\]" docs/plans/typing-investigation/typing-prd.md
grep -c "\[ \]" docs/plans/typing-investigation/typing-prd.md

# See recent investigation activity
git log --oneline -20

# Watch the answers file
tail -f docs/plans/typing-investigation/investigation-answers.md
```

### Check PHPStan/Psalm Status
```bash
# Current PHPStan errors
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M 2>&1 | tail -20

# If Psalm is installed
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm 2>&1 | tail -20
```

## After Investigation Completes

1. **Review the findings**:
   ```bash
   # Open the answers file
   cat docs/plans/typing-investigation/investigation-answers.md

   # Check for any blocked questions
   grep "Blocked" docs/plans/typing-investigation/investigation-answers.md

   # Review the summary
   tail -100 docs/plans/typing-investigation/investigation-answers.md
   ```

2. **Address any blockers**:
   If there are blocked questions, you may need to:
   - Make decisions on ambiguous type situations
   - Research specific edge cases
   - Consult PHP/PHPStan/Psalm documentation

3. **Proceed to implementation**:
   Once investigation is complete, you can start implementing:
   ```bash
   /ralph-loop "Based on the completed investigation in docs/plans/typing-investigation/investigation-answers.md,
   implement Phase 1: PHPStan level 6 with zero errors.
   Follow the recommendations documented in the answers file.
   Output <promise>PHASE_1_COMPLETE</promise> when done." \
   --max-iterations 30 \
   --completion-promise "PHASE_1_COMPLETE"
   ```

## Useful Commands During Investigation

### PHPStan Commands
```bash
# Run at current level
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M

# Run at specific level
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M -l 6

# Export errors to JSON
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=512M --error-format=json 2>/dev/null > phpstan-errors.json

# Use the parsing script
uv run scripts/parse_phpstan.py phpstan-errors.json --by-identifier
```

### Psalm Commands
```bash
# Install Psalm
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer require --dev vimeo/psalm

# Initialize
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --init

# Run analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm

# Taint analysis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/psalm --taint-analysis
```

### Code Search
```bash
# Find all template annotations
grep -rn "@template" src/

# Find all phpstan annotations
grep -rn "@phpstan-" src/

# Find files with mixed types
grep -rn "mixed" src/

# Count methods with return types
grep -rh "function.*): " src/ | wc -l
```

## Troubleshooting

### Docker Issues
```bash
# Rebuild containers
docker-compose -f .devcontainer/docker-compose.yml build --no-cache

# Clear composer cache
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 composer clear-cache
```

### PHPStan Memory Issues
```bash
# Increase memory limit
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --memory-limit=1G
```

### Loop Gets Stuck
- Cancel and review investigation-answers.md for blocked items
- Provide missing information and restart
- Check if a question needs manual research

## Cost Estimation

A full investigation (120 questions) typically requires:
- 40-60 iterations
- ~50,000-100,000 tokens per iteration
- Estimated cost: $15-30 depending on complexity

For budget control:
```bash
# Check cost periodically inside Claude Code
/cost

# Set a lower iteration limit for testing
/ralph-loop "..." --max-iterations 10
```

## Expected Output

After successful completion, you should have:

1. `investigation-answers.md` - Fully populated with answers and evidence
2. `typing-prd.md` - All checkboxes marked complete
3. Git history showing incremental progress
4. A clear implementation roadmap in the summary section

The investigation answers become the specification for the implementation phase.

## Implementation Phases (Post-Investigation)

Based on investigation, implementation typically proceeds:

1. **Phase 1**: PHPStan level 6 with zero errors
2. **Phase 2**: Add Psalm, achieve level 4
3. **Phase 3**: PHPStan level 7-8
4. **Phase 4**: Psalm level 2-3, taint analysis
5. **Phase 5**: PHPStan level 9, Psalm level 1

Each phase should be a separate Ralph loop with clear completion criteria.
