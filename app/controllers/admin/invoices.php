<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='invoices';$pageTitle='Invoices';
try{$invoices=$pdo->query("SELECT i.*,u.name AS un,o.order_number FROM `{$p}invoices` i LEFT JOIN `{$p}users` u ON u.id=i.user_id LEFT JOIN `{$p}orders` o ON o.id=i.order_id ORDER BY i.created_at DESC")->fetchAll();}catch(Exception $e){$invoices=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Invoices</div></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>INVOICE #</th><th>CLIENT</th><th>ORDER</th><th>TOTAL</th><th>STATUS</th><th>DUE</th><th>DATE</th></tr></thead><tbody>
<?php foreach($invoices as $inv):?><tr>
<td class="tdp"><?=e($inv['invoice_number'])?></td><td><?=e($inv['un']??'—')?></td>
<td class="tdm"><?=e($inv['order_number']??'—')?></td>
<td style="color:var(--acid);font-weight:700"><?=moneyFmt($inv['total'])?></td>
<td><?=badge($inv['status'])?></td><td class="tdm"><?=formatDate($inv['due_date'])?></td>
<td class="tdm"><?=formatDate($inv['created_at'])?></td>
</tr><?php endforeach;if(empty($invoices)):?><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--mist)">No invoices</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
