# LinkParty — Complete SEO & Backlink Platform

## Quick Install
1. Upload all files to `public_html/`
2. Visit `https://yoursite.com/install/`
3. Follow the 4-step wizard
4. **Delete the `/install/` folder after setup**

## Structure (flat — everything in public_html)
```
public_html/
├── index.php          ← Router (do not delete)
├── .htaccess          ← URL rewriting
├── .env               ← Created by installer
├── schema.sql         ← DB schema (used by installer)
├── install/           ← DELETE after install
├── cron/              ← Set up in cPanel cron
├── app/               ← Application code
├── assets/            ← CSS/JS/images
└── storage/           ← Logs/cache (chmod 755)
```

## Cron Jobs (cPanel)
```
0 3 * * *  php /home/USER/public_html/cron/daily.php
0 6 * * 1  php /home/USER/public_html/cron/rank_check.php
```

## Default Login
- Admin: Set during install wizard
- Demo: demo@yoursite.com / secret (if added manually)

## Support
Admin > Settings > Contact Email
