<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='customizer';$pageTitle='Customizer';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){foreach($_POST as $k=>$v){if($k==='_token')continue;$pdo->prepare("INSERT INTO `{$p}settings`(`key`,`value`,`group`)VALUES(?,?,'theme') ON DUPLICATE KEY UPDATE `value`=?")->execute([$k,$v,$v]);}$success='Saved!';}
try{$cfg=$pdo->query("SELECT `key`,`value` FROM `{$p}settings`")->fetchAll(PDO::FETCH_KEY_PAIR);}catch(Exception $e){$cfg=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Site Customizer</div><div class="ps">Edit homepage content and colors</div></div><?php if($success):?><span class="bdg b-success">✓ <?=e($success)?></span><?php endif;?></div>
<div style="display:grid;grid-template-columns:320px 1fr;gap:.9rem;align-items:start">
<div>
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">🏷 Site Identity</div></div><div class="pnb">
<div class="fg"><label class="fl">SITE NAME</label><input type="text" name="site_name" class="fc" value="<?=e($cfg['site_name']??appName())?>"></div>
<div class="fg"><label class="fl">TAGLINE</label><input type="text" name="site_tagline" class="fc" value="<?=e($cfg['site_tagline']??'')?>"></div>
</div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">🎨 Colors</div></div><div class="pnb">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
<div class="fg"><label class="fl">ACCENT</label><input type="color" name="accent_color" class="fc" value="<?=e($cfg['accent_color']??'#b8ff3c')?>" style="height:42px;cursor:pointer" oninput="updPrev()"></div>
<div class="fg"><label class="fl">BACKGROUND</label><input type="color" name="bg_color" class="fc" value="<?=e($cfg['bg_color']??'#080810')?>" style="height:42px;cursor:pointer" oninput="updPrev()"></div>
</div></div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">🦸 Hero</div></div><div class="pnb">
<div class="fg"><label class="fl">HEADLINE</label><input type="text" name="hero_title" id="ht" class="fc" value="<?=e($cfg['hero_title']??'')?>" oninput="updPrev()"></div>
<div class="fg"><label class="fl">SUB</label><textarea name="hero_sub" id="hs" class="fc" rows="2" oninput="updPrev()"><?=e($cfg['hero_sub']??'')?></textarea></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
<div class="fg"><label class="fl">CTA 1</label><input type="text" name="hero_cta" id="hc1" class="fc" value="<?=e($cfg['hero_cta']??'')?>" oninput="updPrev()"></div>
<div class="fg"><label class="fl">CTA 2</label><input type="text" name="hero_cta2" id="hc2" class="fc" value="<?=e($cfg['hero_cta2']??'')?>" oninput="updPrev()"></div>
</div></div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">📊 Stats</div></div><div class="pnb">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
<?php foreach([[1,'22+','SEO TOOLS'],[2,'12K+','Publisher Sites'],[3,'98%','Delivery Rate'],[4,'2,400+','Happy Clients']] as [$n,$dv,$dl]):?>
<div class="fg"><label class="fl">STAT <?=$n?> VALUE</label><input type="text" name="stat<?=$n?>val" class="fc" value="<?=e($cfg['stat'.$n.'val']??$dv)?>" oninput="updPrev()"></div>
<div class="fg"><label class="fl">STAT <?=$n?> LABEL</label><input type="text" name="stat<?=$n?>lbl" class="fc" value="<?=e($cfg['stat'.$n.'lbl']??$dl)?>" oninput="updPrev()"></div>
<?php endforeach;?></div></div></div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">📞 Contact</div></div><div class="pnb">
<div class="fg"><label class="fl">EMAIL</label><input type="email" name="contact_email" class="fc" value="<?=e($cfg['contact_email']??'')?>"></div>
<div class="fg"><label class="fl">PHONE</label><input type="text" name="contact_phone" class="fc" value="<?=e($cfg['contact_phone']??'')?>"></div>
<div class="fg"><label class="fl">ADDRESS</label><textarea name="contact_address" class="fc" rows="2"><?=e($cfg['contact_address']??'')?></textarea></div>
<?php foreach(['social_twitter'=>'Twitter/X','social_linkedin'=>'LinkedIn','social_instagram'=>'Instagram'] as $k=>$l):?>
<div class="fg"><label class="fl"><?=e($l)?> URL</label><input type="url" name="<?=$k?>" class="fc" value="<?=e($cfg[$k]??'')?>" placeholder="https://..."></div>
<?php endforeach;?></div></div>
<button type="submit" class="btn bp" style="width:100%;justify-content:center">💾 Save & Publish</button>
</form>
</div>
<div class="panel" style="position:sticky;top:50px"><div class="pnh"><div class="pnt">Live Preview</div>
<div class="pna"><button class="btn bs bxs" onclick="document.getElementById('pf2').style.width='100%'">Desktop</button><button class="btn bs bxs" onclick="document.getElementById('pf2').style.width='768px'">Tablet</button><button class="btn bs bxs" onclick="document.getElementById('pf2').style.width='375px'">Mobile</button></div></div>
<div style="background:#050508;padding:.6rem;display:flex;justify-content:center;min-height:500px">
<iframe id="pf2" style="border:none;width:100%;height:500px;transition:width .3s"></iframe>
</div></div>
</div>
<?php $pageContent=ob_get_clean();
$js='<script>
function g(n){var e=document.querySelector("[name="+n+"]");return e?e.value:"";}
function updPrev(){
var ac=document.querySelector("[name=accent_color]")?.value||"#b8ff3c";
var bg=document.querySelector("[name=bg_color]")?.value||"#080810";
var html=`<!DOCTYPE html><html><head><meta charset="UTF-8"><style>*{margin:0;padding:0;box-sizing:border-box}body{background:${bg};color:#f0ede6;font-family:monospace}
nav{display:flex;align-items:center;justify-content:space-between;padding:.7rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);background:${bg}}
.logo{font-weight:800;font-size:.9rem}.logo em{background:${ac};color:${bg};padding:.08em .32em;font-size:.46rem;font-style:normal}
.gbtn{background:${ac};color:${bg};padding:.4rem 1rem;font-size:.68rem;font-weight:700;border:none;cursor:pointer;border-radius:2px}
.hero{padding:3rem 1.5rem 2.5rem;text-align:center}h1{font-size:1.8rem;font-weight:800;line-height:1.1;margin-bottom:.7rem}
.sub{font-size:.75rem;color:#8888aa;margin-bottom:1.3rem;line-height:1.7}
.stats{display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid rgba(255,255,255,.07);padding:1.5rem}
.sv{font-size:1.4rem;font-weight:800;color:${ac};text-align:center}.sl{font-size:.5rem;color:#8888aa;text-align:center;letter-spacing:.1em;text-transform:uppercase;margin-top:.2rem}
</style></head><body>
<nav><div class="logo">${g("site_name")||"LinkParty"} <em>PLATFORM</em></div><div class="gbtn">${g("hero_cta")||"Get Started"}</div></nav>
<div class="hero"><h1>${g("hero_title")}</h1><p class="sub">${g("hero_sub")}</p>
<div style="display:flex;gap:.7rem;justify-content:center"><div class="gbtn">${g("hero_cta")}</div><div style="border:1px solid rgba(255,255,255,.15);padding:.38rem .9rem;font-size:.68rem;cursor:pointer;border-radius:2px;color:#8888aa">${g("hero_cta2")}</div></div>
</div>
<div class="stats">${[1,2,3,4].map(n=>`<div><div class="sv">${g("stat"+n+"val")}</div><div class="sl">${g("stat"+n+"lbl")}</div></div>`).join("")}</div>
</body></html>`;
var b=new Blob([html],{type:"text/html"});document.getElementById("pf2").src=URL.createObjectURL(b);}
document.addEventListener("DOMContentLoaded",updPrev);
</script>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
