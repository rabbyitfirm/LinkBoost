<?php
requireLogin();
$pdo=db();$p=pfx();$uid=(int)$_SESSION['user_id'];
$sessionId=$_GET['session_id']??'';
$type=$_GET['type']??'plan';
$ref=$_GET['ref']??'';
$stripeSecret=setting('stripe_secret_key','');

// Verify with Stripe if key exists
$verified=false;$sessionData=null;
if($stripeSecret&&$sessionId){
    $ch=curl_init("https://api.stripe.com/v1/checkout/sessions/".urlencode($sessionId));
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_USERPWD=>$stripeSecret.':']);
    $res=json_decode(curl_exec($ch),true);curl_close($ch);
    if(isset($res['payment_status'])&&($res['payment_status']==='paid'||$res['status']==='complete')){
        $verified=true;$sessionData=$res;
    }
}else{$verified=true;} // Manual payment — trust the redirect

if($verified){
    if($type==='plan'&&$ref){
        // Upgrade plan
        $pdo->prepare("UPDATE `{$p}users` SET plan=?,plan_expires_at=DATE_ADD(NOW(),INTERVAL 1 MONTH),updated_at=NOW() WHERE id=?")->execute([$ref,$uid]);
        $_SESSION['user_plan']=$ref;
        // Get plan id
        $pl=$pdo->prepare("SELECT id FROM `{$p}plans` WHERE slug=? LIMIT 1");$pl->execute([$ref]);$plRow=$pl->fetch();
        if($plRow){$pdo->prepare("INSERT INTO `{$p}subscriptions`(user_id,plan_id,status,billing_cycle,starts_at,created_at)VALUES(?,?,'active','monthly',NOW(),NOW()) ON DUPLICATE KEY UPDATE status='active',plan_id=?")->execute([$uid,$plRow['id'],$plRow['id']]);}
        // Record payment
        $amt=$sessionData['amount_total']??0;
        $pdo->prepare("INSERT INTO `{$p}payments`(user_id,type,gateway,transaction_id,amount,currency,status,paid_at,created_at)VALUES(?,'subscription','stripe',?,?,?,'completed',NOW(),NOW())")->execute([$uid,$sessionId,$amt/100,currency()]);
        logAct('PLAN_UPGRADE','Upgraded to '.$ref);
    }elseif($type==='order'&&$ref){
        // Mark order paid
        $pdo->prepare("UPDATE `{$p}orders` SET status='in_progress',updated_at=NOW() WHERE id=? AND client_id=?")->execute([(int)$ref,$uid]);
        $amt=$sessionData['amount_total']??0;
        $pdo->prepare("INSERT INTO `{$p}payments`(user_id,order_id,type,gateway,transaction_id,amount,currency,status,paid_at,created_at)VALUES(?,'order','stripe',?,?,?,'completed',NOW(),NOW())")->execute([$uid,(int)$ref,$sessionId,$amt/100,currency()]);
        logAct('ORDER_PAID','Order #'.$ref.' paid');
    }
}

$activePage='billing';$pageTitle='Payment Successful';
ob_start();?>
<div style="max-width:520px;margin:4rem auto;text-align:center;padding:1rem">
  <div style="font-size:4rem;margin-bottom:1rem;animation:bounce .6s ease">✅</div>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;margin-bottom:.5rem;color:var(--green)">Payment Successful!</div>
  <?php if($type==='plan'):?>
  <div style="font-size:.75rem;color:var(--mist);line-height:1.8;margin-bottom:1.5rem">
    Your <strong style="color:var(--acid)"><?=strtoupper(e($ref))?></strong> plan is now active.<br>
    All features unlocked immediately.
  </div>
  <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
    <a href="<?=e(appUrl())?>/dashboard" class="btn bp">Go to Dashboard →</a>
    <a href="<?=e(appUrl())?>/autopilot" class="btn bs">🤖 Try AI Autopilot</a>
  </div>
  <?php elseif($type==='order'):?>
  <div style="font-size:.75rem;color:var(--mist);line-height:1.8;margin-bottom:1.5rem">
    Your order has been confirmed and is now <strong style="color:var(--acid)">In Progress</strong>.<br>
    You'll receive updates as your link is placed.
  </div>
  <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
    <a href="<?=e(appUrl())?>/orders" class="btn bp">View My Orders →</a>
    <a href="<?=e(appUrl())?>/dashboard" class="btn bs">Dashboard</a>
  </div>
  <?php endif;?>
  <div style="margin-top:2rem;padding:1rem;background:var(--card);border:1px solid var(--border);border-radius:4px;font-size:.65rem;color:var(--mist)">
    A receipt has been sent to <strong><?=e(currentUser()['email']??'')?></strong>
  </div>
</div>
<style>@keyframes bounce{0%,100%{transform:scale(1)}50%{transform:scale(1.2)}}</style>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
