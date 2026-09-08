<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='messages';$pageTitle='Messages';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$a=$_POST['action']??'';$id=(int)($_POST['id']??0);if($a==='read')$pdo->prepare("UPDATE `{$p}contact_messages` SET is_read=1 WHERE id=?")->execute([$id]);if($a==='delete')$pdo->prepare("DELETE FROM `{$p}contact_messages` WHERE id=?")->execute([$id]);redirect('/admin/messages');}
try{$msgs=$pdo->query("SELECT * FROM `{$p}contact_messages` ORDER BY created_at DESC")->fetchAll();}catch(Exception $e){$msgs=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Messages</div><div class="ps"><?=count(array_filter($msgs,fn($m)=>!$m['is_read']))?> unread</div></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th></th><th>NAME</th><th>EMAIL</th><th>SUBJECT</th><th>MESSAGE</th><th>DATE</th><th>ACTIONS</th></tr></thead><tbody>
<?php foreach($msgs as $m):?><tr style="<?=$m['is_read']?'opacity:.65':''?>">
<td><?=$m['is_read']?'':'<span style="color:var(--acid)">●</span>'?></td>
<td class="tdp"><?=e($m['name'])?></td><td><?=e($m['email'])?></td>
<td class="tdc"><?=e($m['subject']??'—')?></td>
<td class="tdc"><?=e(substr($m['message']??'',0,60))?>...</td>
<td class="tdm"><?=formatDate($m['created_at'])?></td>
<td><div class="tda">
<?php if(!$m['is_read']):?><form method="post" style="display:inline"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="read"><input type="hidden" name="id" value="<?=(int)$m['id']?>"><button class="btn bi bxs">Read</button></form><?php endif;?>
<a href="mailto:<?=e($m['email'])?>" class="btn bs bxs">Reply</a>
<form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="_token" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$m['id']?>"><button class="btn bd bxs">✕</button></form>
</div></td></tr>
<?php endforeach;if(empty($msgs)):?><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--mist)">No messages</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
