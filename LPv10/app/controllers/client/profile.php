<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='profile';$pageTitle='Profile';$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $name=trim($_POST['name']??'');$phone=trim($_POST['phone']??'');$pass=$_POST['password']??'';$pass2=$_POST['password2']??'';
    if(!$name)$error='Name required.';
    elseif($pass&&strlen($pass)<8)$error='Password min 8 chars.';
    elseif($pass&&$pass!==$pass2)$error='Passwords do not match.';
    else{$sets="name=?,phone=?,updated_at=NOW()";$vals=[$name,$phone];if($pass){$sets.=",password=?";$vals[]=password_hash($pass,PASSWORD_BCRYPT,['cost'=>12]);}$pdo->prepare("UPDATE `{$p}users` SET $sets WHERE id=?")->execute(array_merge($vals,[(int)$_SESSION['user_id']]));$_SESSION['user_name']=$name;$success='Profile updated!';}
    $s=$pdo->prepare("SELECT * FROM `{$p}users` WHERE id=? LIMIT 1");$s->execute([(int)$_SESSION['user_id']]);$user=$s->fetch();
}
ob_start();?>
<div class="ph"><div><div class="pt">Profile</div></div></div>
<?php if($error):?><div class="al al-e">✗ <?=e($error)?></div><?php endif;?>
<?php if($success):?><div class="al al-s">✓ <?=e($success)?></div><?php endif;?>
<div style="display:grid;grid-template-columns:320px 1fr;gap:.9rem;align-items:start">
<div class="panel"><div class="pnb" style="text-align:center">
  <?php if($user['avatar']??''):?><img src="<?=e($user['avatar'])?>" style="width:72px;height:72px;border-radius:50%;object-fit:cover;margin-bottom:.8rem">
  <?php else:?><div style="width:72px;height:72px;border-radius:50%;background:var(--acid);display:flex;align-items:center;justify-content:center;font-family:Syne,sans-serif;font-weight:800;font-size:1.5rem;color:var(--ink);margin:0 auto .8rem"><?=strtoupper(substr($user['name']??'U',0,1))?></div><?php endif;?>
  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:.2rem"><?=e($user['name']??'')?></div>
  <div style="font-size:.65rem;color:var(--mist);margin-bottom:.6rem"><?=e($user['email']??'')?></div>
  <?=planBadge($user['plan']??'free')?>
  <div style="margin-top:.9rem;padding-top:.9rem;border-top:1px solid var(--border);font-size:.62rem;color:var(--mist);display:flex;flex-direction:column;gap:.35rem;text-align:left">
    <div style="display:flex;justify-content:space-between"><span>Member since</span><span><?=formatDate($user['created_at'],'M Y')?></span></div>
    <div style="display:flex;justify-content:space-between"><span>Last login</span><span><?=formatDate($user['last_login']??'','M j, Y')?></span></div>
    <div style="display:flex;justify-content:space-between"><span>Account ID</span><span>#<?=(int)$user['id']?></span></div>
  </div>
</div></div>
<div class="panel"><div class="pnh"><div class="pnt">Edit Profile</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">FULL NAME</label><input type="text" name="name" class="fc" value="<?=e($user['name']??'')?>" required></div>
<div class="fg"><label class="fl">EMAIL <span style="color:var(--mist);font-size:.52rem">(cannot change)</span></label><input type="email" class="fc" value="<?=e($user['email']??'')?>" readonly style="opacity:.5"></div>
<div class="fg"><label class="fl">PHONE</label><input type="text" name="phone" class="fc" value="<?=e($user['phone']??'')?>" placeholder="+1 234 567 8900"></div>
<div class="fsec">API KEY</div>
<?php if($user['api_key']??''):?>
<div class="fg"><label class="fl">YOUR API KEY <span style="font-size:.52rem;color:var(--mist)">(Pro/Agency plans)</span></label>
<div style="display:flex;gap:.4rem"><input type="text" class="fc" value="<?=e($user['api_key']??'')?>" readonly id="apiKey" onclick="this.select()" style="font-size:.64rem">
<button onclick="navigator.clipboard.writeText(document.getElementById('apiKey').value);this.textContent='✓'" class="btn bs bsm" style="flex-shrink:0">Copy</button></div>
<div class="fh">Use header: X-API-Key: YOUR_KEY | Endpoint: <?=e(appUrl())?>/api/v1/orders</div></div>
<?php elseif(planLimit('api_access')):?>
<?php $ak=genApiKey();db()->prepare("UPDATE `".pfx()."users` SET api_key=? WHERE id=?")->execute([$ak,(int)$_SESSION['user_id']]);?>
<div class="al al-s">✓ API key generated. Refresh the page to see it.</div>
<?php else:?>
<div class="al al-i" style="font-size:.66rem">API access requires <a href="<?=e(appUrl())?>/billing" style="color:var(--acid)">Pro or Agency plan →</a></div>
<?php endif;?>
<div class="fsec">CHANGE PASSWORD</div>
<div class="fg"><label class="fl">NEW PASSWORD <span style="color:var(--mist);font-size:.5rem">(leave blank to keep)</span></label><input type="password" name="password" class="fc" placeholder="Min 8 characters"></div>
<div class="fg"><label class="fl">CONFIRM PASSWORD</label><input type="password" name="password2" class="fc" placeholder="Repeat password"></div>
<button type="submit" class="btn bp">Save Changes →</button>
</form>
</div></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
