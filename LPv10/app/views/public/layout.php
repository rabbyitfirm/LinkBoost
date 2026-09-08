<?php
// Usage: $pageTitle, $pageContent, $pageMeta=''
$ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810');
?><!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($pageTitle??appName())?></title>
<?=$pageMeta??''?>
<?php
$ga4=setting('ga4_id','');$fbp=setting('fb_pixel','');$gtm=setting('gtm_id','');$ch=setting('custom_head','');
if($ga4):?><script async src="https://www.googletagmanager.com/gtag/js?id=<?=e($ga4)?>"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?=e($ga4)?>');</script><?php endif;?>
<?php if($gtm):?><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?=e($gtm)?>');</script><?php endif;?>
<?php if($fbp):?><script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?=e($fbp)?>');fbq('track','PageView');</script><?php endif;?>
<?php if($ch):echo $ch;endif;?>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="<?=e(setting('accent_color','#b8ff3c'))?>">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{--ink:<?=e($bg)?>;--acid:<?=e($ac)?>;--paper:#f0ede6;--mist:#8888aa;--card:#0f0f1e;--card2:#131325;--border:rgba(255,255,255,.07)}
*{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth}
body{background:var(--ink);color:var(--paper);font-family:'DM Mono',monospace;font-size:14px;overflow-x:hidden}
a{color:inherit;text-decoration:none}
.c{max-width:1200px;margin:0 auto;padding:0 1.5rem}
nav{position:sticky;top:0;z-index:100;background:rgba(8,8,16,.95);backdrop-filter:blur(14px);border-bottom:1px solid var(--border)}
.ni2{display:flex;align-items:center;justify-content:space-between;height:60px;gap:1rem}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;display:flex;align-items:center;gap:.4rem}
.logo-badge{background:var(--acid);color:var(--ink);padding:.06em .35em;font-size:.48rem;font-weight:700;letter-spacing:.05em;border-radius:3px}
.nl2{display:flex;gap:.08rem;align-items:center}
.nl2 a{font-size:.68rem;padding:.4rem .75rem;color:var(--mist);transition:color .18s;border-radius:3px}
.nl2 a:hover{color:var(--paper);background:rgba(255,255,255,.04)}
.na{display:flex;gap:.38rem;align-items:center}
.btn{padding:.52rem 1rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.68rem;letter-spacing:.03em;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.28rem;text-decoration:none;transition:all .18s;border-radius:3px}
.bp{background:var(--acid);color:var(--ink)}.bp:hover{transform:translateY(-2px);box-shadow:0 5px 18px rgba(184,255,60,.28)}
.bs{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--mist)}.bs:hover{border-color:rgba(255,255,255,.14);color:var(--paper)}
.hm{display:none;background:none;border:none;color:var(--paper);font-size:1.1rem;cursor:pointer;padding:.2rem}
footer{border-top:1px solid var(--border);padding:2.8rem 0 1.5rem;margin-top:5rem}
.fg2{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:2rem;margin-bottom:2.5rem}
.fc2{font-family:'Syne',sans-serif;font-weight:800;font-size:.88rem;margin-bottom:.6rem}
.fd{font-size:.65rem;color:var(--mist);line-height:1.8}
.fl2{display:flex;flex-direction:column;gap:.4rem}
.fl2 a{font-size:.64rem;color:var(--mist);transition:color .2s}
.fl2 a:hover{color:var(--acid)}
.fb{display:flex;justify-content:space-between;align-items:center;padding-top:1.5rem;border-top:1px solid var(--border);font-size:.58rem;color:var(--mist);flex-wrap:wrap;gap:.5rem}
.mob-nav{display:none;background:var(--card);border-bottom:1px solid var(--border);padding:1rem 1.5rem}
.mob-nav.open{display:block}
.mob-nav a{display:block;padding:.55rem 0;color:var(--mist);font-size:.74rem;border-bottom:1px solid rgba(255,255,255,.04)}
.mob-nav a:last-child{border-bottom:none;padding-top:.7rem;color:var(--acid)}
@media(max-width:800px){.nl2,.na .btn:not(.bp){display:none}.hm{display:block}.fg2{grid-template-columns:1fr 1fr}}
@media(max-width:550px){.fg2{grid-template-columns:1fr}}
</style>
</head><body>
<nav>
<div class="c ni2">
  <a href="<?=e(appUrl())?>/" class="logo"><?=e(appName())?><span class="logo-badge">PLATFORM</span></a>
  <div class="nl2">
    <?php foreach(['/services'=>'Services','/pricing'=>'Pricing','/tools'=>'SEO Tools','/blog'=>'Blog','/contact'=>'Contact'] as $u=>$l):?>
    <a href="<?=e(appUrl().$u)?>"><?=e($l)?></a>
    <?php endforeach;?>
  </div>
  <div class="na">
    <?php if(isLoggedIn()):?>
    <a href="<?=e(appUrl())?>/dashboard" class="btn bp">Dashboard →</a>
    <?php else:?>
    <a href="<?=e(appUrl())?>/login" class="btn bs">Login</a>
    <a href="<?=e(appUrl())?>/register" class="btn bp">Start Free →</a>
    <?php endif;?>
    <button class="hm" onclick="document.getElementById('mn').classList.toggle('open')">☰</button>
  </div>
