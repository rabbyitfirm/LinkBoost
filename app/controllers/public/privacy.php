<?php
ob_start();?>
<div style="max-width:800px;margin:0 auto;padding:4rem 1.5rem">
<div style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Privacy <span style="color:var(--acid)">Policy</span></div>
<div style="font-size:.65rem;color:var(--mist);margin-bottom:2.5rem">Last updated: <?=date('F j, Y')?></div>
<?php foreach([
['What We Collect','Name, email address, payment information (processed by Stripe — we never store card numbers), website URLs you submit for orders, and usage data for SEO tools.'],
['How We Use It','To deliver services, send order updates, improve our platform, and send newsletters (only if you opt in). We do not sell your data to third parties.'],
['Cookies','We use essential session cookies to keep you logged in. We may use analytics cookies (Google Analytics) if enabled by the site admin. You can disable cookies in your browser.'],
['Data Retention','We retain account data as long as your account is active. Order history is kept for 3 years for legal compliance. You can request deletion at any time.'],
['Third Parties','We use Stripe for payments, Google for OAuth login (optional), and Claude AI for AI tool features. Each has their own privacy policy.'],
['Your Rights','You have the right to access, correct, or delete your personal data. Email us at '.setting('contact_email','admin@'.($_SERVER['HTTP_HOST']??'linkparty.net')).' to make a request.'],
['Security','We use HTTPS, bcrypt password hashing, and prepared SQL statements. No system is 100% secure, but we take reasonable measures to protect your data.'],
['Contact','Privacy questions? Email '.setting('contact_email','admin@'.($_SERVER['HTTP_HOST']??'linkparty.net'))),
] as [$h,$b]):?>
<div style="margin-bottom:1.5rem"><div style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.45rem;font-size:.9rem"><?=e($h)?></div><div style="font-size:.72rem;color:var(--mist);line-height:1.85"><?=e($b)?></div></div>
<?php endforeach;?>
</div>
<?php $pageContent=ob_get_clean();$pageTitle='Privacy Policy — '.appName();include LB_ROOT.'/app/views/public/layout.php';
