<?php ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register — <?=e(appName())?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php include LB_ROOT.'/app/views/layouts/auth_css.php'; ?>
</head><body><div class="aw">
<a href="<?=e(appUrl())?>/" class="ab"><div class="al2"><?=e(appName())?> <em>PLATFORM</em></div></a>
<div class="ac">
  <div class="at">Create account</div>
  <div class="as">Join thousands of SEOs — start free, upgrade anytime</div>
  <?php if($error??''):?><div class="al al-e">✗ <?=e($error)?></div><?php endif;?>
  <a href="<?=e(appUrl())?>/auth/google" class="gbtn"><svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg> Sign up with Google</a>
  <div class="div">or register with email</div>
  <form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
    <div class="fg"><label class="fl">FULL NAME</label><input type="text" name="name" class="fc" placeholder="Your Name" required autofocus></div>
    <div class="fg"><label class="fl">EMAIL</label><input type="email" name="email" class="fc" placeholder="you@email.com" required></div>
    <div class="fg"><label class="fl">PASSWORD</label><input type="password" name="password" class="fc" placeholder="Min 8 characters" required oninput="checkPsw(this.value)"><div class="pwb"><div class="pwf" id="pf"></div></div></div>
    <div class="fg"><label class="fl">CONFIRM PASSWORD</label><input type="password" name="password2" class="fc" placeholder="Repeat password" required></div>
    <button type="submit" class="sb2">Create Free Account →</button>
  </form>
  <div class="alinks"><span style="font-size:.6rem;color:var(--mist)">🆓 Free plan — no credit card needed</span><a href="<?=e(appUrl())?>/login">Sign in</a></div>
</div></div></body></html>
