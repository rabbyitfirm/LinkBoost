<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='tickets';$pageTitle='Support Tickets';
$status=$_GET['status']??'all';$wh=$status!=='all'?"WHERE t.status='".htmlspecialchars($status,ENT_QUOTES)."'":'WHERE 1';
try{$tickets=$pdo->query("SELECT t.*,u.name AS uname,u.email AS uemail,(SELECT COUNT(*) FROM `{$p}ticket_replies` WHERE ticket_id=t.id) AS rc FROM `{$p}tickets` t LEFT JOIN `{$p}users` u ON u.id=t.user_id $wh ORDER BY FIELD(t.status,'open','in_progress','resolved','closed'),t.updated_at DESC")->fetchAll();}
catch(Exception $e){$tickets=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Support Tickets</div></div>
<div class="pa"><div class="ftabs"><?php foreach(['all'=>'All','open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $k=>$l):?><a href="?status=<?=e($k)?>" class="ftab <?=$status===$k?'active':''?>"><?=e($l)?></a><?php endforeach;?></div></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>TICKET</th><th>CLIENT</th><th>SUBJECT</th><th>PRIORITY</th><th>REPLIES</th><th>STATUS</th><th>DATE</th><th></th></tr></thead><tbody>
<?php foreach($tickets as $t):?><tr>
<td class="tdp"><?=e($t['ticket_number'])?></td>
<td><div><?=e($t['uname']??'—')?></div><div class="tdm"><?=e($t['uemail']??'')?></div></td>
<td class="tdc"><?=e($t['subject'])?></td>
<td><?=badge($t['priority']??'medium')?></td>
<td><?=(int)$t['rc']?></td>
<td><?=badge($t['status'])?></td>
<td class="tdm"><?=formatDate($t['created_at'])?></td>
<td><a href="<?=e(appUrl())?>/admin/tickets/<?=(int)$t['id']?>" class="btn bs bxs">Manage →</a></td>
</tr><?php endforeach;if(empty($tickets)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No tickets</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
