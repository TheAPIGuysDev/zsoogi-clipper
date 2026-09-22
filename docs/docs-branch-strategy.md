# Docs Branch Strategy

**Branch:** `premium` is the source of truth  
**Deploy from:** `premium` only  
**Merge direction:** `premium` → `main` (never the reverse)  

---

## Overview

All documentation lives in `docs/` and is committed to git alongside the plugin code. The `premium` branch is the primary development branch — all doc edits happen there. The `main` branch receives docs via normal merges at release time; nobody edits docs directly on `main`.

```
premium  ──●──●──●──●──────●─────►  (all development + doc edits)
                            │
main     ──────────────────●──────►  (merge from premium at release)
```

---

## Rules

1. **All doc edits on `premium`** — never edit `docs/` directly on `main`
2. **Deploy from `premium`** — run `./mkdocs-deploy.sh` from `premium`
3. **`main` gets docs via merge** — `git merge premium` at release time brings code and docs together
4. **Doc updates travel with feature commits** — when you gate a new feature behind `License::has_feature()`, update the relevant `.md` in the same commit

---

## Workflow

### Daily development (on `premium`)

```bash
# Edit code and docs together
vim includes/class-license.php
vim docs/freemium-development.md

# Commit both in one shot
git add includes/class-license.php docs/freemium-development.md
git commit -m "Gate PDF extraction behind license; update dev docs"

# Deploy docs at any time
./mkdocs-deploy.sh
```

### Release to `main`

```bash
# On premium — make sure everything is committed and docs are deployed
./mkdocs-deploy.sh

# Switch to main and merge
git checkout main
git merge premium

# Push to origin
git push origin main

# Switch back
git checkout premium
```

### Hotfix on `main` (code only — no doc changes)

If a critical bug fix lands on `main` directly (rare), cherry-pick it back to `premium` immediately so the branches don't diverge:

```bash
git checkout premium
git cherry-pick <commit-sha>
```

---

## What to Avoid

| Pattern | Why |
|---------|-----|
| Editing docs on `main` | Creates a divergence; docs on `premium` will overwrite on next merge |
| Running deploy from `main` | Fine for a one-off but `premium` is canonical — redeploy from there |
| Separate docs branch | Just another sync problem; unnecessary given single-codebase model |
| Git subtrees / submodules for docs | Overkill; `docs/` is small and low-churn |
| Cherry-picking doc commits | Tedious and error-prone; normal merges are cleaner |

---

## Why Not Two Doc Branches?

The previous model used `main` (free) and `ProVersion` (premium) as separate branches with separate code. The `premium` branch consolidates this into a single codebase with `License::has_feature()` gates. There's no longer a meaningful distinction in the code between branches, so there's no reason to maintain separate doc trees either.

One branch, one set of docs, one deploy target.

---

## CI Consideration (Future)

If CI is added, include a doc build check on PRs to `main`:

```yaml
- name: Build MkDocs
  run: mkdocs build --clean
```

This catches broken links, missing nav entries, and `{{ plugin_version }}` injection failures before merge — without deploying.
