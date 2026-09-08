<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='orders';$pageTitle='Orders';$sym=currSym();
$status=$_GET['status']??'all';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $a=$_POST['action']??'';
    if($a==='create'){$num=setting('order_prefix','ORD-').date('ymd').rand(10,99);$pdo->prepare("INSERT INTO `{$p}orders`(order_number,client_id,service_id,status,target_url,anchor_text,niche,notes,amount,due_date,created_at)VALUES(?,?,?,'pending',?,?,?,?,?,?,NOW())")->execute([$num,(int)$_POST['client_id'],$_POST['service_id']?:(null),$_POST['target_url'],$_POST['anchor_text'],$_POST['niche']??'',$_POST['notes']??'',$_POST['amount'],$_POST['due_date']?:null]);logAct('ORDER_CREATE','Order '.$num);redirect('/admin/orders');}
    if($a==='update'){$pdo->prepare("UPDATE `{$p}orders` SET status=?,admin_notes=?,live_url=?,report_url=?,updated_at=NOW() WHERE id=?")->execute([$_POST['status'],$_POST['admin_notes']??'',$_POST['live_url']??'',$_POST['report_url']??'',(int)$_POST['oid']]);if($_POST['status']==='completed')$pdo->prepare("UPDATE `{$p}orders` SET completed_at=NOW() WHERE id=? AND completed_at IS NULL")->execute([(int)$_POST['oid']]);redirect('/admin/orders');}
    if($a==='delete'){$pdo->prepare("DELETE FROM `{$p}orders` WHERE id=?")->execute([(int)$_POST['id']]);redirect('/admin/orders');}
}
$wh=$status!=='all'?"WHERE o.status='".htmlspecialchars($status,ENT_QUOTES)."'":'WHERE 1';
try{$orders=$pdo->query("SELECT o.*,u.name AS cn,u.email AS ce,s.name AS sn FROM `{$p}orders` o LEFT JOIN `{$p}users` u ON u.id=o.client_id LEFT JOIN `{$p}services` s ON s.id=o.service_id $wh ORDER BY o.created_at DESC")->fetchAll();$clients=$pdo->query("SELECT id,name FROM `{$p}users` WHERE role='client' ORDER BY name")->fetchAll();$services=$pdo->query("SELECT id,name,price FROM `{$p}services` WHERE status=1 ORDER BY name")->fetchAll();}catch(Exception $e){$orders=$clients=$services=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Orders</div></div>
<div class="pa"><div class="ftabs"><?php foreach(['all'=>'All','pending'=>'Pending','in_progress'=>'In Progress','review'=>'Review','completed'=>'Done','cancelled'=>'Cancelled'] as $k=>$l):?><a href="?status=<?=e($k)?>" class="ftab <?=($status)===$k?'active':''?>"><?=e($l)?></a><?php endforeach;?></div>
<button class="btn bp" onclick="oM('om1')">+ New Order</button></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>ORDER #</th><th>CLIENT</th><th>SERVICE</th><th>TARGET URL</th><th>AMOUNT</th><th>STATUS</th><th>DATE</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($orders as $o):?><tr>
<td class="tdp"><?=e($o['order_number'])?></td>
<td><div><?=e($o['cn']??'—')?></div><div class="tdm"><?=e($o['ce']??'')?></div></td>
<td class="tdc"><?=e($o['sn']??'Custom')?></td>
<td><div class="tdc"><?=e($o['target_url'])?></div><div class="tdm"><?=e($o['anchor_text']??'')?></div></td>
<td style="color:var(--acid);font-family:'Syne',sans-serif;font-weight:700"><?=moneyFmt($o['amount'])?></td>
<td><?=badge($o['status'])?></td><td class="tdm"><?=formatDate($o['created_at'])?></td>
<td><div class="tda">
<button class="btn bs bxs" onclick="editOrd(<?=htmlspecialchars(json_encode($o),ENT_QUOTES)?>)">Edit</button>
<?php if($o['live_url']):?><a href="<?=e($o['live_url'])?>" target="_blank" class="btn bi bxs">↗</a><?php endif;?>
<form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$o['id']?>"><button class="btn bd bxs">✕</button></form>
</div></td></tr>
<?php endforeach;if(empty($orders)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No orders</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals=<<<MOD
<div class="mo" id="om1"><div class="mw"><div class="mh"><div class="mt">New Order</div><button class="mc" onclick="cM('om1')">✕</button></div><div class="mb">
<form method="post"><input type="hidden" name="_token" value=".csrf()."><input type="hidden" name="action" value="create">
<div class="fg"><label class="fl">CLIENT</label><select name="client_id" class="fc" required><option value="">— Select —</option>CLIENTS</select></div>
<div class="fg"><label class="fl">SERVICE</label><select name="service_id" class="fc"><option value="">— Custom —</option>SERVICES</select></div>
<div class="fg"><label class="fl">TARGET URL</label><input type="url" name="target_url" class="fc" placeholder="https://" required></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">ANCHOR TEXT</label><input type="text" name="anchor_text" class="fc" required></div><div class="fg"><label class="fl">NICHE</label><input type="text" name="niche" class="fc"></div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">AMOUNT</label><input type="number" name="amount" class="fc" step="0.01" required></div><div class="fg"><label class="fl">DUE DATE</label><input type="date" name="due_date" class="fc"></div></div>
<div class="fg"><label class="fl">NOTES</label><textarea name="notes" class="fc" rows="2"></textarea></div>
<div class="mf"><button type="button" class="btn bs" onclick="cM('om1')">Cancel</button><button type="submit" class="btn bp">Create Order</button></div>
</form></div></div></div>
<div class="mo" id="om2"><div class="mw"><div class="mh"><div class="mt">Update Order</div><button class="mc" onclick="cM('om2')">✕</button></div><div class="mb">
<form method="post"><input type="hidden" name="_token" value=".csrf()."><input type="hidden" name="action" value="update"><input type="hidden" name="oid" id="eoid">
<div id="eoinfo" style="font-size:.66rem;color:var(--mist);margin-bottom:.8rem;padding:.6rem .85rem;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:3px"></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">STATUS</label><select name="status" id="eost" class="fc"><option value="pending">Pending</option><option value="in_progress">In Progress</option><option value="review">Review</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div><div class="fg"><label class="fl">LIVE URL</label><input type="url" name="live_url" id="eolive" class="fc"></div></div>
<div class="fg"><label class="fl">REPORT URL</label><input type="url" name="report_url" id="eorep" class="fc"></div>
<div class="fg"><label class="fl">ADMIN NOTES</label><textarea name="admin_notes" id="eonotes" class="fc" rows="3"></textarea></div>
<div class="mf"><button type="button" class="btn bs" onclick="cM('om2')">Cancel</button><button type="submit" class="btn bp">Save</button></div>
</form></div></div></div>
MOD;
$modals=str_replace('.csrf().',csrf(),$modals);
$cOpts='';foreach($clients as $c)$cOpts.='<option value="'.(int)$c['id'].'">'.e($c['name']).'</option>';
$sOpts='';foreach($services as $s)$sOpts.='<option value="'.(int)$s['id'].'">'.e($s['name']).' ('.currSym().number_format((float)$s['price'],2).')</option>';
$modals=str_replace('CLIENTS',$cOpts,$modals);$modals=str_replace('SERVICES',$sOpts,$modals);
$js='<script>function editOrd(o){document.getElementById("eoid").value=o.id;document.getElementById("eost").value=o.status;document.getElementById("eolive").value=o.live_url||"";document.getElementById("eorep").value=o.report_url||"";document.getElementById("eonotes").value=o.admin_notes||"";document.getElementById("eoinfo").innerHTML="<strong>"+o.order_number+"</strong> — "+o.target_url;oM("om2");}</script>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
