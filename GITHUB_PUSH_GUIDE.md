# How to Push Fix&Go to GitHub

## 📋 Prerequisites

1. **Git installed** on your computer
   - Download from: https://git-scm.com/downloads
   - Verify: `git --version`

2. **GitHub account**
   - Sign up at: https://github.com

3. **GitHub repository created**
   - Go to: https://github.com/new
   - Name: `fixandgo` (or your preferred name)
   - Keep it **Private** (recommended for now)
   - Don't initialize with README (we already have one)

## 🚀 Step-by-Step Instructions

### Step 1: Open Terminal/Command Prompt

Navigate to your project directory:

```bash
# Windows (Command Prompt or PowerShell)
cd C:\path\to\your\fixandgo\project

# Mac/Linux
cd /path/to/your/fixandgo/project
```

### Step 2: Initialize Git Repository

```bash
# Initialize git in your project
git init

# Check status
git status
```

### Step 3: Configure Git (First Time Only)

```bash
# Set your name
git config --global user.name "Your Name"

# Set your email (use your GitHub email)
git config --global user.email "your.email@example.com"

# Verify configuration
git config --list
```

### Step 4: Add Files to Git

```bash
# Add all files (respects .gitignore)
git add .

# Check what will be committed
git status
```

**Important**: The `.gitignore` file will automatically exclude:
- `config.php` (sensitive database credentials)
- `uploads/` folder (user uploaded files)
- Test and debug files
- Temporary files

### Step 5: Create First Commit

```bash
# Commit with a message
git commit -m "Initial commit: Fix&Go phone repair management system"
```

### Step 6: Connect to GitHub

Replace `yourusername` and `fixandgo` with your actual GitHub username and repository name:

```bash
# Add remote repository
git remote add origin https://github.com/yourusername/fixandgo.git

# Verify remote was added
git remote -v
```

### Step 7: Push to GitHub

```bash
# Push to GitHub (first time)
git push -u origin main

# Or if your default branch is 'master'
git push -u origin master
```

**If you get an authentication error:**

#### Option A: Use Personal Access Token (Recommended)

1. Go to: https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Give it a name: "Fix&Go Project"
4. Select scopes: `repo` (full control)
5. Click "Generate token"
6. **Copy the token** (you won't see it again!)
7. When pushing, use token as password:
   - Username: your GitHub username
   - Password: paste the token

#### Option B: Use SSH Key

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your.email@example.com"

# Copy public key
# Windows:
type %USERPROFILE%\.ssh\id_ed25519.pub

# Mac/Linux:
cat ~/.ssh/id_ed25519.pub

# Add to GitHub:
# 1. Go to: https://github.com/settings/keys
# 2. Click "New SSH key"
# 3. Paste your public key
# 4. Click "Add SSH key"

# Change remote to SSH
git remote set-url origin git@github.com:yourusername/fixandgo.git

# Push again
git push -u origin main
```

## ✅ Verify Upload

1. Go to your GitHub repository: `https://github.com/yourusername/fixandgo`
2. You should see all your files
3. Check that `config.php` is **NOT** there (it should be ignored)
4. README.md should be displayed on the main page

## 🔄 Making Future Changes

After making changes to your code:

```bash
# Check what changed
git status

# Add changed files
git add .

# Or add specific files
git add fixandgo/backend/some_file.php

# Commit with descriptive message
git commit -m "Fix: Resolved product transfer issue"

# Push to GitHub
git push
```

## 📝 Useful Git Commands

```bash
# View commit history
git log

# View changes before committing
git diff

# Undo changes to a file
git checkout -- filename.php

# Create a new branch
git checkout -b feature/new-feature

# Switch branches
git checkout main

# Merge branch into main
git checkout main
git merge feature/new-feature

# Pull latest changes from GitHub
git pull

# Clone repository to another computer
git clone https://github.com/yourusername/fixandgo.git
```

## 🔒 Security Checklist

Before pushing, verify these files are **NOT** included:

- [ ] `fixandgo/backend/config.php` ❌ (should be in .gitignore)
- [ ] `fixandgo/backend/db.php` ❌ (if it contains credentials)
- [ ] `fixandgo/uploads/*` ❌ (user uploaded files)
- [ ] `.env` files ❌ (environment variables)
- [ ] Database dumps with real data ❌

Files that **SHOULD** be included:

- [x] `fixandgo/backend/config.example.php` ✅ (template)
- [x] `.gitignore` ✅
- [x] `README.md` ✅
- [x] All `.html`, `.css`, `.js` files ✅
- [x] All `.php` files (except config.php) ✅
- [x] Migration `.sql` files ✅

## 🐛 Troubleshooting

### Error: "fatal: not a git repository"
```bash
# Make sure you're in the right directory
pwd  # or cd on Windows

# Initialize git
git init
```

### Error: "remote origin already exists"
```bash
# Remove existing remote
git remote remove origin

# Add it again
git remote add origin https://github.com/yourusername/fixandgo.git
```

### Error: "failed to push some refs"
```bash
# Pull first, then push
git pull origin main --allow-unrelated-histories
git push origin main
```

### Accidentally committed config.php
```bash
# Remove from git but keep local file
git rm --cached fixandgo/backend/config.php

# Commit the removal
git commit -m "Remove config.php from tracking"

# Push
git push
```

## 📚 Additional Resources

- **Git Documentation**: https://git-scm.com/doc
- **GitHub Guides**: https://guides.github.com/
- **Git Cheat Sheet**: https://education.github.com/git-cheat-sheet-education.pdf

## 🎯 Quick Reference

```bash
# Complete workflow for first push
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/fixandgo.git
git push -u origin main

# Complete workflow for updates
git add .
git commit -m "Description of changes"
git push
```

## 🤝 Collaboration

If working with a team:

```bash
# Before starting work
git pull

# Create feature branch
git checkout -b feature/your-feature

# Make changes and commit
git add .
git commit -m "Add new feature"

# Push feature branch
git push origin feature/your-feature

# Create Pull Request on GitHub
# After review and merge, update main
git checkout main
git pull
```

---

**Remember**: Never commit sensitive information like passwords, API keys, or database credentials!
