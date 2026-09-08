<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>New Password — <?=e(appName())?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php include LB_ROOT.'/app/views/layouts/auth_css.php'; ?>
</head><body><div class="aw">
<a href="<?=e(appUrl())?>/" class="ab"><div class="al2"><?=e(appName())?></div></a>
<div class="ac">
  <div class="at">Set new password</div><div class="as">Choose a strong password</div>
  <?php if($error??''):?><div class="al al-e">✗ <?=e($error)?></div><?php endif;?>
  <?php if($success??''):?><div class="al al-s">✓ <?=$success?></div>
  <?php else:?>
  <form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="token" value="<?=e($token??'')?>">
    <div class="fg"><label class="fl">NEW PASSWORD</label><input type="password" name="password" class="fc" placeholder="Min 8 characters" required oninput="checkPsw(this.value)"><div class="pwb"><div class="pwf" id="pf"></div></div></div>
    <div class="fg"><label class="fl">CONFIRM</label><input type="password" name="password2" class="fc" placeholder="Repeat" required></div>
    <button type="submit" class="sb2">Reset Password →</button>
  </form>
  <?php endif;?>
  <div class="alinks"><a href="<?=e(appUrl())?>/login">← Login</a></div>
</div></div></body></html>
