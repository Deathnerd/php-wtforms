# RALPH_PROMPT.md - Follow-Up Question Exploration Loop

## Current Objective

Recursively investigate all follow-up questions that arose during the initial typing investigation. Each answered question may spawn new questions - continue until all questions are resolved.

## Your Task This Iteration

1. **Check Progress**: Read `investigation-answers.md` → "New Questions Discovered" section
2. **Find Next Unanswered**: Look for questions without answers (marked with `[ ]` or no status)
3. **Investigate Thoroughly**: Use code inspection, commands, documentation, web search
4. **Document Findings**: Add answer directly below the question
5. **Spawn New Questions**: If your answer raises new questions, add them to the list
6. **Commit Progress**: Save your work to git

## Follow-Up Question Format

Questions in `investigation-answers.md` should follow this format:

```markdown
### New Questions Discovered

1. [ ] Should we create a `CSRFConfigurationException` for CSRF setup errors?

   **Answer:** [Your detailed findings here]

   **New questions raised:**
   - [Any new question that arose]

2. [x] Should `Widget` be marked `@template-covariant`?

   **Answer:** Yes, because Widget only reads field data for display...

   **New questions raised:**
   - None
```

## Investigation Depth

For each question, aim for:

1. **Concrete Answer**: Yes/No/It depends, with clear reasoning
2. **Evidence**: Code examples, documentation links, or command output
3. **Trade-offs**: If applicable, what are the pros/cons
4. **Recommendation**: What should we do and why
5. **Spawned Questions**: Any new questions this investigation raised

## Recursive Exploration Rules

**Add new questions when:**
- Your investigation reveals a design decision to make
- You find a related problem that needs solving
- There's an edge case that needs consideration
- Implementation details need clarification

**Stop adding questions when:**
- The question is purely about implementation (save for implementation phase)
- The question is already answered elsewhere
- The question is outside the scope of typing/static analysis

## Useful Commands

### Code Inspection
```bash
# Search for patterns
grep -r "pattern" src/ --include="*.php"

# Find class usage
grep -rn "ClassName" src/

# Check method signatures
grep -A 5 "function methodName" src/path/to/file.php
```

### PHPStan/Psalm Testing
```bash
# Test a hypothesis
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse src/path/to/file.php --memory-limit=512M

# Check if annotation works
echo '<?php /** @template-covariant T */ class Foo {}' | docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/phpstan analyse --level=9 -
```

### Documentation Research
- PHPStan docs: https://phpstan.org/
- Psalm docs: https://psalm.dev/
- PHP RFC archive for language features

## Progress Tracking

After each question is answered:

```bash
git add investigation-answers.md
git commit -m "Follow-up: [brief summary of question answered]"
```

## When to Continue vs Stop

**CONTINUE** if:
- There are unanswered questions (marked `[ ]`)
- New questions were added this iteration
- You made progress (at least 1 question answered)

**STOP** and output completion promise if:
- ALL follow-up questions have been answered (all marked `[x]`)
- No new questions have been raised in the last iteration
- Each answer has a clear recommendation

## Completion Signal

When ALL follow-up questions are resolved, output:

<promise>FOLLOWUP_COMPLETE</promise>

## Current Follow-Up Questions

From the investigation summary, these questions need exploration:

1. Should we create a `CSRFConfigurationException` for CSRF setup errors?
2. Should `Widget` be marked `@template-covariant` since it only reads field data?
3. Should we add `@psalm-immutable` to option classes after construction?
4. How to handle i18n messages in a type-safe way (translation keys)?
5. Should we create a common `WTFormsException` base class?

## Quality Standards

**Good follow-up answer:**
- Definitive recommendation with reasoning
- Code examples showing the approach
- Consideration of edge cases
- Clear "yes/no/it depends" conclusion

**Bad follow-up answer:**
- "Maybe we should..."
- No concrete recommendation
- Missing evidence or examples
- Raises questions without attempting to answer them

## Context Refresh

If starting fresh after context reset:

```bash
# Check current state
grep -A 20 "New Questions Discovered" investigation-answers.md

# See recent progress
git log --oneline -5

# Count remaining questions
grep -c "\[ \]" investigation-answers.md
```

Now begin your exploration. Find the next unanswered follow-up question and investigate it thoroughly.
