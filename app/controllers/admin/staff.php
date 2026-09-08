<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='staff';$pageTitle='Staff';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$a=$_POST['action']??'';if($a==='save'){$hash=password_hash($_POST['password'],PASSWORD_BCRYPT,['cost'=>12]);$pdo->prepare("INSERT INTO `{$p}users`(name,email,password,role,plan,status,created_at)VALUES(?,?,?,?,?,1,NOW())")->execute([$_POST['name'],$_POST['email'],$hash,$_POST['role'],'agency']);redirect('/admin/staff');}if($a==='remove'){$pdo->prepare("UPDATE `{$p}users` SET status=0 WHERE id=? AND role IN('staff')")->execute([(int)$_POST['id']]);redirect('/admin/staff');}}
try{$staff=$pdo->query("SELECT * FROM `{$p}users` WHERE role IN('admin','staff') AND status=1 ORDER BY role,name")->fetchAll();}catch(Exception $e){$staff=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Staff</div></div><button class="btn bp" onclick="oM('stmo')">+ Add Staff</button></div>
<div class="panel"><div class="tw"><table><thead><tr><th>NAME</th><th>EMAIL</th><th>ROLE</th><th>JOINED</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($staff as $st):?><tr>
<td class="tdp"><?=e($st['name'])?></td><td><?=e($st['email'])?></td><td><?=badge($st['role'])?></td>
<td class="tdm"><?=formatDate($st['created_at'])?></td>
<td><?php if($st['id']!==$user['id']):?><form method="post" style="display:inline"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?=(int)$st['id']?>"><button class="btn bd bxs" onclick="return confirm('Remove?')">Remove</button></form><?php else:?><span class="tdm">YOU</span><?php endif;?></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="stmo"><div class="mw"><div class="mh"><div class="mt">Add Staff</div><button class="mc" onclick="cM(\'stmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="save"><div class="fg"><label class="fl">FULL NAME</label><input type="text" name="name" class="fc" required></div><div class="fg"><label class="fl">EMAIL</label><input type="email" name="email" class="fc" required></div><div class="fg"><label class="fl">ROLE</label><select name="role" class="fc"><option value="staff">Staff</option><option value="admin">Admin</option></select></div><div class="fg"><label class="fl">PASSWORD</label><input type="password" name="password" class="fc" required></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'stmo\')">Cancel</button><button type="submit" class="btn bp">Add Staff</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
