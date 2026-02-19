# Notes & operational reminders

This file complements the main article and documents practical points
encountered while maintaining a real WordPress site with Git.

It is intentionally short and experience-driven.

---

## 🧭 Context reminder

- Existing WordPress site, already live in production
- The **entire site** is tracked in Git
- Git is used to maintain and deploy, not to reinstall WordPress
- Production is never edited directly

Local environment:
- **Lando**, configured to stay close to production

---

## 🔐 Configuration & secrets

### `.env` file
- The `.env` file is stored **outside of the webroot**
- It is **not committable by design**
- It contains:
  - environment-specific secrets (credentials, salts, keys)
  - environment-specific configuration values
    (e.g. `WP_DEBUG`, logging levels, feature toggles)

This is an architectural choice, not a Git exclusion workaround.

---

### `wp-config.php`
- `wp-config.php` **is committed**
- It contains **no sensitive data**
- All secrets and environment-specific values are loaded from `.env`
- The file is identical between local and production

This makes the configuration:
- explicit
- auditable
- reproducible

---

## ⚠️ Common pitfalls

### Editing directly on production

Even small “quick fixes” on production quickly lead to:
- undocumented changes
- Git drift
- fragile deployments

Once production diverges, trust in Git is lost.

---

### Versioning runtime content

Uploads, caches, backups and generated files:
- create noisy diffs
- increase repository size
- add zero value to version control

They must stay out of Git.

---

## 📦 About the `.gitignore`

The `.gitignore` provided in this repository is **not exhaustive**.

It only lists:
- files and directories that must absolutely not be committed
- based on real production usage

Everything else is versioned by default on purpose.

This keeps the repository:
- readable
- auditable
- aligned with reality

---

## 🔁 Local → Git → production

A change is considered valid only if:
1. it runs locally
2. it is committed in Git
3. it is deployed as-is to production

If one of these steps is skipped, the workflow is broken.

---

## 🧠 Final note

This setup is intentionally boring.

That is exactly what makes it reliable.
