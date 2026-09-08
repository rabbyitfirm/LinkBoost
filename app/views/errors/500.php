<?php
$ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810');
?><!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>500 Error — <?=e(appName())?></title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{background:<?=e($bg)?>;color:#f0ede6;font-family:monospace;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem}</style>
</head><body>
<div>
  <div style="font-size:4rem;font-family:sans-serif;font-weight:900;color:<?=e($ac)?>;line-height:1">500</div>
  <div style="font-size:1.1rem;font-weight:700;margin:.7rem 0 .4rem">Server Error</div>
  <div style="font-size:.72rem;color:#8888aa;margin-bottom:1.5rem">Something went wrong. Check cPanel error logs.</div>
  <a href="<?=e(appUrl())?>/" style="padding:.6rem 1.3rem;background:<?=e($ac)?>;color:<?=e($bg)?>;font-weight:700;font-size:.72rem;border-radius:3px;text-decoration:none">← Home</a>
</div></body></html>
