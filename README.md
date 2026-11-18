# Ellis Knowledge Studio Web

Source for **https://web.ellisknowledgestudio.com/**: websites, Google, social media and AI assistants for local trades.

Plain static HTML with one PHP mail script. No build step: edit a file, upload it, done.

## Files

| File | What it is |
|---|---|
| `index.html` | Home page: hero, live demos, services, AI assistant, prices, work, FAQ, quote form. All CSS is in the `<style>` block at the top. |
| `start.html` | Full business questionnaire (linked from the quote section) |
| `contact-thanks.html` | Thank-you page after a form is sent |
| `privacy.html` | Privacy notice |
| `send.php` | Emails both forms to james@ through the Hostinger mailbox (SMTP, via PHPMailer) |
| `mail-config.example.php` | Template for `mail-config.php` |
| `phpmailer/` | PHPMailer library (left as is; its `.htaccess` blocks direct access) |
| `robots.txt`, `sitemap.xml` | Search engine files |

### mail-config.php is NOT in this repo

`mail-config.php` holds the mailbox password, so `.gitignore` keeps it out of GitHub. It exists only on the server in `public_html`. **Never delete it there.** If it's ever lost, copy `mail-config.example.php` to `mail-config.php` on the server and put the password in.

## Live demos section (`#demos` in index.html)

These cards link to the trade demo sites (built in Hostinger Website Builder, each with an ElevenLabs voice agent):

| Trade | Site | Demo business | Agent |
|---|---|---|---|
| Drains | https://drains.ellisknowledgestudio.com | Harbourside Drainage | Josie |
| Heating | https://heating.ellisknowledgestudio.com | Chine Heating & Plumbing | Tom |
| Window cleaning | https://windows.ellisknowledgestudio.com | Hengistbury Window & Gutter Care | Ben |

The demos are also linked from the top nav ("Live demos"), the hero ("Hear an AI receptionist answer…"), the AI assistant section and the footer.

### Adding the next trade demo

1. In `index.html`, find `<div class="demos">` and copy one whole `<a class="demo" …>…</a>` block.
2. Change: the `href` and `.url` text (subdomain), `.trade`, `h3` (business name), the blurb, the agent initial/name/accent and the button text.
3. Pick an icon: swap the `<svg>` inside `.ico` (any 24×24 line icon with `stroke="currentColor"` will match).
4. Add a footer link next to the other demo links, and add the trade to the hero link text if you like.
5. With 4 or more demos, the grid still works: change `.demos{grid-template-columns:repeat(3,1fr)}` to `repeat(2,1fr)` for a 2×2 layout.

## Deploying

The current method: upload the changed files to `public_html` for the web subdomain in Hostinger File Manager.

Optional: auto-deploy from GitHub (hPanel → Advanced → Git). Note that Hostinger's Git deploy wants an empty target folder the first time. If you use it, back up `mail-config.php` first and put it back in `public_html` after the first deploy. The quote form will fail without it.

## Other sites (footer links)

Main site https://ellisknowledgestudio.com · Studio https://studio.ellisknowledgestudio.com · Classroom https://classroom.ellisknowledgestudio.com
