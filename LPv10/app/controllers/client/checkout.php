<?php
requireLogin();
$pdo=db();$p=pfx();$user=currentUser();$uid=(int)$_SESSION['user_id'];

// What are we checking out?
$type=$_GET['type']??'plan'; // plan | order
$planSlug=$_GET['plan']??'pro';
$cycle=$_GET['cycle']??'monthly'; // monthly | yearly
$orderId=(int)($_GET['order']??0);

$stripeKey=setting('stripe_pub_key','');
$stripeSecret=setting('stripe_secret_key','');
$hasStripe=$stripeKey&&$stripeSecret;

// Load plan or order
$item=null;$price=0;$label='';
if($type==='plan'){
    $s=$pdo->prepare("SELECT * FROM `{$p}plans` WHERE slug=? LIMIT 1");
    $s->execute([$planSlug]);$item=$s->fetch();
    if($item){
        $price=$cycle==='yearly'?(float)$item['price_yearly']:(float)$item['price_monthly'];
        $label=$item['name'].' Plan ('.ucfirst($cycle).')';
    }
}elseif($type==='order'&&$orderId){
    $s=$pdo->prepare("SELECT o.*,sv.name AS sname FROM `{$p}orders` o LEFT JOIN `{$p}services` sv ON sv.id=o.service_id WHERE o.id=? AND o.client_id=? LIMIT 1");
    $s->execute([$orderId,$uid]);$item=$s->fetch();
    if($item){$price=(float)$item['amount'];$label='Order #'.$item['order_number'].($item['sname']?' — '.$item['sname']:'');}
}

if(!$item||$price<=0)redirect('/billing');

// Handle Stripe session creation
$error='';$sessionUrl='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    if(!$hasStripe){$error='Stripe not configured. Please contact admin.';}
    else{
        try{
            // Create Stripe checkout session via API
            $successUrl=appUrl().'/checkout/success?session_id={CHECKOUT_SESSION_ID}&type='.urlencode($type).'&ref='.($type==='plan'?urlencode($planSlug):$orderId);
            $cancelUrl=appUrl().'/checkout/cancel';
            $payload=[
                'mode'=>$type==='plan'?'subscription':'payment',
                'success_url'=>$successUrl,
                'cancel_url'=>$cancelUrl,
                'customer_email'=>$user['email'],
                'metadata[user_id]'=>$uid,
                'metadata[type]'=>$type,
                'metadata[plan]'=>$planSlug,
                'metadata[order_id]'=>$orderId,
                'line_items[0][quantity]'=>1,
                'line_items[0][price_data][currency]'=>strtolower(currency()),
                'line_items[0][price_data][unit_amount]'=>(int)round($price*100),
                'line_items[0][price_data][product_data][name]'=>$label,
            ];
            if($type==='plan'){
                $payload['line_items[0][price_data][recurring][interval]']=$cycle==='yearly'?'year':'month';
            }
            $ch=curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($payload),CURLOPT_USERPWD=>$stripeSecret.':',CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded']]);
            $res=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
            $data=json_decode($res,true);
            if($code===200&&isset($data['url'])){redirect($data['url']);}
            else{$error='Stripe error: '.($data['error']['message']??'Unknown error');}
        }catch(Exception $e){$error='Payment error: '.$e->getMessage();}
    }
}

$activePage='billing';$pageTitle='Checkout';
ob_start();?>
<style>
.co-wrap{max-width:860px;margin:0 auto}
.co-grid{display:grid;grid-template-columns:1fr 360px;gap:1.2rem;align-items:start}
.co-card{background:var(--card);border:1px solid var(--border);border-radius:4px;overflow:hidden}
.co-head{padding:.85rem 1.1rem;border-bottom:1px solid var(--border);font-family:'Syne',sans-serif;font-weight:700;font-size:.82rem}
.co-body{padding:1.1rem}
.order-line{display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:.7rem}
.order-line:last-child{border-bottom:none}
.pay-btn{width:100%;padding:.9rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.82rem;border:none;cursor:pointer;background:var(--acid);color:var(--ink);border-radius:3px;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem}
.pay-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(184,255,60,.3)}
.pay-btn:disabled{opacity:.5;transform:none;cursor:not-allowed}
.method-btn{width:100%;padding:.72rem;font-family:'Syne',sans-serif;font-weight:600;font-size:.72rem;border:1px solid var(--border);cursor:pointer;background:rgba(255,255,255,.04);color:var(--paper);border-radius:3px;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-bottom:.45rem;text-decoration:none}
.method-btn:hover{border-color:rgba(255,255,255,.18);background:rgba(255,255,255,.07)}
.trust-row{display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap;margin-top:.8rem}
.trust-item{font-size:.58rem;color:var(--mist);display:flex;align-items:center;gap:.25rem}
@media(max-width:700px){.co-grid{grid-template-columns:1fr}}
</style>

