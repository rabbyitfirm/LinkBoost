<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='reports';$pageTitle='Revenue Reports';
try{
    // Monthly revenue last 12 months
    $monthly=$pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') AS mo,COALESCE(SUM(amount),0) AS rev,COUNT(*) AS cnt FROM `{$p}payments` WHERE status='completed' AND created_at>DATE_SUB(NOW(),INTERVAL 12 MONTH) GROUP BY mo ORDER BY mo")->fetchAll();
    // Plan distribution
    $plans=$pdo->query("SELECT plan,COUNT(*) AS cnt FROM `{$p}users` WHERE role='client' AND status=1 GROUP BY plan")->fetchAll();
    // Order status
    $orders=$pdo->query("SELECT status,COUNT(*) AS cnt FROM `{$p}orders` GROUP BY status")->fetchAll();
    // Top services
    $services=$pdo->query("SELECT s.name,COUNT(o.id) AS orders,COALESCE(SUM(o.amount),0) AS revenue FROM `{$p}orders` o JOIN `{$p}services` s ON s.id=o.service_id GROUP BY s.id ORDER BY revenue DESC LIMIT 8")->fetchAll();
    // Key totals
    $totals=['revenue'=>$pdo->query("SELECT COALESCE(SUM(amount),0) FROM `{$p}payments` WHERE status='completed'")->fetchColumn(),'orders'=>$pdo->query("SELECT COUNT(*) FROM `{$p}orders`")->fetchColumn(),'clients'=>$pdo->query("SELECT COUNT(*) FROM `{$p}users` WHERE role='client' AND status=1")->fetchColumn(),'paid'=>$pdo->query("SELECT COUNT(*) FROM `{$p}users` WHERE role='client' AND plan!='free' AND status=1")->fetchColumn()];
}catch(Exception $e){$monthly=$plans=$orders=$services=[];$totals=['revenue'=>0,'orders'=>0,'clients'=>0,'paid'=>0];}
ob_start();?>
<div class="ph"><div><div class="pt">Revenue Reports</div><div class="ps">Last 12 months overview</div></div></div>
<div class="sg">
  <div class="sc"><div class="si">💰</div><div class="sl">TOTAL REVENUE</div><div class="sv"><?=currSym()?><?=number_format((float)$totals['revenue'],0)?></div></div>
  <div class="sc"><div class="si">📋</div><div class="sl">TOTAL ORDERS</div><div class="sv"><?=(int)$totals['orders']?></div></div>
  <div class="sc"><div class="si">👥</div><div class="sl">TOTAL CLIENTS</div><div class="sv"><?=(int)$totals['clients']?></div></div>
  <div class="sc"><div class="si">💎</div><div class="sl">PAID SUBSCRIBERS</div><div class="sv" style="color:var(--acid)"><?=(int)$totals['paid']?></div></div>
</div>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:.9rem;margin-bottom:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">Monthly Revenue</div></div><div class="pnb"><canvas id="revChart" height="200"></canvas></div></div>
<div class="panel"><div class="pnh"><div class="pnt">Plan Distribution</div></div><div class="pnb"><canvas id="planChart" height="200"></canvas></div></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
<div class="panel"><div class="pnh"><div class="pnt">Order Status</div></div><div class="pnb"><canvas id="orderChart" height="200"></canvas></div></div>
<div class="panel"><div class="pnh"><div class="pnt">Top Services by Revenue</div></div><div class="pnb">
<?php foreach($services as $s):$max=max(array_column($services,'revenue'));$pct=$max>0?round($s['revenue']/$max*100):0;?>
<div style="margin-bottom:.65rem"><div style="display:flex;justify-content:space-between;font-size:.65rem;margin-bottom:.2rem"><span><?=e($s['name'])?></span><span style="color:var(--acid);font-weight:700"><?=moneyFmt($s['revenue'])?></span></div>
<div class="pb"><div class="pf" style="width:<?=$pct?>%;background:var(--acid)"></div></div></div>
<?php endforeach;if(empty($services)):?><div style="text-align:center;padding:1.5rem;color:var(--mist)">No data yet</div><?php endif;?>
</div></div>
</div>
<?php
$moLabels=json_encode(array_column($monthly,'mo'));
$moRevs=json_encode(array_map(fn($r)=>round((float)$r['rev'],2),$monthly));
$planLabels=json_encode(array_column($plans,'plan'));
$planCounts=json_encode(array_column($plans,'cnt'));
$orderLabels=json_encode(array_column($orders,'status'));
$orderCounts=json_encode(array_column($orders,'cnt'));
$pageContent=ob_get_clean();
$js=<<<JS
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
const dark='#0f0f1e',mist='#8888aa',acid='#b8ff3c',blue='#54a0ff',red='#ff4d6d',purple='#a29bfe',green='#55efc4',orange='#ff9f43';
Chart.defaults.color=mist;Chart.defaults.borderColor='rgba(255,255,255,.06)';
new Chart(document.getElementById('revChart'),{type:'bar',data:{labels:$moLabels,datasets:[{label:'Revenue',data:$moRevs,backgroundColor:'rgba(184,255,60,.3)',borderColor:acid,borderWidth:1.5,borderRadius:3}]},options:{plugins:{legend:{display:false}},scales:{y:{grid:{color:'rgba(255,255,255,.04)'}}}}});
new Chart(document.getElementById('planChart'),{type:'doughnut',data:{labels:$planLabels,datasets:[{data:$planCounts,backgroundColor:[mist,blue,green,purple],borderWidth:2,borderColor:dark}]},options:{cutout:'65%',plugins:{legend:{position:'bottom',labels:{font:{size:10},padding:12}}}}});
new Chart(document.getElementById('orderChart'),{type:'doughnut',data:{labels:$orderLabels,datasets:[{data:$orderCounts,backgroundColor:[orange,blue,purple,green,red],borderWidth:2,borderColor:dark}]},options:{cutout:'65%',plugins:{legend:{position:'bottom',labels:{font:{size:10},padding:12}}}}});
</script>
JS;
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
