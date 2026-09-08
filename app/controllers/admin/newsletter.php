<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='newsletter';$pageTitle='Newsletter';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $a=$_POST['action']??'';
    if($a==='send'){
        $subs=$pdo->query("SELECT * FROM `{$p}newsletter` WHERE status='subscribed'")->fetchAll();
        $sent=0;foreach($subs as $s){if(sendMail($s['email'],$_POST['subject'],mailTemplate($_POST['subject'],'<p class="p">'.nl2br(htmlspecialchars($_POST['body'])).'</p>','Visit '.appName(),appUrl().'/')))$sent++;}
        $_SESSION['flash_success']="Sent to $sent subscribers!";redirect('/admin/newsletter');
    }
}
$flash=$_SESSION['flash_success']??'';unset($_SESSION['flash_success']);
try{$subs=$pdo->query("SELECT * FROM `{$p}newsletter` ORDER BY created_at DESC")->fetchAll();$count=(int)$pdo->query("SELECT COUNT(*) FROM `{$p}newsletter` WHERE status='subscribed'")->fetchColumn();}
catch(Exception $e){$subs=[];$count=0;}
ob_start();?>
<div class="ph"><div><div class="pt">Newsletter</div><div class="ps"><?=$count?> active subscribers</div></div><button class="btn bp" onclick="oM('nmo')">✉️ Send Newsletter</button></div>
<?php if($flash):?><div class="al al-s">✓ <?=e($flash)?></div><?php endif;?>
<div class="panel"><div class="tw"><table><thead><tr><th>EMAIL</th><th>NAME</th><th>STATUS</th><th>JOINED</th></tr></thead><tbody>
<?php foreach($subs as $s):?><tr>
<td><?=e($s['email'])?></td><td class="tdm"><?=e($s['name']??'—')?></td><td><?=badge($s['status'])?></td><td class="tdm"><?=formatDate($s['created_at'])?></td>
</tr><?php endforeach;if(empty($subs)):?><tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--mist)">No subscribers yet</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();
$modals='<div class="mo" id="nmo"><div class="mw mwl"><div class="mh"><div class="mt">Send Newsletter</div><button class="mc" onclick="cM(\'nmo\')">✕</button></div><div class="mb"><form method="post"><input type="hidden" name="_token" value="'.csrf().'"><input type="hidden" name="action" value="send"><div class="fg"><label class="fl">SUBJECT</label><input type="text" name="subject" class="fc" placeholder="Newsletter subject line" required></div><div class="fg"><label class="fl">BODY (plain text)</label><textarea name="body" class="fc" rows="8" placeholder="Write your newsletter content here..." required></textarea></div><div class="fh">Will send to '.$count.' active subscribers</div><div class="mf"><button type="button" class="btn bs" onclick="cM(\'nmo\')">Cancel</button><button type="submit" class="btn bp">Send to '.$count.' Subscribers →</button></div></form></div></div></div>';
include LB_ROOT.'/app/views/layouts/admin_wrap.php';
