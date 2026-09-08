<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='dashboard';$pageTitle='Dashboard';$uid=(int)$_SESSION['user_id'];
try{$stats=['orders'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders` WHERE client_id=$uid")->fetchColumn(),'completed'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders` WHERE client_id=$uid AND status='completed'")->fetchColumn(),'pending'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders` WHERE client_id=$uid AND status='pending'")->fetchColumn(),'spent'=>$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE user_id=$uid AND status='completed'")->fetchColumn(),'keywords'=>$pdo->query("SELECT COUNT(*) FROM `{$p}rank_tracking` WHERE user_id=$uid")->fetchColumn(),'projects'=>$pdo->query("SELECT COUNT(*) FROM `{$p}projects` WHERE user_id=$uid")->fetchColumn()];$recentOrders=$pdo->query("SELECT o.*,s.name AS sn FROM `{$p}orders` o LEFT JOIN `{$p}services` s ON s.id=o.service_id WHERE o.client_id=$uid ORDER BY o.created_at DESC LIMIT 6")->fetchAll();}catch(Exception $e){$stats=array_fill_keys(['orders','completed','pending','spent','keywords','projects'],0);$recentOrders=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Welcome back, <?=e(explode(' ',$user['name']??'')[0])?>! 👋</div><div class="ps">Here's your overview — <?=date('M j, Y')?></div></div></div>
<div class="sg">
  <div class="sc"><div class="si">📋</div><div class="sl">TOTAL ORDERS</div><div class="sv"><?=(int)$stats['orders']?></div><div class="ssb"><?=(int)$stats['completed']?> completed</div></div>
  <div class="sc"><div class="si">⏳</div><div class="sl">PENDING</div><div class="sv" style="color:var(--orange)"><?=(int)$stats['pending']?></div><div class="ssb">In queue</div></div>
  <div class="sc"><div class="si">📈</div><div class="sl">KEYWORDS TRACKED</div><div class="sv" style="color:var(--blue)"><?=(int)$stats['keywords']?></div><div class="ssb">/ <?=planLimit('max_keywords')?> limit</div></div>
  <div class="sc"><div class="si">💰</div><div class="sl">TOTAL SPENT</div><div class="sv"><?=moneyFmt($stats['spent'])?></div><div class="ssb">All time</div></div>
</div>
<?php if(userPlan()==='free'):?>
<div style="background:linear-gradient(135deg,rgba(184,255,60,.08),rgba(84,160,255,.05));border:1px solid rgba(184,255,60,.18);border-radius:4px;padding:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem">
  <div><div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem;margin-bottom:.25rem">⚡ Upgrade to Pro</div><div style="font-size:.68rem;color:var(--mist)">Get unlimited keywords, more projects, API access, and advanced features</div></div>
  <a href="<?=e(appUrl())?>/billing" class="btn bp">View Plans →</a>
</div>
<?php endif;?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">Recent Orders</div><a href="<?=e(appUrl())?>/orders" class="btn bs bsm">All Orders</a></div>
<div class="tw"><table><thead><tr><th>ORDER</th><th>SERVICE</th><th>STATUS</th><th>LIVE</th></tr></thead><tbody>
<?php foreach($recentOrders as $o):?><tr>
<td><div class="tdp"><?=e($o['order_number'])?></div><div class="tdm"><?=formatDate($o['created_at'])?></div></td>
<td class="tdc"><?=e($o['sn']??'Custom')?></td>
<td><?=badge($o['status'])?></td>
<td><?php if($o['live_url']):?><a href="<?=e($o['live_url'])?>" target="_blank" class="btn bi bxs">↗</a><?php else:?>—<?php endif;?></td>
</tr><?php endforeach;if(empty($recentOrders)):?><tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--mist)"><a href="<?=e(appUrl())?>/orders/new" style="color:var(--acid)">Place your first order →</a></td></tr><?php endif;?>
</tbody></table></div></div>
<div class="panel"><div class="pnh"><div class="pnt">Quick Actions</div></div><div class="pnb" style="display:flex;flex-direction:column;gap:.45rem">
<a href="<?=e(appUrl())?>/orders/new" class="btn bp" style="justify-content:center">📋 New Link Order</a>
<a href="<?=e(appUrl())?>/rank-tracker" class="btn bs" style="justify-content:center">📈 Rank Tracker</a>
<a href="<?=e(appUrl())?>/site-audit" class="btn bs" style="justify-content:center">🔍 Site Audit</a>
<a href="<?=e(appUrl())?>/seo-tools" class="btn bs" style="justify-content:center">🛠 SEO Tools</a>
<a href="<?=e(appUrl())?>/ask-ai" class="btn bs" style="justify-content:center">✨ Ask AI</a>
</div></div>
</div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
