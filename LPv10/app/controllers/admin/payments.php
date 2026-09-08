<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='payments';$pageTitle='Payments';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$pdo->prepare("INSERT INTO `{$p}payments`(user_id,order_id,type,gateway,transaction_id,amount,currency,status,notes,paid_at,created_at)VALUES(?,?,?,'manual',?,'".currency()."','completed',?,NOW(),NOW())")->execute([(int)$_POST['uid'],(int)$_POST['order_id'],$_POST['type']??'order',$_POST['txn']??'',(float)$_POST['amount'],$_POST['notes']??'']);redirect('/admin/payments');}
try{$payments=$pdo->query("SELECT pay.*,u.name AS un,o.order_number FROM `{$p}payments` pay LEFT JOIN `{$p}users` u ON u.id=pay.user_id LEFT JOIN `{$p}orders` o ON o.id=pay.order_id ORDER BY pay.created_at DESC")->fetchAll();$total=$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE status='completed'")->fetchColumn();$month=$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE status='completed' AND MONTH(created_at)=MONTH(NOW())")->fetchColumn();}catch(Exception $e){$payments=[];$total=$month=0;}
ob_start();?>
<div class="ph"><div><div class="pt">Payments</div></div><button class="btn bp" onclick="oM('pmo')">+ Record Payment</button></div>
<div class="sg" style="grid-template-columns:repeat(3,1fr)">
  <div class="sc"><div class="sl">TOTAL REVENUE</div><div class="sv"><?=moneyFmt($total)?></div></div>
  <div class="sc"><div class="sl">THIS MONTH</div><div class="sv" style="color:var(--green)"><?=moneyFmt($month)?></div></div>
  <div class="sc"><div class="sl">TRANSACTIONS</div><div class="sv"><?=count($payments)?></div></div>
</div>
<div class="panel"><div class="tw"><table><thead><tr><th>TXN</th><th>CLIENT</th><th>ORDER</th><th>TYPE</th><th>AMOUNT</th><th>GATEWAY</th><th>STATUS</th><th>DATE</th></tr></thead><tbody>
<?php foreach($payments as $pay):?><tr>
<td class="tdm" style="font-size:.58rem"><?=e($pay['transaction_id']??'MANUAL')?></td>
<td><?=e($pay['un']??'—')?></td><td class="tdm"><?=e($pay['order_number']??'—')?></td>
<td><?=badge($pay['type']??'order')?></td>
<td style="color:var(--acid);font-family:'Syne',sans-serif;font-weight:700"><?=moneyFmt($pay['amount'])?></td>
<td class="tdm"><?=e($pay['gateway'])?></td><td><?=badge($pay['status'])?></td>
<td class="tdm"><?=formatDate($pay['created_at'])?></td>
</tr><?php endforeach;if(empty($payments)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No payments</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="pmo"><div class="mw"><div class="mh"><div class="mt">Record Payment</div><button class="mc" onclick="cM(\'pmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><div class="fg"><label class="fl">CLIENT ID</label><input type="number" name="uid" class="fc" required></div><div class="fg"><label class="fl">ORDER ID</label><input type="number" name="order_id" class="fc"></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">AMOUNT</label><input type="number" name="amount" class="fc" step="0.01" required></div><div class="fg"><label class="fl">GATEWAY</label><select name="gateway" class="fc"><option value="manual">Manual</option><option value="stripe">Stripe</option><option value="paypal">PayPal</option><option value="bank_transfer">Bank Transfer</option></select></div></div><div class="fg"><label class="fl">TRANSACTION ID</label><input type="text" name="txn" class="fc"></div><div class="fg"><label class="fl">NOTES</label><textarea name="notes" class="fc" rows="2"></textarea></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'pmo\')">Cancel</button><button type="submit" class="btn bp">Save</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
