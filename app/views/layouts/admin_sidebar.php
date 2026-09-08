<?php
$pO=0;$nM=0;
try{$pO=(int)db()->query("SELECT COUNT(*) FROM `".pfx()."orders` WHERE status='pending'")->fetchColumn();}catch(Exception $e){}
try{$nM=(int)db()->query("SELECT COUNT(*) FROM `".pfx()."contact_messages` WHERE is_read=0")->fetchColumn();}catch(Exception $e){}
$AP=$activePage??'';
$nav=['OVERVIEW'=>[['⬛','Dashboard','/admin','dashboard'],],'BUSINESS'=>[['📋','Orders','/admin/orders','orders',$pO],['💳','Payments','/admin/payments','payments'],['🧾','Invoices','/admin/invoices','invoices'],['📦','Services','/admin/services','services'],],'CLIENTS'=>[['👥','Clients','/admin/clients','clients'],['💎','Plans','/admin/plans','plans'],],'CONTENT'=>[['🎨','Customizer','/admin/customizer','customizer'],['📝','Blog','/admin/blog','blog'],['📄','Pages','/admin/pages','pages'],['✉️','Messages','/admin/messages','messages',$nM],],'AI TOOLS'=>[['🤖','AI Autopilot','/admin/autopilot','autopilot'],],'CONTENT 2'=>[['💬','Tickets','/admin/tickets','tickets'],['🏷','Coupons','/admin/coupons','coupons'],['📰','Newsletter','/admin/newsletter','newsletter'],['⭐','Testimonials','/admin/testimonials','testimonials'],['🌐','Publishers','/admin/publishers','publishers'],['📊','Reports','/admin/reports','reports'],],'SYSTEM'=>[['👤','Staff','/admin/staff','staff'],['⚙','Settings','/admin/settings','settings'],['📊','Activity Log','/admin/logs','logs'],]];
?>
<div id="sb-ov" onclick="cSB()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <div class="sb-logo"><?=e(appName())?></div>
    <span class="sb-plan" style="background:rgba(255,77,109,.15);color:var(--red)">ADMIN</span>
  </div>
  <nav class="sb-nav">
    <?php foreach($nav as $sec=>$links): ?>
    <div class="sb-sec"><?=e($sec)?></div>
    <?php foreach($links as $l): [$ico,$lbl,$url,$key]=array_pad($l,4,null);$badge=$l[4]??0; ?>
    <a href="<?=e(appUrl().$url)?>" class="nl <?=$AP===$key?'active':''?>">
      <span class="ni"><?=$ico?></span><?=e($lbl)?>
      <?php if($badge>0):?><span class="nb"><?=$badge?></span><?php endif;?>
    </a>
    <?php endforeach;endforeach;?>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">
      <div class="sb-av"><?=strtoupper(substr($user['name']??'A',0,1))?></div>
      <div><div class="sb-nm"><?=e($user['name']??'Admin')?></div><div class="sb-rl">ADMIN</div></div>
      <a href="<?=e(appUrl())?>/logout" class="sb-lo" title="Logout">⏻</a>
    </div>
  </div>
</aside>
