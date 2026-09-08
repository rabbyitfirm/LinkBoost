<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='tickets';
$tid=(int)($GLOBALS['route_params'][0]??0);
$s=$pdo->prepare("SELECT t.*,u.name AS uname,u.email AS uemail FROM `{$p}tickets` t LEFT JOIN `{$p}users` u ON u.id=t.user_id WHERE t.id=? LIMIT 1");$s->execute([$tid]);$ticket=$s->fetch();
if(!$ticket)redirect('/admin/tickets');
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    if($_POST['action']==='reply'){
        $pdo->prepare("INSERT INTO `{$p}ticket_replies`(ticket_id,user_id,message,is_staff,created_at)VALUES(?,?,?,1,NOW())")->execute([$tid,$user['id'],$_POST['message']]);
        $pdo->prepare("UPDATE `{$p}tickets` SET status='in_progress',updated_at=NOW() WHERE id=?")->execute([$tid]);
        notify($ticket['user_id'],'info','Ticket Reply','Staff replied to your ticket '.$ticket['ticket_number'],'/ tickets/'.$tid);
        sendMail($ticket['uemail'],'Reply to Your Ticket '.$ticket['ticket_number'],mailTemplate('Staff replied to your ticket','<p class="p">A staff member has replied to your support ticket <strong>'.$ticket['ticket_number'].'</strong>.</p><p class="p">Log in to view the reply and respond.</p>','View Ticket',appUrl().'/tickets/'.$tid));
    }
    if($_POST['action']==='status'){$pdo->prepare("UPDATE `{$p}tickets` SET status=?,updated_at=NOW() WHERE id=?")->execute([$_POST['status'],$tid]);}
    redirect('/admin/tickets/'.$tid);
}
$replies=$pdo->query("SELECT r.*,u.name,u.role,u.avatar FROM `{$p}ticket_replies` r JOIN `{$p}users` u ON u.id=r.user_id WHERE r.ticket_id=$tid ORDER BY r.created_at ASC")->fetchAll();
$pageTitle='Ticket '.$ticket['ticket_number'];
ob_start();?>
<div class="ph"><div><div class="pt"><?=e($ticket['ticket_number'])?></div><div class="ps"><?=e($ticket['uname']??'')?> · <?=e($ticket['uemail']??'')?></div></div>
<div class="pa"><?=badge($ticket['status'])?>
<form method="post" style="display:inline-flex;gap:.3rem"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="status">
<select name="status" class="fc" style="height:32px;font-size:.64rem;padding:.2rem .6rem"><option value="open">Open</option><option value="in_progress">In Progress</option><option value="resolved">Resolved</option><option value="closed">Closed</option></select>
<button type="submit" class="btn bs bsm">Set</button></form>
<a href="<?=e(appUrl())?>/admin/tickets" class="btn bs bsm">← All</a></div></div>
<div style="background:var(--card2);border:1px solid var(--border);padding:.85rem;border-radius:4px;margin-bottom:.85rem;font-size:.7rem">
<strong><?=e($ticket['subject'])?></strong> · Dept: <?=e($ticket['department'])?> · Priority: <?=e($ticket['priority'])?>
</div>
<div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:.85rem">
<?php foreach($replies as $r):$isStaff=(bool)$r['is_staff'];?>
<div style="display:flex;gap:.7rem;<?=$isStaff?'flex-direction:row-reverse':''?>">
  <div style="width:32px;height:32px;border-radius:50%;background:<?=$isStaff?'var(--acid)':'var(--blue)'?>;display:flex;align-items:center;justify-content:center;font-family:Syne,sans-serif;font-weight:800;font-size:.64rem;color:<?=$isStaff?'var(--ink)':'#fff'?>;flex-shrink:0"><?=strtoupper(substr($r['name']??'?',0,1))?></div>
  <div style="max-width:78%">
    <div style="font-size:.58rem;color:var(--mist);margin-bottom:.3rem;<?=$isStaff?'text-align:right':''?>"><?=e($r['name'])?> <?=$isStaff?'<span class="bdg b-success">STAFF</span>':''?> · <?=formatDate($r['created_at'],'M j, g:i A')?></div>
    <div style="background:<?=$isStaff?'rgba(184,255,60,.05)':'var(--card2)'?>;border:1px solid <?=$isStaff?'rgba(184,255,60,.15)':'var(--border)'?>;padding:.75rem .9rem;border-radius:4px;font-size:.72rem;line-height:1.7"><?=nl2br(e($r['message']))?></div>
  </div>
</div>
<?php endforeach;?>
</div>
<div class="panel"><div class="pnh"><div class="pnt">Staff Reply</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="reply">
<div class="fg"><textarea name="message" class="fc" rows="5" placeholder="Type your reply to the client..." required></textarea></div>
<button type="submit" class="btn bp">Send Reply + Email Client →</button>
</form></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
