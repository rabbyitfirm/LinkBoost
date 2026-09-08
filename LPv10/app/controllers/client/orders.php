<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='orders';$pageTitle='My Orders';$uid=(int)$_SESSION['user_id'];
$status=$_GET['status']??'all';$wh=$status!=='all'?"AND o.status='".htmlspecialchars($status,ENT_QUOTES)."'":'';
try{$orders=$pdo->query("SELECT o.*,s.name AS sn FROM `{$p}orders` o LEFT JOIN `{$p}services` s ON s.id=o.service_id WHERE o.client_id=$uid $wh ORDER BY o.created_at DESC")->fetchAll();}catch(Exception $e){$orders=[];}
ob_start();?>
<div class="ph"><div><div class="pt">My Orders</div></div>
<div class="pa"><div class="ftabs"><?php foreach(['all'=>'All','pending'=>'Pending','in_progress'=>'In Progress','review'=>'Review','completed'=>'Done','cancelled'=>'Cancelled'] as $k=>$l):?><a href="?status=<?=e($k)?>" class="ftab <?=($status)===$k?'active':''?>"><?=e($l)?></a><?php endforeach;?></div>
<a href="<?=e(appUrl())?>/orders/new" class="btn bp">+ New Order</a></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>ORDER #</th><th>SERVICE</th><th>TARGET URL</th><th>ANCHOR</th><th>AMOUNT</th><th>STATUS</th><th>LIVE URL</th><th>DATE</th></tr></thead><tbody>
<?php foreach($orders as $o):?><tr>
<td class="tdp"><?=e($o['order_number'])?></td>
<td class="tdc"><?=e($o['sn']??'Custom')?></td>
<td class="tdc"><?=e($o['target_url'])?></td>
<td class="tdc"><?=e($o['anchor_text']??'')?></td>
<td style="color:var(--acid);font-weight:700"><?=moneyFmt($o['amount'])?></td>
<td><?=badge($o['status'])?></td>
<td><div class="tda"><?php if($o['live_url']):?><a href="<?=e($o['live_url'])?>" target="_blank" class="btn bi bxs">↗</a><?php endif;?>
<?php if($o['status']==='completed'):?><a href="<?=e(appUrl())?>/review/<?=(int)$o['id']?>" class="btn bs bxs">★ Review</a><?php elseif($o['status']==='pending'):?><a href="<?=e(appUrl())?>/checkout?type=order&order=<?=(int)$o['id']?>" class="btn bp bxs">Pay</a><?php endif;?></div></td>
<td style="display:none"><?php if($o['live_url']):?><a href="<?=e($o['live_url'])?>" target="_blank" class="btn bi bxs">↗ View</a>
<?php elseif($o['status']==='pending'):?><a href="<?=e(appUrl())?>/checkout?type=order&order=<?=(int)$o['id']?>" class="btn bp bxs">Pay Now</a>
<?php else:?>—<?php endif;?></td>
<td class="tdm"><?=formatDate($o['created_at'])?></td>
</tr><?php endforeach;if(empty($orders)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No orders yet — <a href="<?=e(appUrl())?>/orders/new" style="color:var(--acid)">place one now</a></td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
