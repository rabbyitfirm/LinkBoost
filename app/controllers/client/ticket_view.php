<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='tickets';$uid=(int)$_SESSION['user_id'];
$tid=(int)($GLOBALS['route_params'][0]??0);
$s=$pdo->prepare("SELECT * FROM `{$p}tickets` WHERE id=? AND user_id=? LIMIT 1");$s->execute([$tid,$uid]);$ticket=$s->fetch();
if(!$ticket)redirect('/tickets');
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $msg=trim($_POST['message']??'');
    if($msg){$pdo->prepare("INSERT INTO `{$p}ticket_replies`(ticket_id,user_id,message,is_staff,created_at)VALUES(?,?,?,0,NOW())")->execute([$tid,$uid,$msg]);$pdo->prepare("UPDATE `{$p}tickets` SET status='open',updated_at=NOW() WHERE id=?")->execute([$tid]);redirect('/tickets/'.$tid);}
}
$replies=$pdo->query("SELECT r.*,u.name,u.role,u.avatar FROM `{$p}ticket_replies` r JOIN `{$p}users` u ON u.id=r.user_id WHERE r.ticket_id=$tid ORDER BY r.created_at ASC")->fetchAll();
$pageTitle='Ticket #'.$ticket['ticket_number'];
ob_start();?>
<div class="ph"><div><div class="pt"><?=e($ticket['ticket_number'])?></div><div class="ps"><?=e($ticket['subject'])?></div></div>
<div class="pa"><?=badge($ticket['status'])?><?=badge($ticket['priority']??'medium')?><a href="<?=e(appUrl())?>/tickets" class="btn bs bsm">← All Tickets</a></div></div>
<div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:.85rem">
<?php foreach($replies as $r):$isStaff=(bool)$r['is_staff'];?>
<div style="display:flex;gap:.7rem;<?=$isStaff?'':'flex-direction:row-reverse'?>">
  <div style="width:32px;height:32px;border-radius:50%;background:<?=$isStaff?'var(--blue)':'var(--acid)'?>;display:flex;align-items:center;justify-content:center;font-family:Syne,sans-serif;font-weight:800;font-size:.64rem;color:<?=$isStaff?'#fff':'var(--ink)'?>;flex-shrink:0"><?=strtoupper(substr($r['name']??'?',0,1))?></div>
  <div style="max-width:78%">
    <div style="font-size:.58rem;color:var(--mist);margin-bottom:.3rem;<?=$isStaff?'':'text-align:right'?>"><?=e($r['name'])?> <?=$isStaff?'<span class="bdg b-info">STAFF</span>':''?> · <?=formatDate($r['created_at'],'M j, g:i A')?></div>
    <div style="background:<?=$isStaff?'var(--card2)':'rgba(184,255,60,.06)'?>;border:1px solid <?=$isStaff?'var(--border)':'rgba(184,255,60,.15)'?>;padding:.75rem .9rem;border-radius:4px;font-size:.72rem;line-height:1.7"><?=nl2br(e($r['message']))?></div>
  </div>
</div>
<?php endforeach;?>
</div>
<?php if($ticket['status']!=='closed'):?>
<div class="panel"><div class="pnh"><div class="pnt">Reply</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><textarea name="message" class="fc" rows="4" placeholder="Type your reply..." required></textarea></div>
<button type="submit" class="btn bp">Send Reply →</button>
</form></div></div>
<?php else:?><div class="al al-i">This ticket is closed. Open a new ticket if you need further help.</div><?php endif;?>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
