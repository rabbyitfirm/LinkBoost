<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='dashboard';
try{$stats=['rev'=>$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE status='completed'")->fetchColumn(),'rev_month'=>$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE status='completed' AND MONTH(created_at)=MONTH(NOW())")->fetchColumn(),'orders'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders`")->fetchColumn(),'clients'=>$pdo->query("SELECT COUNT(*) FROM `{$p}users` WHERE role='client' AND status=1")->fetchColumn(),'pending'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders` WHERE status='pending'")->fetchColumn(),'msgs'=>$pdo->query("SELECT COUNT(*) FROM `{$p}contact_messages` WHERE is_read=0")->fetchColumn(),'subs'=>$pdo->query("SELECT COUNT(*) FROM `{$p}users` WHERE plan!='free' AND status=1")->fetchColumn(),'audits'=>$pdo->query("SELECT COUNT(*) FROM `{$p}audits`")->fetchColumn()];$recentOrders=$pdo->query("SELECT o.*,u.name AS cn,u.email AS ce,s.name AS sn FROM `{$p}orders` o LEFT JOIN `{$p}users` u ON u.id=o.client_id LEFT JOIN `{$p}services` s ON s.id=o.service_id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();$planDist=$pdo->query("SELECT plan,COUNT(*) AS cnt FROM `{$p}users` WHERE role='client' GROUP BY plan")->fetchAll();}catch(Exception $e){$stats=array_fill_keys(['rev','rev_month','orders','clients','pending','msgs','subs','audits'],0);$recentOrders=$planDist=[];}
$pageTitle='Dashboard';
ob_start();
?>
<div class="ph"><div><div class="pt">Dashboard</div><div class="ps">Platform overview — <?=date('l, F j, Y')?></div></div></div>
<div class="sg">
  <div class="sc"><div class="si">💰</div><div class="sl">TOTAL REVENUE</div><div class="sv"><?=currSym()?><?=number_format((float)$stats['rev'],0)?></div><div class="ssb sup">+<?=currSym()?><?=number_format((float)$stats['rev_month'],0)?> this month</div></div>
  <div class="sc"><div class="si">📋</div><div class="sl">TOTAL ORDERS</div><div class="sv"><?=(int)$stats['orders']?></div><div class="ssb"><?=(int)$stats['pending']?> pending</div></div>
  <div class="sc"><div class="si">👥</div><div class="sl">CLIENTS</div><div class="sv"><?=(int)$stats['clients']?></div><div class="ssb"><?=(int)$stats['subs']?> paid subscribers</div></div>
  <div class="sc"><div class="si">✉️</div><div class="sl">MESSAGES</div><div class="sv" style="color:var(--orange)"><?=(int)$stats['msgs']?></div><div class="ssb">Unread</div></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">Quick Actions</div></div><div class="pnb" style="display:grid;grid-template-columns:1fr 1fr;gap:.45rem">
<?php foreach(['/admin/orders'=>'📋 Orders','/admin/clients'=>'👥 Clients','/admin/services'=>'📦 Services','/admin/payments'=>'💳 Payments','/admin/blog'=>'📝 Blog','/admin/customizer'=>'🎨 Customizer','/admin/plans'=>'💎 Plans','/admin/settings'=>'⚙ Settings'] as $u=>$l):?>
<a href="<?=e(appUrl().$u)?>" class="btn bs bsm" style="justify-content:flex-start"><?=e($l)?></a>
<?php endforeach;?></div></div>
<div class="panel"><div class="pnh"><div class="pnt">Plan Distribution</div></div><div class="pnb">
<?php $planMap=[];foreach($planDist as $pd)$planMap[$pd['plan']]=(int)$pd['cnt'];$total=max(array_sum($planMap),1);?>
<?php foreach(['free'=>'var(--mist)','starter'=>'var(--blue)','pro'=>'var(--green)','agency'=>'var(--purple)'] as $plan=>$col):$cnt=$planMap[$plan]??0;?>
<div style="margin-bottom:.7rem"><div style="display:flex;justify-content:space-between;font-size:.62rem;margin-bottom:.25rem"><span style="color:var(--mist)"><?=strtoupper($plan)?></span><span style="color:<?=$col?>;font-weight:700"><?=$cnt?></span></div>
<div class="pb"><div class="pf" style="width:<?=round($cnt/$total*100)?>%;background:<?=$col?>"></div></div></div>
<?php endforeach;?></div></div>
</div>
<div class="panel"><div class="pnh"><div class="pnt">Recent Orders</div><a href="<?=e(appUrl())?>/admin/orders" class="btn bs bsm">View All</a></div>
<div class="tw"><table><thead><tr><th>ORDER</th><th>CLIENT</th><th>SERVICE</th><th>AMOUNT</th><th>STATUS</th><th>DATE</th></tr></thead><tbody>
<?php foreach($recentOrders as $o):?><tr><td class="tdp"><?=e($o['order_number'])?></td><td><div><?=e($o['cn']??'—')?></div><div class="tdm"><?=e($o['ce']??'')?></div></td><td class="tdc"><?=e($o['sn']??'Custom')?></td><td style="color:var(--acid);font-family:'Syne',sans-serif;font-weight:700"><?=moneyFmt($o['amount'])?></td><td><?=badge($o['status'])?></td><td class="tdm"><?=formatDate($o['created_at'])?></td></tr>
<?php endforeach;if(empty($recentOrders)):?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--mist)">No orders yet</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
