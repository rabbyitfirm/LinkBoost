<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='tools';$pageTitle='SEO Tools Stats';
try{$usage=$pdo->query("SELECT tool,COUNT(*) AS uses FROM `{$p}tool_usage` GROUP BY tool ORDER BY uses DESC LIMIT 20")->fetchAll();$total=$pdo->query("SELECT COUNT(*) FROM `{$p}tool_usage`")->fetchColumn();}catch(Exception $e){$usage=[];$total=0;}
ob_start();?>
<div class="ph"><div><div class="pt">SEO Tools Usage</div><div class="ps"><?=(int)$total?> total uses</div></div></div>
<div class="panel"><div class="tw"><table><thead><tr><th>TOOL</th><th>USES</th><th>PERCENTAGE</th></tr></thead><tbody>
<?php foreach($usage as $u):$pct=$total>0?round($u['uses']/$total*100):0;?><tr>
<td class="tdp"><?=e($u['tool'])?></td><td style="color:var(--acid);font-weight:700"><?=(int)$u['uses']?></td>
<td><div style="display:flex;align-items:center;gap:.5rem"><div class="pb" style="width:120px;margin:0"><div class="pf" style="width:<?=$pct?>%;background:var(--acid)"></div></div><span class="tdm"><?=$pct?>%</span></div></td>
</tr><?php endforeach;if(empty($usage)):?><tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--mist)">No tool usage yet</td></tr><?php endif;?>
</tbody></table></div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
