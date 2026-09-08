<?php
header('Content-Type: text/plain');
$base=appUrl();
echo "User-agent: *\n";
echo "Disallow: /admin\n";
echo "Disallow: /dashboard\n";
echo "Disallow: /orders\n";
echo "Disallow: /checkout\n";
echo "Disallow: /api/\n";
echo "Disallow: /install/\n";
echo "Disallow: /storage/\n";
echo "Allow: /blog/\n";
echo "Allow: /tools\n";
echo "Allow: /services\n";
echo "Allow: /pricing\n";
echo "\nSitemap: $base/sitemap.xml\n";
echo "\n# LinkParty - Generated ".date('Y-m-d')."\n";
