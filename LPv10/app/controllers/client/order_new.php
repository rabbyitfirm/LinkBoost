<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='new_order';$pageTitle='New Order';$uid=(int)$_SESSION['user_id'];$error='';
// Check plan limit
$orderCount=(int)$pdo->query("SELECT COUNT(*) FROM `{$p}orders` WHERE client_id=$uid AND MONTH(created_at)=MONTH(NOW())")->fetchColumn();
$maxOrders=(int)planLimit('max_orders');
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    if($maxOrders>0&&$orderCount>=$maxOrders){$error='Monthly order limit reached. Upgrade your plan for more orders.';}
    else{$sid=(int)($_POST['service_id']??0);$price=0;if($sid){$sr=$pdo->prepare("SELECT price FROM `{$p}services` WHERE id=? LIMIT 1");$sr->execute([$sid]);$price=(float)($sr->fetchColumn()?:0);}
    $num=setting('order_prefix','ORD-').date('ymd').rand(10,99);$pdo->prepare("INSERT INTO `{$p}orders`(order_number,client_id,service_id,status,target_url,anchor_text,niche,notes,amount,created_at)VALUES(?,?,?,'pending',?,?,?,?,?,NOW())")->execute([$num,$uid,$sid?:null,$_POST['target_url'],$_POST['anchor_text'],$_POST['niche']??'',$_POST['notes']??'',$price]);
    logAct('ORDER_PLACED','Order '.$num);redirect('/orders');}
}
try{$services=$pdo->query("SELECT * FROM `{$p}services` WHERE status=1 ORDER BY sort_order,price")->fetchAll();}catch(Exception $e){$services=[];}
ob_start();?>
<div class="ph"><div><div class="pt">New Order</div><div class="ps">Select a service and fill in your details</div></div></div>
<?php if($error):?><div class="al al-e">✗ <?=e($error)?> <a href="<?=e(appUrl())?>/billing" style="color:var(--acid)">Upgrade →</a></div><?php endif;?>
<?php if($maxOrders>0):?><div class="al al-i">Plan usage: <?=$orderCount?> / <?=$maxOrders?> orders this month <?php if($orderCount>=$maxOrders*0.8):?><a href="<?=e(appUrl())?>/billing" style="color:var(--acid)">— Upgrade for more</a><?php endif;?></div><?php endif;?>
<?php if(!empty($services)):?>
<div class="panel"><div class="pnh"><div class="pnt">Choose a Service</div></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.8rem;padding:.95rem">
<?php foreach($services as $svc):?>
<div onclick="pickSvc(<?=(int)$svc['id']?>,<?=htmlspecialchars(json_encode($svc['name']),ENT_QUOTES)?>,<?=number_format((float)$svc['price'],2)?>,<?=(int)$svc['turnaround_days']?>)"
  id="sc<?=(int)$svc['id']?>" style="background:var(--card2);border:1px solid var(--border);padding:.88rem;cursor:pointer;transition:all .2s;border-radius:4px">
  <div style="font-size:.48rem;font-weight:700;letter-spacing:.07em;color:var(--acid);margin-bottom:.5rem;padding:.1em .38em;background:rgba(184,255,60,.1);border-radius:2px;display:inline-block"><?=e(strtoupper(str_replace('_',' ',$svc['type'])))?></div>
  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.82rem;margin-bottom:.22rem"><?=e($svc['name'])?></div>
  <?php if($svc['niche']):?><div style="font-size:.58rem;color:var(--mist);margin-bottom:.35rem"><?=e($svc['niche'])?></div><?php endif;?>
  <div style="display:flex;gap:.4rem;margin-bottom:.6rem;flex-wrap:wrap">
    <span style="font-size:.54rem;color:var(--mist);background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.1em .4em;border-radius:2px">DA<?=(int)$svc['min_da']?>+</span>
    <span style="font-size:.54rem;color:var(--mist);background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.1em .4em;border-radius:2px">DR<?=(int)$svc['min_dr']?>+</span>
    <span style="font-size:.54rem;color:var(--mist);background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.1em .4em;border-radius:2px">⏱<?=(int)$svc['turnaround_days']?>d</span>
  </div>
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div style="color:var(--acid);font-family:'Syne',sans-serif;font-weight:800;font-size:1rem"><?=currSym()?><?=number_format((float)$svc['price'],2)?></div>
    <div style="font-size:.54rem;color:var(--mist)">per placement</div>
  </div>
</div>
<?php endforeach;?></div></div>
<?php endif;?>
<div class="panel"><div class="pnh"><div class="pnt">Order Details</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="service_id" id="svcId" value="0">
<div id="svcSel" style="display:none;margin-bottom:.85rem;padding:.6rem .85rem;background:rgba(184,255,60,.05);border:1px solid rgba(184,255,60,.18);font-size:.68rem;border-radius:3px">
  Selected: <strong id="svcNm"></strong> — <span id="svcPr" style="color:var(--acid);font-weight:700"></span> <span id="svcDy" style="float:right;color:var(--mist)"></span>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
  <div style="grid-column:1/-1"><div class="fg"><label class="fl">TARGET URL *</label><input type="url" name="target_url" class="fc" placeholder="https://yoursite.com/page" required></div></div>
  <div class="fg"><label class="fl">ANCHOR TEXT *</label><input type="text" name="anchor_text" class="fc" placeholder="your target keyword" required></div>
  <div class="fg"><label class="fl">NICHE</label><input type="text" name="niche" class="fc" placeholder="Tech / Finance / Health"></div>
</div>
<div class="fg"><label class="fl">SPECIAL NOTES</label><textarea name="notes" class="fc" rows="3" placeholder="Preferred domains, topics to avoid, any special requirements..."></textarea></div>
<button type="submit" class="btn bp">Place Order →</button>
</form></div></div>
<?php $pageContent=ob_get_clean();
$js='<script>var prev=null;function pickSvc(id,name,price,days){if(prev){var e=document.getElementById("sc"+prev);if(e){e.style.borderColor="rgba(255,255,255,.07)";e.style.background="var(--card2)";}}document.getElementById("svcId").value=id;document.getElementById("svcNm").textContent=name;document.getElementById("svcPr").textContent="'.currSym().'"+price;document.getElementById("svcDy").textContent=days+" day delivery";document.getElementById("svcSel").style.display="block";var el=document.getElementById("sc"+id);if(el){el.style.borderColor="var(--acid)";el.style.background="rgba(184,255,60,.04)";}prev=id;}</script>';
include LB_ROOT.'/app/views/layouts/client_wrap.php';
