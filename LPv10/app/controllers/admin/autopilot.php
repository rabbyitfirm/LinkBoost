<?php
requireAdmin();$pdo=db();$p=pfx();$user=currentUser();$activePage='autopilot';$pageTitle='AI Autopilot Stats';
try{$uses=$pdo->query("SELECT u.name,u.email,l.created_at FROM `{$p}activity_log` l JOIN `{$p}users` u ON u.id=l.user_id WHERE l.action LIKE 'AUTOPILOT%' ORDER BY l.created_at DESC LIMIT 50")->fetchAll();
$stats=['kw_uses'=>$pdo->query("SELECT COUNT(*) FROM `{$p}tool_usage` WHERE tool='keyword_planner'")->fetchColumn(),'cw_uses'=>$pdo->query("SELECT COUNT(*) FROM `{$p}tool_usage` WHERE tool='content_writer'")->fetchColumn(),'ap_uses'=>$pdo->query("SELECT COUNT(*) FROM `{$p}tool_usage` WHERE tool='autopilot'")->fetchColumn(),'report_uses'=>$pdo->query("SELECT COUNT(*) FROM `{$p}tool_usage` WHERE tool='seo_report'")->fetchColumn()];}catch(Exception $e){$uses=[];$stats=array_fill_keys(['kw_uses','cw_uses','ap_uses','report_uses'],0);}
ob_start();?>
<div class="ph"><div><div class="pt">AI Autopilot Usage</div></div></div>
<div class="sg">
  <div class="sc"><div class="si">🤖</div><div class="sl">AUTOPILOT RUNS</div><div class="sv"><?=(int)$stats['ap_uses']?></div></div>
  <div class="sc"><div class="si">🎯</div><div class="sl">KEYWORD PLANS</div><div class="sv"><?=(int)$stats['kw_uses']?></div></div>
  <div class="sc"><div class="si">✍️</div><div class="sl">CONTENT WRITTEN</div><div class="sv"><?=(int)$stats['cw_uses']?></div></div>
  <div class="sc"><div class="si">📊</div><div class="sl">REPORTS GENERATED</div><div class="sv"><?=(int)$stats['report_uses']?></div></div>
</div>
<div class="panel"><div class="pnh"><div class="pnt">AI Feature Usage</div></div>
<div style="padding:1rem;display:flex;flex-direction:column;gap:.6rem">
  <?php foreach([['🤖','AI Autopilot','/autopilot','Full SEO analysis'],['🎯','Keyword Planner','/keyword-planner','Keyword research'],['✍️','Content Writer','/content-writer','AI content'],['🕵️','Competitor Spy','/competitor','Competitive analysis'],['📄','On-Page Optimizer','/on-page','Page optimization'],['🔎','Link Prospector','/link-prospector','Link opportunities'],['📊','SEO Report','/seo-report','Monthly reports']] as [$ico,$name,$url,$desc]):?>
  <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .8rem;background:var(--card2);border:1px solid var(--border);border-radius:3px">
    <div style="display:flex;gap:.6rem;align-items:center">
      <span style="font-size:1.1rem"><?=$ico?></span>
      <div><div style="font-family:Syne,sans-serif;font-weight:600;font-size:.75rem"><?=e($name)?></div><div style="font-size:.6rem;color:var(--mist)"><?=e($desc)?></div></div>
    </div>
    <a href="<?=e(appUrl().$url)?>" target="_blank" class="btn bs bxs">Preview →</a>
  </div>
  <?php endforeach;?>
</div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
