<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='rank_tracker';$pageTitle='Rank Tracker';$uid=(int)$_SESSION['user_id'];
$maxKw=(int)planLimit('max_keywords');$kwCount=(int)$pdo->query("SELECT COUNT(*) FROM `{$p}rank_tracking` WHERE user_id=$uid")->fetchColumn();
$projectId=(int)($_GET['project']??0);
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$a=$_POST['action']??'';
if($a==='add'){if($kwCount>=$maxKw&&$maxKw>0){$_SESSION['flash']='Keyword limit reached.';}else{$pdo->prepare("INSERT INTO `{$p}rank_tracking`(project_id,user_id,keyword,target_url,country,device,created_at)VALUES(?,?,?,?,?,?,NOW())")->execute([$projectId?:null,$uid,trim($_POST['keyword']),$_POST['target_url']??'',$_POST['country']??'US',$_POST['device']??'desktop']);}}
if($a==='delete'){$pdo->prepare("DELETE FROM `{$p}rank_tracking` WHERE id=? AND user_id=?")->execute([(int)$_POST['id'],$uid]);}
redirect('/rank-tracker'.($projectId?"?project=$projectId":''));}
$flash=$_SESSION['flash']??'';unset($_SESSION['flash']);
try{$projects=$pdo->query("SELECT * FROM `{$p}projects` WHERE user_id=$uid ORDER BY name")->fetchAll();$q="SELECT rt.*,pr.name AS pname,pr.domain FROM `{$p}rank_tracking` rt LEFT JOIN `{$p}projects` pr ON pr.id=rt.project_id WHERE rt.user_id=$uid";if($projectId)$q.=" AND rt.project_id=$projectId";$q.=" ORDER BY rt.created_at DESC";$keywords=$pdo->query($q)->fetchAll();}catch(Exception $e){$projects=$keywords=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Rank Tracker</div><div class="ps"><?=$kwCount?> / <?=$maxKw>0?$maxKw:'∞'?> keywords tracked</div></div>
<button class="btn bp" onclick="oM('rtmo')" <?=($maxKw>0&&$kwCount>=$maxKw)?'disabled title="Keyword limit reached"':''?>>+ Add Keyword</button></div>
<?php if($flash):?><div class="al al-e">⚠ <?=e($flash)?> <a href="<?=e(appUrl())?>/billing" style="color:var(--acid)">Upgrade →</a></div><?php endif;?>
<?php if(!empty($projects)):?>
<div class="ftabs">
  <a href="<?=e(appUrl())?>/rank-tracker" class="ftab <?=$projectId===0?'active':''?>">All Keywords</a>
  <?php foreach($projects as $pr):?><a href="?project=<?=(int)$pr['id']?>" class="ftab <?=$projectId===$pr['id']?'active':''?>"><?=e($pr['name'])?></a><?php endforeach;?>
</div>
<?php endif;?>
<div class="panel"><div class="tw"><table><thead><tr><th>KEYWORD</th><th>PROJECT</th><th>TARGET URL</th><th>RANK</th><th>CHANGE</th><th>DEVICE</th><th>COUNTRY</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($keywords as $kw):$rank=$kw['current_rank'];$prev=$kw['prev_rank'];$diff=$prev&&$rank?$prev-$rank:null;?><tr>
<td class="tdp"><?=e($kw['keyword'])?></td>
<td class="tdm"><?=e($kw['pname']??$kw['domain']??'—')?></td>
<td class="tdc tdm"><?=e($kw['target_url']??'—')?></td>
<td style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:<?=$rank?($rank<=3?'var(--green)':$rank<=10?'var(--acid)':'var(--mist)'):'var(--mist)'?>"><?=$rank??'N/A'?></td>
<td><?php if($diff!==null):?><span style="color:<?=$diff>0?'var(--green)':($diff<0?'var(--red)':'var(--mist)')?>;font-weight:700"><?=$diff>0?'↑'.$diff:($diff<0?'↓'.abs($diff):'—')?></span><?php else:?><span class="tdm">—</span><?php endif;?></td>
<td class="tdm"><?=e($kw['device'])?></td>
<td class="tdm"><?=e($kw['country'])?></td>
<td><div class="tda">
<form method="post" style="display:inline"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$kw['id']?>"><button class="btn bd bxs" onclick="return confirm('Remove keyword?')">✕</button></form>
</div></td>
</tr><?php endforeach;if(empty($keywords)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No keywords yet — add some to track rankings</td></tr><?php endif;?>
</tbody></table></div></div>
<div class="al al-i" style="font-size:.65rem">💡 Rank positions are updated daily. For fresh data, check back tomorrow after the daily crawl.</div>
<?php $pageContent=ob_get_clean();
$pOpts='<option value="">No Project</option>';foreach($projects as $pr)$pOpts.='<option value="'.(int)$pr['id'].'">'.e($pr['name']).'</option>';
$modals='<div class="mo" id="rtmo"><div class="mw"><div class="mh"><div class="mt">Add Keyword</div><button class="mc" onclick="cM(\'rtmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="add"><div class="fg"><label class="fl">KEYWORD *</label><input type="text" name="keyword" class="fc" placeholder="e.g. buy backlinks online" required autofocus></div><div class="fg"><label class="fl">TARGET URL</label><input type="url" name="target_url" class="fc" placeholder="https://yoursite.com/page"></div><div class="fg"><label class="fl">PROJECT</label><select name="project_id" class="fc">'.$pOpts.'</select></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">COUNTRY</label><select name="country" class="fc"><option value="US">US</option><option value="GB">GB</option><option value="AU">AU</option><option value="CA">CA</option><option value="IN">IN</option><option value="BD">BD</option></select></div><div class="fg"><label class="fl">DEVICE</label><select name="device" class="fc"><option value="desktop">Desktop</option><option value="mobile">Mobile</option></select></div></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'rtmo\')">Cancel</button><button type="submit" class="btn bp">Add Keyword</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/client_wrap.php';
