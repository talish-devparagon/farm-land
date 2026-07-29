---
description: Triage and fix a bug quickly without unrelated refactors
argument-hint: [describe the bug]
allowed-tools: Read, Grep, Glob, Edit, Bash
---

Debug this issue: $ARGUMENTS

1. Find the likely cause — check related components/actions first
2. Explain the root cause in 1-2 lines
3. Fix it
4. Don't refactor unrelated code
5. Confirm the fix with a quick test if possible
