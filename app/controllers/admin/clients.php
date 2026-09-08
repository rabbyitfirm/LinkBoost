<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='clients';$pageTitle='Clients';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $a=$_POST['action']??'';$id=(int)($_POST['id']??0);
    if($a==='save'){if($id){$sets="name=?,email=?,phone=?,plan=?,status=?,updated_at=NOW()";$vals=[$_POST['name'],$_POST['email'],$_POST['phone']??'',$_POST['plan'],(int)$_POST['status']];if(!empty($_POST['password'])){$sets.=',password=?';$vals[]=password_hash($_POST['password'],PASSWORD_BCRYPT,['cost'=>12]);}$pdo->prepare("UPDATE `{$p}users` SET $sets WHERE id=?")->execute(array_merge($vals,[$id]));}else{$hash=password_hash($_POST['password']??'changeme123',PASSWORD_BCRYPT,['cost'=>12]);$pdo->prepare("INSERT INTO `{$p}users`(name,email,password,phone,role,plan,status,created_at)VALUES(?,?,?,?,'client',?,1,NOW())")->execute([$_POST['name'],$_POST['email'],$hash,$_POST['phone']??'',$_POST['plan']??'free']);}redirect('/admin/clients');}
    if($a==='toggle'){$pdo->prepare("UPDATE `{$p}users` SET status=IF(status=1,0,1) WHERE id=?")->execute([$id]);redirect('/admin/clients');}
}
try{$clients=$pdo->query("SELECT u.*,COUNT(DISTINCT o.id) AS oc,COALESCE(SUM(CASE WHEN pay.status='completed' THEN pay.amount ELSE 0 END),0) AS spent FROM `{$p}users` u LEFT JOIN `{$p}orders` o ON o.client_id=u.id LEFT JOIN `{$p}payments` pay ON pay.user_id=u.id WHERE u.role='client' GROUP BY u.id ORDER BY u.created_at DESC")->fetchAll();}catch(Exception $e){$clients=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Clients</div><div class="ps"><?=count($clients)?> total</div></div>
<button class="btn bp" onclick="document.getElementById('cmid').value=0;document.getElementById('cmform').reset();oM('cmo')">+ Add Client</button></div>
<div class="panel"><div class="tw"><table><thead><tr><th>NAME</th><th>EMAIL</th><th>PLAN</th><th>ORDERS</th><th>SPENT</th><th>JOINED</th><th>STATUS</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($clients as $c):?><tr>
<td class="tdp"><?=e($c['name'])?></td><td><?=e($c['email'])?></td>
<td><?=planBadge($c['plan'])?></td><td><?=(int)$c['oc']?></td>
<td style="color:var(--acid);font-family:'Syne',sans-serif;font-weight:700"><?=moneyFmt($c['spent'])?></td>
<td class="tdm"><?=formatDate($c['created_at'])?></td><td><?=badge($c['status']?'active':'inactive')?></td>
<td><div class="tda"><button class="btn bs bxs" onclick="editCl(<?=htmlspecialchars(json_encode($c),ENT_QUOTES)?>)">Edit</button>
<form method="post" style="display:inline"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=(int)$c['id']?>"><button class="btn bs bxs"><?=$c['status']?'Off':'On'?></button></form>
</div></td></tr>
<?php endforeach;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="cmo"><div class="mw"><div class="mh"><div class="mt" id="cmtitle">Add Client</div><button class="mc" onclick="cM(\'cmo\')">✕</button></div><div class="mb"><form method="post" id="cmform"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="cmid" value="0"><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">NAME</label><input type="text" name="name" id="cm_name" class="fc" required></div><div class="fg"><label class="fl">EMAIL</label><input type="email" name="email" id="cm_email" class="fc" required></div><div class="fg"><label class="fl">PHONE</label><input type="text" name="phone" id="cm_phone" class="fc"></div><div class="fg"><label class="fl">PLAN</label><select name="plan" id="cm_plan" class="fc"><option value="free">Free</option><option value="starter">Starter</option><option value="pro">Pro</option><option value="agency">Agency</option></select></div><div class="fg"><label class="fl">STATUS</label><select name="status" id="cm_status" class="fc"><option value="1">Active</option><option value="0">Inactive</option></select></div></div><div class="fg"><label class="fl">PASSWORD (leave blank to keep)</label><input type="password" name="password" id="cm_pass" class="fc" placeholder="Min 8 chars"></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'cmo\')">Cancel</button><button type="submit" class="btn bp">Save</button></div></form></div></div></div>';
$js='<script>function editCl(c){document.getElementById("cmtitle").textContent="Edit Client";document.getElementById("cmid").value=c.id;document.getElementById("cm_name").value=c.name||"";document.getElementById("cm_email").value=c.email||"";document.getElementById("cm_phone").value=c.phone||"";document.getElementById("cm_plan").value=c.plan||"free";document.getElementById("cm_status").value=c.status;document.getElementById("cm_pass").value="";oM("cmo");}</script>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
