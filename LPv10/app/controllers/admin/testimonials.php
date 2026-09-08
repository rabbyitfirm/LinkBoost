<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='testimonials';$pageTitle='Testimonials';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$a=$_POST['action']??'';$id=(int)($_POST['id']??0);
if($a==='save'){$d=[$_POST['name'],$_POST['role'],$_POST['avatar']??'',(int)$_POST['rating'],$_POST['content'],(int)$_POST['is_featured'],(int)$_POST['sort_order']];
if($id)$pdo->prepare("UPDATE `{$p}testimonials` SET name=?,role=?,avatar=?,rating=?,content=?,is_featured=?,sort_order=? WHERE id=?")->execute(array_merge($d,[$id]));
else $pdo->prepare("INSERT INTO `{$p}testimonials`(name,role,avatar,rating,content,is_featured,sort_order,created_at)VALUES(?,?,?,?,?,?,?,NOW())")->execute($d);
redirect('/admin/testimonials');}
if($a==='delete'){$pdo->prepare("DELETE FROM `{$p}testimonials` WHERE id=?")->execute([$id]);redirect('/admin/testimonials');}}
try{$tms=$pdo->query("SELECT * FROM `{$p}testimonials` ORDER BY sort_order,id")->fetchAll();}catch(Exception $e){$tms=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Testimonials</div></div><button class="btn bp" onclick="document.getElementById('tmid').value=0;document.getElementById('tmform').reset();oM('tmmo')">+ Add</button></div>
<div class="panel"><div class="tw"><table><thead><tr><th>NAME</th><th>ROLE</th><th>RATING</th><th>FEATURED</th><th>CONTENT</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($tms as $t):?><tr>
<td class="tdp"><?=e($t['name'])?></td><td class="tdm"><?=e($t['role']??'')?></td>
<td><?=str_repeat('★',(int)$t['rating'])?></td>
<td><?=$t['is_featured']?'<span class="bdg b-success">Yes</span>':'—'?></td>
<td class="tdc"><?=e(substr($t['content']??'',0,60))?>...</td>
<td><div class="tda"><button class="btn bs bxs" onclick="editTm(<?=htmlspecialchars(json_encode($t),ENT_QUOTES)?>)">Edit</button>
<form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$t['id']?>"><button class="btn bd bxs">✕</button></form>
</div></td></tr><?php endforeach;?></tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="tmmo"><div class="mw"><div class="mh"><div class="mt">Testimonial</div><button class="mc" onclick="cM(\'tmmo\')">✕</button></div><div class="mb"><form method="post" id="tmform"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="tmid" value="0"><div class="fg"><label class="fl">NAME</label><input type="text" name="name" id="tmname" class="fc" required></div><div class="fg"><label class="fl">ROLE / COMPANY</label><input type="text" name="role" id="tmrole" class="fc"></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">RATING (1-5)</label><input type="number" name="rating" id="tmrating" class="fc" min="1" max="5" value="5"></div><div class="fg"><label class="fl">SORT ORDER</label><input type="number" name="sort_order" id="tmso" class="fc" value="0"></div></div><div class="fg"><label class="fl">AVATAR URL</label><input type="url" name="avatar" id="tmav" class="fc" placeholder="https://..."></div><div class="fg"><label class="fl">CONTENT</label><textarea name="content" id="tmcont" class="fc" rows="4" required></textarea></div><div class="fg"><label class="fl">FEATURED ON HOMEPAGE</label><select name="is_featured" id="tmfeat" class="fc"><option value="1">Yes</option><option value="0">No</option></select></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'tmmo\')">Cancel</button><button type="submit" class="btn bp">Save</button></div></form></div></div></div>';
$js='<script>function editTm(t){document.getElementById("tmid").value=t.id;document.getElementById("tmname").value=t.name||"";document.getElementById("tmrole").value=t.role||"";document.getElementById("tmrating").value=t.rating||5;document.getElementById("tmso").value=t.sort_order||0;document.getElementById("tmav").value=t.avatar||"";document.getElementById("tmcont").value=t.content||"";document.getElementById("tmfeat").value=t.is_featured||1;oM("tmmo");}</script>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
