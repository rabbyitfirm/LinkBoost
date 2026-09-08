<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='billing';$pageTitle='Billing & Plans';$uid=(int)$_SESSION['user_id'];
try{$plans=$pdo->query("SELECT * FROM `{$p}plans` WHERE status=1 ORDER BY sort_order")->fetchAll();$payments=$pdo->query("SELECT * FROM `{$p}payments` WHERE user_id=$uid ORDER BY created_at DESC LIMIT 10")->fetchAll();}catch(Exception $e){$plans=[];$payments=[];}
$currentPlan=userPlan();
ob_start();?>
<div class="ph"><div><div class="pt">Billing & Plans</div><div class="ps">Current plan: <?=planBadge($currentPlan)?></div></div></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.85rem;margin-bottom:.9rem">
<?php foreach($plans as $pl):$isCurrent=$pl['slug']===$currentPlan;$feats=json_decode($pl['features']??'[]',true);?>
<div style="background:var(--card);border:1px solid <?=$pl['is_popular']?'rgba(184,255,60,.35)':'var(--border)'?>;border-radius:4px;padding:1.2rem;position:relative">
  <?php if($pl['is_popular']):?><div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--acid);color:var(--ink);font-size:.48rem;font-weight:700;padding:.18em .7em;border-radius:20px;letter-spacing:.06em;white-space:nowrap">MOST POPULAR</div><?php endif;?>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;margin-bottom:.2rem"><?=e($pl['name'])?></div>
  <div style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:<?=$pl['price_monthly']>0?'var(--acid)':'var(--green)'?>;margin:.4rem 0">
    <?=$pl['price_monthly']>0?currSym().number_format((float)$pl['price_monthly'],0):'FREE'?><?php if($pl['price_monthly']>0):?><span style="font-size:.6rem;color:var(--mist);font-weight:400">/mo</span><?php endif;?></div>
  <?php if($pl['price_yearly']>0):?><div style="font-size:.6rem;color:var(--mist);margin-bottom:.7rem"><?=currSym().number_format((float)$pl['price_yearly'],0)?>/year (save <?=round((1-$pl['price_yearly']/($pl['price_monthly']*12))*100)?>%)</div><?php endif;?>
  <div style="display:flex;flex-direction:column;gap:.3rem;margin-bottom:.9rem;font-size:.65rem">
    <?php foreach($feats as $feat):?><div>✓ <span style="color:var(--mist)"><?=e($feat)?></span></div><?php endforeach;?>
  </div>
  <?php if($isCurrent):?><div class="btn bs" style="width:100%;justify-content:center;cursor:default;opacity:.6">✓ Current Plan</div>
  <?php elseif($pl['price_monthly']>0):?>
    <a href="<?=e(appUrl())?>/checkout?type=plan&plan=<?=e($pl['slug'])?>&cycle=monthly" class="btn bp" style="width:100%;justify-content:center">Upgrade Now →</a>
    <?php if($pl['price_yearly']>0):?><a href="<?=e(appUrl())?>/checkout?type=plan&plan=<?=e($pl['slug'])?>&cycle=yearly" class="btn bs bsm" style="width:100%;justify-content:center;margin-top:.3rem">Save 20% — Pay Yearly</a><?php endif;?>
  <?php else:?><div class="btn bs" style="width:100%;justify-content:center;cursor:default">Free Plan</div><?php endif;?>
</div>
<?php endforeach;?></div>
<div class="panel"><div class="pnh"><div class="pnt">Payment History</div></div>
<div class="tw"><table><thead><tr><th>DATE</th><th>AMOUNT</th><th>TYPE</th><th>STATUS</th></tr></thead><tbody>
<?php foreach($payments as $pay):?><tr>
<td class="tdm"><?=formatDate($pay['created_at'])?></td>
<td style="color:var(--acid);font-weight:700"><?=moneyFmt($pay['amount'])?></td>
<td><?=badge($pay['type']??'order')?></td><td><?=badge($pay['status'])?></td>
</tr><?php endforeach;if(empty($payments)):?><tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--mist)">No payments yet</td></tr><?php endif;?>
</tbody></table></div></div>
<div class="al al-i">To upgrade your plan, contact us at <a href="mailto:<?=e(setting('contact_email','admin@linkparty.net'))?>" style="color:var(--acid)"><?=e(setting('contact_email','admin@linkparty.net'))?></a> or use the Upgrade button above.</div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
