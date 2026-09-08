<?php
// Stripe sends webhook events here
// Set webhook URL in Stripe Dashboard: https://yoursite.com/webhook/stripe
$payload=file_get_contents('php://input');
$sig=$_SERVER['HTTP_STRIPE_SIGNATURE']??'';
$secret=setting('stripe_webhook_secret','');

// Verify signature if secret is set
if($secret&&$sig){
    $parts=explode(',',$sig);$ts='';$v1='';
    foreach($parts as $part){[$k,$v]=array_pad(explode('=',$part,2),2,'');if($k==='t')$ts=$v;if($k==='v1')$v1=$v;}
    $expected=hash_hmac('sha256',$ts.'.'.$payload,$secret);
    if(!hash_equals($expected,$v1)){http_response_code(400);die('Bad signature');}
}

$event=json_decode($payload,true);
$pdo=db();$p=pfx();

switch($event['type']??''){
    case 'checkout.session.completed':
        $s=$event['data']['object'];
        $uid=(int)($s['metadata']['user_id']??0);
        $type=$s['metadata']['type']??'';
        $plan=$s['metadata']['plan']??'';
        $orderId=(int)($s['metadata']['order_id']??0);
        $amt=(int)($s['amount_total']??0)/100;
        if($uid&&$type==='plan'&&$plan){
            $pdo->prepare("UPDATE `{$p}users` SET plan=?,plan_expires_at=DATE_ADD(NOW(),INTERVAL 1 MONTH) WHERE id=?")->execute([$plan,$uid]);
            $pdo->prepare("INSERT INTO `{$p}payments`(user_id,type,gateway,transaction_id,amount,currency,status,paid_at,created_at)VALUES(?,'subscription','stripe',?,?,?,'completed',NOW(),NOW())")->execute([$uid,$s['id']??'',$amt,strtoupper($s['currency']??currency())]);
        }elseif($uid&&$type==='order'&&$orderId){
            $pdo->prepare("UPDATE `{$p}orders` SET status='in_progress' WHERE id=? AND client_id=?")->execute([$orderId,$uid]);
            $pdo->prepare("INSERT INTO `{$p}payments`(user_id,order_id,type,gateway,transaction_id,amount,currency,status,paid_at,created_at)VALUES(?,'order','stripe',?,?,?,'completed',NOW(),NOW())")->execute([$uid,$orderId,$s['id']??'',$amt,strtoupper($s['currency']??currency())]);
        }
        break;
    case 'customer.subscription.deleted':
        // Downgrade to free if subscription cancelled
        $custId=$event['data']['object']['customer']??'';
        // You'd look up user by stripe_customer_id if stored
        break;
}
http_response_code(200);echo json_encode(['ok'=>true]);
