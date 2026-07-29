---
description: Stage and commit changes following project conventions
argument-hint: [optional context about the change]
allowed-tools: Bash, Read
---

Review staged/unstaged changes: $ARGUMENTS

1. Run `git diff` and `git status` to see what changed
2. Group related changes; suggest splitting if unrelated changes are mixed
3. Write a commit message as: type(scope): short description
   (types: feat, fix, refactor, chore, style — lowercase, no period, under 60 chars)
4. No AI attribution in the message
5. Show me the commit message before running `git commit`
