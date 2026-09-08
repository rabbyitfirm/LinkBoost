<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='tickets';$pageTitle='Support Tickets';$uid=(int)$_SESSION['user_id'];
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $a=$_POST['action']??'';
    if($a==='create'){
        $num='TKT-'.strtoupper(substr(md5(uniqid()),0,8));
        $pdo->prepare("INSERT INTO `{$p}tickets`(ticket_number,user_id,subject,status,priority,department,created_at)VALUES(?,?,?,'open',?,?,NOW())")->execute([$num,$uid,$_POST['subject'],$_POST['priority']??'medium',$_POST['department']??'general']);
        $tid=$pdo->lastInsertId();
        $pdo->prepare("INSERT INTO `{$p}ticket_replies`(ticket_id,user_id,message,is_staff,created_at)VALUES(?,?,?,0,NOW())")->execute([$tid,$uid,$_POST['message']]);
        notify($uid,'info','Ticket Created','Your support ticket '.$num.' has been opened.','/tickets/'.$tid);
        // Notify admin
        $admins=$pdo->query("SELECT id FROM `{$p}users` WHERE role='admin' LIMIT 1")->fetch();
        if($admins)notify($admins['id'],'warning','New Support Ticket',$_POST['subject'],'/admin/tickets/'.$tid);
        logAct('TICKET_CREATE',$num);redirect('/tickets/'.$tid);
    }
}
try{$tickets=$pdo->query("SELECT t.*,(SELECT COUNT(*) FROM `{$p}ticket_replies` WHERE ticket_id=t.id) AS reply_count FROM `{$p}tickets` t WHERE t.user_id=$uid ORDER BY t.updated_at DESC,t.created_at DESC")->fetchAll();}
catch(Exception $e){$tickets=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Support Tickets</div><div class="ps"><?=count($tickets)?> total</div></div>
<button class="btn bp" onclick="oM('tmo')">+ New Ticket</button></div>
<div class="panel"><div class="tw"><table><thead><tr><th>TICKET #</th><th>SUBJECT</th><th>DEPT</th><th>PRIORITY</th><th>REPLIES</th><th>STATUS</th><th>DATE</th><th></th></tr></thead><tbody>
<?php foreach($tickets as $t):?><tr>
<td class="tdp"><?=e($t['ticket_number'])?></td>
<td class="tdc"><?=e($t['subject'])?></td>
<td class="tdm"><?=e($t['department'])?></td>
<td><?=badge($t['priority']??'medium')?></td>
<td><?=(int)$t['reply_count']?></td>
<td><?=badge($t['status'])?></td>
<td class="tdm"><?=formatDate($t['created_at'])?></td>
<td><a href="<?=e(appUrl())?>/tickets/<?=(int)$t['id']?>" class="btn bs bxs">View →</a></td>
</tr><?php endforeach;if(empty($tickets)):?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--mist)">No tickets yet — <a href="#" onclick="oM('tmo');return false" style="color:var(--acid)">open one</a></td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="tmo"><div class="mw"><div class="mh"><div class="mt">New Support Ticket</div><button class="mc" onclick="cM(\'tmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="create"><div class="fg"><label class="fl">SUBJECT</label><input type="text" name="subject" class="fc" placeholder="Describe your issue briefly" required></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">DEPARTMENT</label><select name="department" class="fc"><option value="general">General</option><option value="billing">Billing</option><option value="orders">Orders</option><option value="technical">Technical</option></select></div><div class="fg"><label class="fl">PRIORITY</label><select name="priority" class="fc"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div></div><div class="fg"><label class="fl">MESSAGE</label><textarea name="message" class="fc" rows="5" placeholder="Describe your issue in detail..." required></textarea></div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'tmo\')">Cancel</button><button type="submit" class="btn bp">Submit Ticket →</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/client_wrap.php';
