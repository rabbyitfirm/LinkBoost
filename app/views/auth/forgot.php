<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password — <?=e(appName())?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php include LB_ROOT.'/app/views/layouts/auth_css.php'; ?>
</head><body><div class="aw">
<a href="<?=e(appUrl())?>/" class="ab"><div class="al2"><?=e(appName())?></div></a>
<div class="ac">
  <div class="at">Reset password</div><div class="as">Enter your email to receive a reset link</div>
  <?php if($error??''):?><div class="al al-e">✗ <?=e($error)?></div><?php endif;?>
  <?php if($success??''):?><div class="al al-s">✓ <?=e($success)?></div>
  <?php else:?>
  <form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
    <div class="fg"><label class="fl">EMAIL</label><input type="email" name="email" class="fc" placeholder="you@email.com" required autofocus></div>
    <button type="submit" class="sb2">Send Reset Link →</button>
  </form>
  <?php endif;?>
  <div class="alinks"><a href="<?=e(appUrl())?>/login">← Back to login</a></div>
</div></div></body></html>
