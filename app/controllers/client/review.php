<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$uid=(int)$_SESSION['user_id'];
$oid=(int)($GLOBALS['route_params'][0]??0);
$s=$pdo->prepare("SELECT o.*,sv.name AS sname FROM `{$p}orders` o LEFT JOIN `{$p}services` sv ON sv.id=o.service_id WHERE o.id=? AND o.client_id=? AND o.status='completed' LIMIT 1");
$s->execute([$oid,$uid]);$order=$s->fetch();
if(!$order)redirect('/orders');
$existing=$pdo->prepare("SELECT * FROM `{$p}reviews` WHERE order_id=? AND user_id=? LIMIT 1");$existing->execute([$oid,$uid]);$rev=$existing->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $rating=max(1,min(5,(int)$_POST['rating']));$review=trim($_POST['review']??'');$pub=(int)($_POST['is_public']??1);
    if($rev){$pdo->prepare("UPDATE `{$p}reviews` SET rating=?,review=?,is_public=? WHERE id=?")->execute([$rating,$review,$pub,$rev['id']]);}
    else{$pdo->prepare("INSERT INTO `{$p}reviews`(order_id,user_id,rating,review,is_public,created_at)VALUES(?,?,?,?,?,NOW())")->execute([$oid,$uid,$rating,$review,$pub]);}
    logAct('REVIEW_SUBMIT','Order #'.$order['order_number']);redirect('/orders');
}
$pageTitle='Leave a Review';$activePage='orders';
ob_start();?>
<div class="ph"><div><div class="pt">Leave a Review</div><div class="ps">Order #<?=e($order['order_number'])?> · <?=e($order['sname']??'Custom Link')?></div></div></div>
<div class="panel" style="max-width:560px"><div class="pnb">
<?php if($rev):?><div class="al al-i" style="margin-bottom:.85rem">You already reviewed this order. Update below.</div><?php endif;?>
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">RATING</label>
<div style="display:flex;gap:.4rem;margin-top:.3rem" id="starRow">
<?php for($i=1;$i<=5;$i++):?>
<button type="button" onclick="setRating(<?=$i?>)" id="star<?=$i?>" style="background:none;border:none;cursor:pointer;font-size:1.5rem;transition:transform .15s;color:<?=($rev&&$rev['rating']>=$i)?'var(--acid)':'rgba(255,255,255,.2)?>">★</button>
<?php endfor;?>
</div>
<input type="hidden" name="rating" id="ratingVal" value="<?=(int)($rev['rating']??5)?>">
</div>
<div class="fg"><label class="fl">YOUR REVIEW</label><textarea name="review" class="fc" rows="4" placeholder="How was the quality of the link? Delivery time? Overall experience..." required><?=e($rev['review']??'')?></textarea></div>
<div class="fg"><label class="fl">MAKE PUBLIC ON SITE</label><select name="is_public" class="fc"><option value="1" <?=($rev['is_public']??1)?'selected':''?>>Yes — show on homepage</option><option value="0" <?=($rev&&!$rev['is_public'])?'selected':''?>>No — private</option></select></div>
<button type="submit" class="btn bp">Submit Review →</button>
<a href="<?=e(appUrl())?>/orders" class="btn bs" style="margin-left:.4rem">Cancel</a>
</form></div></div>
<script>
function setRating(n){document.getElementById('ratingVal').value=n;for(var i=1;i<=5;i++){var s=document.getElementById('star'+i);s.style.color=i<=n?'var(--acid)':'rgba(255,255,255,.2)';s.style.transform=i===n?'scale(1.2)':'scale(1)';}}
setRating(<?=(int)($rev['rating']??5)?>);
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
