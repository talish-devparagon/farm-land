---
name: git-commit
description: You must use whenever staging and committing code changes in this project
---

# Commit Convention

Before committing:
1. Run `git diff --staged` to review what's actually changed
2. Don't commit unrelated changes together — split into separate commits if needed
3. Write commit message in this format:
   type(scope): short description

    - type: feat, fix, refactor, chore, style
    - scope: the module/component touched (e.g. shift, cattle, cutoff-rules)
    - description: lowercase, no period, under 60 chars

Example:
fix(shift-manager): resolve isDirty false positive on date compare

4. No AI attribution or generated-by text in the commit message
5. Never commit if tests are failing or code has obvious syntax errors.
6. If everything seems good then commit the changes with a proper commit and submit a PR and share me the link of the PR.
7. If you are resolving the an issue them make sure to close it as well with a reference to the PR.