</div>
<div class="mob-nav" id="mn">
  <?php foreach(['/services'=>'Services','/pricing'=>'Pricing','/tools'=>'SEO Tools','/blog'=>'Blog','/contact'=>'Contact'] as $u=>$l):?>
  <a href="<?=e(appUrl().$u)?>"><?=e($l)?></a>
  <?php endforeach;?>
  <a href="<?=e(appUrl())?>/<?=isLoggedIn()?'dashboard':'register'?>"><?=isLoggedIn()?'Dashboard →':'Start Free →'?></a>
</div>
</nav>
<?=$pageContent??''?>
<footer>
<div class="c">
  <div class="fg2">
    <div><div class="logo" style="margin-bottom:.8rem"><?=e(appName())?><span class="logo-badge">SEO PLATFORM</span></div>
      <p class="fd"><?=e(setting('site_tagline','Premium SEO & Backlink Platform'))?></p>
      <div style="display:flex;gap:.5rem;margin-top:.8rem">
        <?php foreach(['social_twitter'=>'𝕏','social_linkedin'=>'in','social_instagram'=>'ig'] as $k=>$ico):$u=setting($k);if($u):?><a href="<?=e($u)?>" style="width:28px;height:28px;border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;color:var(--mist);transition:all .2s" onmouseover="this.style.borderColor='var(--acid)'" onmouseout="this.style.borderColor='var(--border)'"><?=$ico?></a><?php endif;endforeach;?>
      </div>
    </div>
    <div><div class="fc2">Services</div><div class="fl2">
      <?php foreach(['/services'=>'Guest Posts','/services'=>'Niche Edits','/services'=>'Homepage Links','/services'=>'Link Packages'] as $u=>$l):?><a href="<?=e(appUrl().$u)?>"><?=e($l)?></a><?php endforeach;?>
    </div></div>
    <div><div class="fc2">Platform</div><div class="fl2">
      <?php foreach(['/tools'=>'SEO Tools (Free)','/ask-ai'=>'Ask AI','/pricing'=>'Pricing','/publishers'=>'Publishers','/blog'=>'Blog','/faq'=>'FAQ','/contact'=>'Contact'] as $u=>$l):?><a href="<?=e(appUrl().$u)?>"><?=e($l)?></a><?php endforeach;?>
    </div></div>
    <div><div class="fc2">Account</div><div class="fl2">
      <?php foreach(['/register'=>'Create Account','/login'=>'Sign In','/dashboard'=>'Dashboard','/orders/new'=>'New Order','/billing'=>'Upgrade Plan','/terms'=>'Terms','/privacy'=>'Privacy'] as $u=>$l):?><a href="<?=e(appUrl().$u)?>"><?=e($l)?></a><?php endforeach;?>
    </div></div>
  </div>
  <div class="fb">
    <span>© <?=date('Y')?> <?=e(appName())?> — <?=e(setting('footer_text','All rights reserved.'))?></span>
    <span>Built for SEOs, by SEOs</span>
  </div>
</div>
</footer>
</body></html>
