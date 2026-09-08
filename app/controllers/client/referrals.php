<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$uid=(int)$_SESSION['user_id'];$activePage='referrals';$pageTitle='Referrals';
// Generate ref code if not set
if(!isset($user['ref_code'])){
    $code='REF'.strtoupper(substr(md5($uid),0,8));
    try{if(!in_array('ref_code',array_column($pdo->query("DESCRIBE `{$p}users`")->fetchAll(),'Field'))){$pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `ref_code` varchar(20) DEFAULT NULL");}$pdo->prepare("UPDATE `{$p}users` SET ref_code=? WHERE id=?")->execute([$code,$uid]);$user['ref_code']=$code;}catch(Exception $e){$user['ref_code']='REF'.strtoupper(substr(md5($uid),0,8));}
}
try{$refs=$pdo->query("SELECT r.*,u.name AS rname,u.email AS remail,u.plan FROM `{$p}referrals` r JOIN `{$p}users` u ON u.id=r.referred_id WHERE r.referrer_id=$uid ORDER BY r.created_at DESC")->fetchAll();
$total=(int)$pdo->query("SELECT COUNT(*) FROM `{$p}referrals` WHERE referrer_id=$uid AND status='qualified'")->fetchColumn();
$earned=(float)$pdo->query("SELECT COALESCE(SUM(commission),0) FROM `{$p}referrals` WHERE referrer_id=$uid")->fetchColumn();}
catch(Exception $e){$refs=[];$total=0;$earned=0;}
$refLink=appUrl().'/register?ref='.($user['ref_code']??'');
ob_start();?>
<div class="ph"><div><div class="pt">Referral Program</div><div class="ps">Earn commission for every client you refer</div></div></div>
<div style="background:linear-gradient(135deg,rgba(184,255,60,.07),rgba(84,160,255,.04));border:1px solid rgba(184,255,60,.15);border-radius:6px;padding:1.4rem;margin-bottom:.9rem;display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:center">
  <div>
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:.3rem">🎁 Your Referral Link</div>
    <div style="font-size:.65rem;color:var(--mist);margin-bottom:.8rem">Share this link. When someone signs up and upgrades, you earn commission.</div>
    <div style="display:flex;gap:.4rem">
      <input type="text" id="refLink" class="fc" value="<?=e($refLink)?>" readonly style="font-size:.64rem;flex:1;max-width:400px" onclick="this.select()">
      <button onclick="navigator.clipboard.writeText(document.getElementById('refLink').value);this.textContent='✓ Copied!';setTimeout(()=>this.textContent='Copy',2000)" class="btn bp bsm">Copy</button>
    </div>
  </div>
  <div style="text-align:center">
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;color:var(--acid)"><?=$total?></div>
    <div style="font-size:.58rem;color:var(--mist)">QUALIFIED REFERRALS</div>
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;margin-top:.4rem"><?=moneyFmt($earned)?></div>
    <div style="font-size:.58rem;color:var(--mist)">EARNED</div>
  </div>
</div>
<div class="panel"><div class="tw"><table><thead><tr><th>USER</th><th>EMAIL</th><th>PLAN</th><th>STATUS</th><th>COMMISSION</th><th>DATE</th></tr></thead><tbody>
<?php foreach($refs as $r):?><tr>
<td class="tdp"><?=e($r['rname'])?></td><td><?=e($r['remail'])?></td>
<td><?=planBadge($r['plan']??'free')?></td><td><?=badge($r['status'])?></td>
<td style="color:var(--acid);font-weight:700"><?=moneyFmt($r['commission'])?></td>
<td class="tdm"><?=formatDate($r['created_at'])?></td>
</tr><?php endforeach;if(empty($refs)):?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--mist)">No referrals yet. Share your link!</td></tr><?php endif;?>
</tbody></table></div></div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.8rem;margin-top:.5rem">
  <?php foreach([['20%','Commission on first order of each referral'],['Instant','Credited when referral makes payment'],['No limit','Refer as many clients as you want']] as [$v,$d]):?>
  <div class="panel" style="text-align:center;padding:1rem"><div style="font-family:Syne,sans-serif;font-weight:800;font-size:1.3rem;color:var(--acid);margin-bottom:.3rem"><?=e($v)?></div><div style="font-size:.64rem;color:var(--mist)"><?=e($d)?></div></div>
  <?php endforeach;?>
</div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
