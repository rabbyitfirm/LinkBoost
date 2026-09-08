<?php
$ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810');
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 Not Found — <?=e(appName())?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>*{margin:0;padding:0;box-sizing:border-box}body{background:<?=e($bg)?>;color:#f0ede6;font-family:'DM Mono',monospace;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem}</style>
</head><body>
<div>
  <div style="font-size:5rem;font-family:'Syne',sans-serif;font-weight:800;color:<?=e($ac)?>;line-height:1">404</div>
  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.2rem;margin:.8rem 0 .5rem">Page Not Found</div>
  <div style="font-size:.72rem;color:#8888aa;margin-bottom:1.5rem">The page you're looking for doesn't exist or has been moved.</div>
  <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
    <a href="<?=e(appUrl())?>/" style="padding:.6rem 1.2rem;background:<?=e($ac)?>;color:<?=e($bg)?>;font-family:'Syne',sans-serif;font-weight:700;font-size:.72rem;border-radius:3px;text-decoration:none">← Home</a>
    <a href="<?=e(appUrl())?>/dashboard" style="padding:.6rem 1.2rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);color:#8888aa;font-family:'Syne',sans-serif;font-weight:700;font-size:.72rem;border-radius:3px;text-decoration:none">Dashboard</a>
  </div>
</div>
</body></html>
