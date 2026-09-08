<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='logs';$pageTitle='Activity Log';
try{$logs=$pdo->query("SELECT l.*,u.name AS un FROM `{$p}activity_log` l LEFT JOIN `{$p}users` u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 500")->fetchAll();}catch(Exception $e){$logs=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Activity Log</div><div class="ps">Last 500 events</div></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>USER</th><th>ACTION</th><th>DESCRIPTION</th><th>IP</th><th>TIME</th></tr></thead><tbody>
<?php foreach($logs as $l):?><tr>
<td class="tdp"><?=e($l['un']??'System')?></td>
<td><?php $ac=$l['action']??'';$cls=str_contains($ac,'DELETE')||str_contains($ac,'FAIL')?'b-danger':(str_contains($ac,'CREATE')||str_contains($ac,'REGISTER')?'b-success':'b-info');?><span class="bdg <?=$cls?>"><?=e($ac)?></span></td>
<td class="tdc"><?=e($l['description']??'')?></td><td class="tdm" style="font-size:.58rem"><?=e($l['ip_address']??'')?></td>
<td class="tdm"><?=formatDate($l['created_at'],'M j H:i')?></td>
</tr><?php endforeach;if(empty($logs)):?><tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--mist)">No activity yet</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
