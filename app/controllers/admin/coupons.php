<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='coupons';$pageTitle='Coupon Codes';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $a=$_POST['action']??'';$id=(int)($_POST['id']??0);
    if($a==='save'){$d=[$_POST['code'],$_POST['type'],(float)$_POST['value'],(float)$_POST['min_amount'],$_POST['max_uses']?:(null),$_POST['applies_to'],$_POST['expires_at']?:(null),(int)$_POST['status']];
    if($id)$pdo->prepare("UPDATE `{$p}coupons` SET code=?,type=?,value=?,min_amount=?,max_uses=?,applies_to=?,expires_at=?,status=? WHERE id=?")->execute(array_merge($d,[$id]));
    else $pdo->prepare("INSERT INTO `{$p}coupons`(code,type,value,min_amount,max_uses,applies_to,expires_at,status,created_at)VALUES(?,?,?,?,?,?,?,?,NOW())")->execute($d);
    redirect('/admin/coupons');}
    if($a==='delete'){$pdo->prepare("DELETE FROM `{$p}coupons` WHERE id=?")->execute([$id]);redirect('/admin/coupons');}
}
try{$coupons=$pdo->query("SELECT * FROM `{$p}coupons` ORDER BY created_at DESC")->fetchAll();}catch(Exception $e){$coupons=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Coupon Codes</div></div><button class="btn bp" onclick="oM('cmo')">+ New Coupon</button></div>
<div class="panel"><div class="tw"><table><thead><tr><th>CODE</th><th>TYPE</th><th>VALUE</th><th>APPLIES TO</th><th>USES</th><th>EXPIRES</th><th>STATUS</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($coupons as $c):?><tr>
<td><strong style="font-family:monospace;color:var(--acid);letter-spacing:.05em"><?=e($c['code'])?></strong></td>
<td><?=badge($c['type']=='percent'?'info':'success')?></td>
<td style="font-weight:700"><?=$c['type']==='percent'?(int)$c['value'].'%':moneyFmt($c['value'])?></td>
<td class="tdm"><?=e($c['applies_to'])?></td>
<td><?=(int)$c['uses']?>/<?=$c['max_uses']??'∞'?></td>
<td class="tdm"><?=formatDate($c['expires_at'])?></td>
<td><?=badge($c['status']?'active':'inactive')?></td>
<td><div class="tda"><button class="btn bs bxs" onclick="editCoupon(<?=htmlspecialchars(json_encode($c),ENT_QUOTES)?>)">Edit</button>
<form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$c['id']?>"><button class="btn bd bxs">✕</button></form>
</div></td></tr><?php endforeach;if(empty($coupons)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No coupons yet</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="cmo"><div class="mw"><div class="mh"><div class="mt" id="cmot">New Coupon</div><button class="mc" onclick="cM(\'cmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="cmoid" value="0"><div class="fg"><label class="fl">COUPON CODE</label><input type="text" name="code" id="cmocode" class="fc" placeholder="e.g. SAVE20" required style="text-transform:uppercase"></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">TYPE</label><select name="type" id="cmotype" class="fc"><option value="percent">Percentage (%)</option><option value="fixed">Fixed Amount</option></select></div><div class="fg"><label class="fl">VALUE</label><input type="number" name="value" id="cmoval" class="fc" step="0.01" required></div><div class="fg"><label class="fl">MIN ORDER AMOUNT</label><input type="number" name="min_amount" id="cmomin" class="fc" step="0.01" value="0"></div><div class="fg"><label class="fl">MAX USES</label><input type="number" name="max_uses" id="cmomax" class="fc" placeholder="Leave blank = unlimited"></div><div class="fg"><label class="fl">APPLIES TO</label><select name="applies_to" id="cmoapply" class="fc"><option value="all">All</option><option value="orders">Orders only</option><option value="plans">Plans only</option></select></div><div class="fg"><label class="fl">EXPIRES AT</label><input type="datetime-local" name="expires_at" id="cmoexp" class="fc"></div><div class="fg"><label class="fl">STATUS</label><select name="status" id="cmost" class="fc"><option value="1">Active</option><option value="0">Inactive</option></select></div></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'cmo\')">Cancel</button><button type="submit" class="btn bp">Save Coupon</button></div></form></div></div></div>';
$js='<script>function editCoupon(c){document.getElementById("cmot").textContent="Edit Coupon";document.getElementById("cmoid").value=c.id;document.getElementById("cmocode").value=c.code||"";document.getElementById("cmotype").value=c.type||"percent";document.getElementById("cmoval").value=c.value||"";document.getElementById("cmomin").value=c.min_amount||0;document.getElementById("cmomax").value=c.max_uses||"";document.getElementById("cmoapply").value=c.applies_to||"all";document.getElementById("cmoexp").value=c.expires_at?c.expires_at.replace(" ","T"):"";document.getElementById("cmost").value=c.status;oM("cmo");}</script>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