<div class="ph co-wrap"><div><div class="pt">Secure Checkout</div></div></div>

<div class="co-wrap">
<?php if($error):?><div class="al al-e" style="margin-bottom:.9rem">✗ <?=e($error)?></div><?php endif;?>

<div class="co-grid">

<!-- LEFT: Payment methods -->
<div>
  <div class="co-card" style="margin-bottom:.85rem">
    <div class="co-head">💳 Choose Payment Method</div>
    <div class="co-body">
      <?php if($hasStripe):?>
      <!-- Stripe -->
      <form method="post" id="stripeForm">
        <input type="hidden" name="_token" value="<?=e(csrf())?>">
        <button type="submit" class="pay-btn" id="stripeBtn">
          <svg width="18" height="18" viewBox="0 0 60 25" fill="white"><path d="M3 4h6l2 8 2-6h4l2 6 2-8h6l-5 17h-4l-2.5-7-2.5 7H9L3 4zm30 0h12c4 0 7 3 7 7s-3 7-7 7h-6v6h-6V4zm6 9h5c1.5 0 2.5-1 2.5-2.5S45.5 8 44 8h-5v5z"/></svg>
          Pay with Stripe →
        </button>
      </form>
      <?php else:?>
      <div class="al al-i" style="font-size:.68rem">Stripe not configured yet. <a href="<?=e(appUrl())?>/admin/settings" style="color:var(--acid)">Configure in Admin → Settings →</a></div>
      <?php endif;?>

      <div style="display:flex;align-items:center;gap:.6rem;margin:.7rem 0;font-size:.58rem;color:var(--mist)"><div style="flex:1;height:1px;background:var(--border)"></div>or<div style="flex:1;height:1px;background:var(--border)"></div></div>

      <!-- Bank Transfer / Manual -->
      <a href="mailto:<?=e(setting('contact_email','admin@linkparty.net'))?>?subject=Payment for <?=rawurlencode($label)?>&body=I would like to pay <?=e(currSym().number_format($price,2))?> for <?=rawurlencode($label)?>" class="method-btn">
        🏦 Bank Transfer / Manual Payment
      </a>
      <a href="https://paypal.me/<?=e(setting('paypal_username',''))?>" target="_blank" class="method-btn" style="background:rgba(0,53,148,.12);border-color:rgba(0,53,148,.3)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="#003594"><path d="M20 8c0 4-3 6-7 6h-2l-1 5H6l3-15h6c3 0 5 1.5 5 4z"/></svg>
        Pay with PayPal
      </a>
      <a href="<?=e(appUrl())?>/contact" class="method-btn">
        💬 Contact Us to Pay
      </a>

      <div class="trust-row">
        <div class="trust-item">🔒 SSL Secured</div>
        <div class="trust-item">✓ Instant Activation</div>
        <div class="trust-item">↩ Refund Policy</div>
      </div>
    </div>
  </div>

  <!-- Guarantee -->
  <div class="co-card">
    <div class="co-body" style="text-align:center;padding:1.2rem">
      <div style="font-size:2rem;margin-bottom:.5rem">🛡</div>
      <div style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.3rem">Money-Back Guarantee</div>
      <div style="font-size:.66rem;color:var(--mist);line-height:1.7">Not satisfied? Contact us within 7 days of delivery for a full refund — no questions asked.</div>
    </div>
  </div>
</div>

