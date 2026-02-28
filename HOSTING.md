# Hosting CRM on cPanel – https://crm.alresalahct.com

Deploy this CRM as the subdomain **crm.alresalahct.com** on cPanel.

## 1. Upload the project

- Upload the full project (e.g. via File Manager or FTP) so the **document root** of the subdomain can point to the `public` folder.
- Example: upload to `crm` (or `crm_alresalah`) so the path is something like:
  - `~/crm/` (contains `app/`, `database/`, `public/`, `.env`, etc.)
  - Subdomain document root will be set to `crm/public` (see step 2).

## 2. Create subdomain and set document root

1. In cPanel go to **Subdomains**.
2. Create subdomain: **crm** → `crm.alresalahct.com`.
3. Set **Document Root** to the folder that contains `index.php` and `assets/` — i.e. the **`public`** folder of this project.
   - Example: `crm/public` or `public_html/crm/public`, so that the URL **https://crm.alresalahct.com/** serves `public/index.php` and `public/assets/`.
4. Save.

## 3. Environment (.env)

1. In the **project root** (one level above `public/`), create or edit `.env`.
2. Copy from `.env.example` and set:

```ini
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key

# Required for subdomain root:
BASE_URL=/
```

- **BASE_URL=/** — so all links and redirects use `https://crm.alresalahct.com/` (no path prefix).
- Do **not** put `.env` inside `public/`; keep it in the project root (same level as `app/`).

## 4. SSL (HTTPS)

- In cPanel use **SSL/TLS** or **Let's Encrypt** to issue a certificate for `crm.alresalahct.com`.
- The included `public/.htaccess` already forces HTTPS (except on localhost) for production.

## 5. Apache

- **mod_rewrite** must be enabled (default on cPanel).
- The file `public/.htaccess` routes all requests to `public/index.php` and forces HTTPS.

## 6. Checklist

- [ ] Subdomain `crm.alresalahct.com` created.
- [ ] Document root points to **`public`** (e.g. `crm/public`).
- [ ] `.env` in project root with `SUPABASE_*` and **BASE_URL=/**.
- [ ] SSL certificate installed for `crm.alresalahct.com`.
- [ ] Visit **https://crm.alresalahct.com/** and log in with a Supabase Auth user.

## Security notes

- `.env` is in `.gitignore`; do not commit it. On the server it must exist in the project root (outside `public/`).
- `app/`, `database/`, and `storage/` are not under the document root when the doc root is `public/`, so they are not directly accessible. Root `.htaccess` and per-folder `.htaccess` add extra protection if the document root is ever mis-set.
