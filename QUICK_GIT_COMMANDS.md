# Quick Git Commands Reference

## 🚀 First Time Setup (Do Once)

```bash
# Navigate to your project
cd C:\path\to\fixandgo

# Initialize git
git init

# Configure your identity
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Add all files
git add .

# First commit
git commit -m "Initial commit: Fix&Go project"

# Connect to GitHub (replace with your repo URL)
git remote add origin https://github.com/yourusername/fixandgo.git

# Push to GitHub
git push -u origin main
```

## 🔄 Daily Workflow (After Changes)

```bash
# Check what changed
git status

# Add all changes
git add .

# Commit with message
git commit -m "Description of what you changed"

# Push to GitHub
git push
```

## 📥 Pull Latest Changes

```bash
# Get latest code from GitHub
git pull
```

## 🌿 Working with Branches

```bash
# Create new branch
git checkout -b feature/new-feature

# Switch to main branch
git checkout main

# List all branches
git branch

# Delete branch
git branch -d feature/old-feature
```

## ↩️ Undo Changes

```bash
# Undo changes to a file (before commit)
git checkout -- filename.php

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1
```

## 📊 View History

```bash
# View commit history
git log

# View short history
git log --oneline

# View changes
git diff
```

## 🔧 Fix Common Issues

```bash
# Remove file from git but keep locally
git rm --cached filename.php

# Change last commit message
git commit --amend -m "New message"

# Remove remote
git remote remove origin

# Add remote again
git remote add origin https://github.com/user/repo.git
```

## ✅ Before Every Push - Checklist

- [ ] `git status` - Check what will be committed
- [ ] Verify no sensitive files (config.php, .env)
- [ ] `git add .` - Add all changes
- [ ] `git commit -m "Clear message"` - Commit with message
- [ ] `git push` - Push to GitHub

## 🆘 Emergency Commands

```bash
# Discard ALL local changes
git reset --hard HEAD

# Get back to last committed state
git checkout .

# See what's in .gitignore
cat .gitignore
```

---

**Pro Tip**: Always `git pull` before starting work and `git push` when done!
