<?php
ob_start();?>
<div style="max-width:800px;margin:0 auto;padding:4rem 1.5rem">
<div style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Terms of <span style="color:var(--acid)">Service</span></div>
<div style="font-size:.65rem;color:var(--mist);margin-bottom:2.5rem">Last updated: <?=date('F j, Y')?></div>
<?php foreach([
['1. Acceptance','By using '.appName().', you agree to these terms. If you do not agree, do not use the platform.'],
['2. Services','We provide link building, SEO tools, and AI-powered content tools. Link placements are subject to publisher availability. Turnaround times are estimates, not guarantees.'],
['3. Payment','All fees are due before service delivery. We accept Stripe, PayPal, and bank transfer. Prices are in '.currency().' and may change with 30 days notice.'],
['4. Refunds','Refunds are available if we fail to deliver a link matching the agreed specifications. No refunds for completed and approved orders. Plan subscriptions are non-refundable once used.'],
['5. Prohibited Use','You may not use our services for illegal purposes, spam, malware, adult content (unless specifically negotiated), or to violate any third-party rights.'],
['6. AI Tools','AI-generated content is provided as-is. You are responsible for reviewing and verifying all AI outputs before publication.'],
['7. Limitation of Liability','We are not liable for indirect damages, lost profits, or ranking changes. Our maximum liability is limited to the amount paid for the specific order or service.'],
['8. Changes','We may update these terms at any time. Continued use of the platform constitutes acceptance of updated terms.'],
['9. Contact','Questions? Contact us at '.setting('contact_email','admin@'.($_SERVER['HTTP_HOST']??'linkparty.net'))),
] as [$h,$b]):?>
<div style="margin-bottom:1.5rem"><div style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.45rem;font-size:.9rem"><?=e($h)?></div><div style="font-size:.72rem;color:var(--mist);line-height:1.85"><?=e($b)?></div></div>
<?php endforeach;?>
</div>
<?php $pageContent=ob_get_clean();$pageTitle='Terms of Service — '.appName();include LB_ROOT.'/app/views/public/layout.php';
