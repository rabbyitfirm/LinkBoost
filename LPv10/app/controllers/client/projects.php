<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='projects';$pageTitle='Projects';$uid=(int)$_SESSION['user_id'];
$maxProjects=(int)planLimit('max_projects');$count=(int)$pdo->query("SELECT COUNT(*) FROM `{$p}projects` WHERE user_id=$uid")->fetchColumn();
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$a=$_POST['action']??'';$id=(int)($_POST['id']??0);
if($a==='create'){if($count>=$maxProjects&&$maxProjects>0){$_SESSION['flash']='Project limit reached. Upgrade your plan.';}else{$domain=trim(str_replace(['https://','http://','www.'],'',rtrim($_POST['domain']??'','/')));$pdo->prepare("INSERT INTO `{$p}projects`(user_id,name,domain,country,language,status,created_at)VALUES(?,?,?,?,?,1,NOW())")->execute([$uid,$_POST['name'],$domain,$_POST['country']??'US',$_POST['language']??'en']);}}
if($a==='delete'){$pdo->prepare("DELETE FROM `{$p}projects` WHERE id=? AND user_id=?")->execute([$id,$uid]);}
redirect('/projects');}
$flash=$_SESSION['flash']??'';unset($_SESSION['flash']);
try{$projects=$pdo->query("SELECT pr.*,(SELECT COUNT(*) FROM `{$p}rank_tracking` rt WHERE rt.project_id=pr.id) AS kw_count,(SELECT COUNT(*) FROM `{$p}audits` au WHERE au.project_id=pr.id) AS audit_count FROM `{$p}projects` pr WHERE pr.user_id=$uid ORDER BY pr.created_at DESC")->fetchAll();}catch(Exception $e){$projects=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Projects</div><div class="ps"><?=$count?> / <?=$maxProjects>0?$maxProjects:'∞'?> used</div></div>
<button class="btn bp" onclick="oM('prmo')" <?=($maxProjects>0&&$count>=$maxProjects)?'disabled title="Limit reached"':''?>>+ New Project</button></div>
<?php if($flash):?><div class="al al-e">⚠ <?=e($flash)?> <a href="<?=e(appUrl())?>/billing" style="color:var(--acid)">Upgrade →</a></div><?php endif;?>
<?php if(empty($projects)):?>
<div style="text-align:center;padding:3rem;color:var(--mist)">No projects yet. Create one to start tracking rankings and running audits.</div>
<?php else:?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:.85rem">
<?php foreach($projects as $pr):?>
<div class="panel">
  <div style="padding:.88rem"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem">
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem"><?=e($pr['name'])?></div>
    <form method="post" style="display:inline" onsubmit="return confirm('Delete project?')"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$pr['id']?>"><button class="btn bd bxs">✕</button></form>
  </div>
  <div style="font-size:.68rem;color:var(--acid);margin-bottom:.6rem">🌐 <?=e($pr['domain'])?></div>
  <div style="display:flex;gap:.7rem;font-size:.62rem;color:var(--mist)">
    <span>📈 <?=(int)$pr['kw_count']?> keywords</span>
    <span>🔍 <?=(int)$pr['audit_count']?> audits</span>
    <span><?=e($pr['country'])?></span>
  </div></div>
  <div style="padding:.6rem .88rem;border-top:1px solid var(--border);display:flex;gap:.4rem">
    <a href="<?=e(appUrl())?>/rank-tracker?project=<?=(int)$pr['id']?>" class="btn bs bxs" style="flex:1;justify-content:center">📈 Ranks</a>
    <a href="<?=e(appUrl())?>/site-audit?domain=<?=urlencode($pr['domain'])?>" class="btn bs bxs" style="flex:1;justify-content:center">🔍 Audit</a>
  </div>
</div>
<?php endforeach;?></div>
<?php endif;?>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="prmo"><div class="mw"><div class="mh"><div class="mt">New Project</div><button class="mc" onclick="cM(\'prmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="create"><div class="fg"><label class="fl">PROJECT NAME</label><input type="text" name="name" class="fc" placeholder="My Website" required></div><div class="fg"><label class="fl">DOMAIN</label><input type="text" name="domain" class="fc" placeholder="example.com" required></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">COUNTRY</label><select name="country" class="fc"><option value="US">🇺🇸 United States</option><option value="GB">🇬🇧 United Kingdom</option><option value="AU">🇦🇺 Australia</option><option value="CA">🇨🇦 Canada</option><option value="IN">🇮🇳 India</option><option value="BD">🇧🇩 Bangladesh</option><option value="PK">🇵🇰 Pakistan</option><option value="TR">🇹🇷 Turkey</option></select></div><div class="fg"><label class="fl">LANGUAGE</label><select name="language" class="fc"><option value="en">English</option><option value="tr">Turkish</option><option value="bn">Bengali</option><option value="ur">Urdu</option></select></div></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'prmo\')">Cancel</button><button type="submit" class="btn bp">Create Project</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/client_wrap.php';
