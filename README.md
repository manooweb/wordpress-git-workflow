# 🧩 WordPress Git Workflow
### Existing site · Local → Git → Production

This repository is a lightweight technical companion to my article (FR):

📖 **“Maintenir WordPress avec Git entre local et production”**
➡️ https://manooweb.fr/maintenir-wordpress-avec-git-entre-local-et-production/

The article explains the full reasoning and workflow.
This repository only hosts **practical, reusable assets**.

---

## 🎯 Scope & intent

This repository reflects a **real WordPress production site**, already live.

- 🚫 WordPress is **not reinstalled**
- 📦 The **entire site** is tracked in Git
- 🔄 Git is used to **maintain, update and deploy**, not to bootstrap

This is **not** a starter kit or boilerplate.

---

## 📂 What this repo contains

✅ Included:
- `.gitignore` extracted from real-world usage
- Notes about common pitfalls and operational choices
- A durable technical entry point linked to the article

❌ Not included:
- No tutorial duplication
- No full documentation
- No demo WordPress project

---

## 🔁 Workflow summary

- 🚫 No direct edits on production
- ❌ No FTP workflow
- 🧪 Changes are prepared and validated **locally**
- 🧾 Then committed to **Git**
- 🚀 Then deployed to **production**

🛠️ Local environment:
- **Lando** (local WordPress environment close to production)

---

## 📦 Versioned vs non-versioned

✔️ Versioned in Git:
- WordPress core
- Themes
- Plugins
- mu-plugins
- Custom code

🚫 Explicitly excluded:
- 🔐 Secrets
- ♻️ Runtime content
- 🧹 Caches
- 💾 Backups
- 🧪 Environment-specific files

See `.gitignore` for details.

---

## 🔗 Links

- 📖 Article (FR): https://manooweb.fr/maintenir-wordpress-avec-git-entre-local-et-production/
- 🌍 Website: https://manooweb.fr/
- 🐙 GitHub: https://github.com/manooweb