<!-- RIGHT: Order summary -->
<div>
  <div class="co-card" style="margin-bottom:.7rem">
    <div class="co-head">🧾 Order Summary</div>
    <div class="co-body">

      <?php if($type==='plan'&&$item):
        $feats=json_decode($item['features']??'[]',true)??[];?>
      <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.9rem;padding-bottom:.9rem;border-bottom:1px solid var(--border)">
        <div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(184,255,60,.15),rgba(84,160,255,.1));border:1px solid rgba(184,255,60,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">💎</div>
        <div>
          <div style="font-family:'Syne',sans-serif;font-weight:700"><?=e($item['name'])?> Plan</div>
          <div style="font-size:.6rem;color:var(--mist)"><?=ucfirst($cycle)?> billing</div>
        </div>
      </div>
      <?php foreach($feats as $f):?>
      <div class="order-line"><span style="color:var(--mist)">✓ <?=e($f)?></span></div>
      <?php endforeach;?>

      <?php elseif($type==='order'&&$item):?>
      <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.9rem;padding-bottom:.9rem;border-bottom:1px solid var(--border)">
        <div style="width:40px;height:40px;background:rgba(184,255,60,.08);border:1px solid rgba(184,255,60,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">🔗</div>
        <div>
          <div style="font-family:'Syne',sans-serif;font-weight:700"><?=e($item['sname']??'Link Order')?></div>
          <div style="font-size:.6rem;color:var(--mist)"><?=e($item['order_number'])?></div>
        </div>
      </div>
      <div class="order-line"><span style="color:var(--mist)">Target URL</span><span class="tdc" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=e($item['target_url'])?></span></div>
      <div class="order-line"><span style="color:var(--mist)">Anchor Text</span><span><?=e($item['anchor_text']??'—')?></span></div>
      <?php endif;?>

      <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid var(--border)">
        <div class="order-line"><span style="color:var(--mist)">Subtotal</span><span><?=moneyFmt($price)?></span></div>
        <div class="order-line"><span style="color:var(--mist)">Tax</span><span style="color:var(--mist)">Included</span></div>
        <div style="display:flex;justify-content:space-between;padding:.6rem 0;font-family:'Syne',sans-serif;font-weight:800;font-size:1rem">
          <span>Total</span>
          <span style="color:var(--acid)"><?=moneyFmt($price)?><?php if($type==='plan'):?><span style="font-size:.6rem;color:var(--mist);font-weight:400">/<?=$cycle==='yearly'?'yr':'mo'?></span><?php endif;?></span>
        </div>
      </div>

      <?php if($type==='plan'&&$cycle==='monthly'&&$item&&$item['price_yearly']>0):
        $saving=round($price*12-(float)$item['price_yearly']);?>
      <div style="background:rgba(85,239,196,.06);border:1px solid rgba(85,239,196,.15);padding:.6rem .8rem;border-radius:3px;font-size:.62rem;text-align:center">
        💡 Switch to yearly and save <strong style="color:var(--green)"><?=moneyFmt($saving)?>/year</strong>
        <a href="?type=plan&plan=<?=e($planSlug)?>&cycle=yearly" style="color:var(--acid);display:block;margin-top:.2rem">Switch to Yearly →</a>
      </div>
      <?php endif;?>

      <div style="margin-top:.8rem;padding:.65rem .8rem;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:3px;font-size:.62rem;color:var(--mist)">
        👤 Billing to: <strong style="color:var(--paper)"><?=e($user['email'])?></strong>
      </div>
    </div>
  </div>

  <!-- Toggle cycle for plans -->
  <?php if($type==='plan'&&$item):?>
  <div class="co-card">
    <div class="co-body" style="padding:.8rem">
      <div style="font-size:.6rem;color:var(--mist);text-align:center;margin-bottom:.5rem">BILLING CYCLE</div>
      <div style="display:flex;gap:.4rem">
        <a href="?type=plan&plan=<?=e($planSlug)?>&cycle=monthly" class="method-btn <?=$cycle==='monthly'?'active':''?>" style="<?=$cycle==='monthly'?'border-color:var(--acid);color:var(--acid)':''?>;flex:1;margin:0;padding:.5rem">Monthly</a>
        <a href="?type=plan&plan=<?=e($planSlug)?>&cycle=yearly" class="method-btn <?=$cycle==='yearly'?'active':''?>" style="<?=$cycle==='yearly'?'border-color:var(--acid);color:var(--acid)':''?>;flex:1;margin:0;padding:.5rem">Yearly <span style="color:var(--green);font-size:.55rem">Save 20%</span></a>
      </div>
    </div>
  </div>
  <?php endif;?>
</div>

</div><!-- end co-grid -->

<div style="text-align:center;margin-top:.8rem;font-size:.6rem;color:var(--mist)">
  🔒 Payments processed by Stripe — we never store your card details
</div>
</div>

<script>
document.getElementById('stripeForm')?.addEventListener('submit',function(){
  var btn=document.getElementById('stripeBtn');
  if(btn){btn.disabled=true;btn.innerHTML='<span style="animation:sp3 .8s linear infinite;display:inline-block">⚙</span> Redirecting to Stripe...';}
});
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
