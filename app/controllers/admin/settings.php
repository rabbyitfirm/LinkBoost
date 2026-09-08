<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='settings';$pageTitle='Settings';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){foreach($_POST as $k=>$v){if($k==='_token')continue;$pdo->prepare("INSERT INTO `{$p}settings`(`key`,`value`,`group`)VALUES(?,?,'general') ON DUPLICATE KEY UPDATE `value`=?")->execute([$k,$v,$v]);}$success='Settings saved!';}
try{$cfg=$pdo->query("SELECT `key`,`value` FROM `{$p}settings`")->fetchAll(PDO::FETCH_KEY_PAIR);}catch(Exception $e){$cfg=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Settings</div></div><?php if($success):?><span class="bdg b-success">✓ Saved</span><?php endif;?></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
<div style="display:flex;flex-direction:column;gap:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">⚙ General</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">SITE NAME</label><input type="text" name="site_name" class="fc" value="<?=e($cfg['site_name']??appName())?>"></div>
<div class="fg"><label class="fl">CURRENCY</label><select name="currency" class="fc"><?php foreach(['USD'=>'USD — $','EUR'=>'EUR — €','GBP'=>'GBP — £','BDT'=>'BDT — ৳','PKR'=>'PKR — ₨','INR'=>'INR — ₹'] as $k=>$v):?><option value="<?=e($k)?>" <?=($cfg['currency']??'USD')===$k?'selected':''?>><?=e($v)?></option><?php endforeach;?></select></div>
<div class="fg"><label class="fl">ORDER PREFIX</label><input type="text" name="order_prefix" class="fc" value="<?=e($cfg['order_prefix']??'ORD-')?>"></div>
<button type="submit" class="btn bp bsm">Save General</button>
</form></div></div>
<div class="panel"><div class="pnh"><div class="pnt">🔑 Google OAuth</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">GOOGLE CLIENT ID</label><input type="text" name="google_client_id" class="fc" value="<?=e($cfg['google_client_id']??'')?>" placeholder="123...apps.googleusercontent.com"></div>
<div class="fg"><label class="fl">GOOGLE CLIENT SECRET</label><input type="password" name="google_client_secret" class="fc" placeholder="Leave blank to keep current"></div>
</div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">📊 Analytics & Tracking</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">GOOGLE ANALYTICS 4 (Measurement ID)</label><input type="text" name="ga4_id" class="fc" value="<?=e($cfg['ga4_id']??'')?>" placeholder="G-XXXXXXXXXX"></div>
<div class="fg"><label class="fl">FACEBOOK PIXEL ID</label><input type="text" name="fb_pixel" class="fc" value="<?=e($cfg['fb_pixel']??'')?>" placeholder="123456789012345"></div>
<div class="fg"><label class="fl">GOOGLE TAG MANAGER ID</label><input type="text" name="gtm_id" class="fc" value="<?=e($cfg['gtm_id']??'')?>" placeholder="GTM-XXXXXXX"></div>
<div class="fg"><label class="fl">CUSTOM HEAD CODE (scripts, meta tags)</label><textarea name="custom_head" class="fc" rows="3" placeholder="<script>...</script>"><?=e($cfg['custom_head']??'')?></textarea></div>
<button type="submit" class="btn bp bsm">Save Analytics</button>
</form></div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">🔑 Google OAuth</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="al al-i" style="font-size:.62rem">Redirect URI to add in Google Console:<br><strong><?=e(appUrl())?>/auth/google/callback</strong></div>
<button type="submit" class="btn bp bsm">Save Google</button>
</form></div></div>
</div>
<div style="display:flex;flex-direction:column;gap:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">💳 Stripe</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">STRIPE PUBLIC KEY</label><input type="text" name="stripe_pub_key" class="fc" value="<?=e($cfg['stripe_pub_key']??'')?>" placeholder="pk_live_..."></div>
<div class="fg"><label class="fl">STRIPE SECRET KEY</label><input type="password" name="stripe_secret_key" class="fc" placeholder="sk_live_...">
<div class="fg"><label class="fl">STRIPE WEBHOOK SECRET</label><input type="password" name="stripe_webhook_secret" class="fc" placeholder="whsec_...">
<div class="fh">Webhook URL: <?=e(appUrl())?>/webhook/stripe</div></div></div>
<div class="fg"><label class="fl">STRIPE WEBHOOK SECRET <span style="font-size:.52rem;color:var(--mist)">(from Stripe Dashboard → Webhooks)</span></label><input type="password" name="stripe_webhook_secret" class="fc" placeholder="whsec_..."></div>
<div class="al al-i" style="font-size:.62rem">Webhook URL: <strong><?=e(appUrl())?>/webhook/stripe</strong><br>Add this in Stripe Dashboard → Developers → Webhooks</div>
<button type="submit" class="btn bp bsm">Save Stripe</button>
</form></div></div>
<div class="panel"><div class="pnh"><div class="pnt">📊 System Info</div></div><div class="pnb">
<div style="display:flex;flex-direction:column;gap:.45rem;font-size:.68rem">
<div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">PHP Version</span><span style="color:var(--acid)"><?=phpversion()?></span></div>
<div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">App URL</span><span><?=e(appUrl())?></span></div>
<div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">DB Prefix</span><span><?=e(pfx())?></span></div>
<div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Server</span><span><?=e($_SERVER['SERVER_SOFTWARE']??'LiteSpeed')?></span></div>
<div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">DB</span><span><?=e(env('DB_DATABASE'))?></span></div>
</div></div></div>
</div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
