<?php
$AP=$activePage??'';$u=currentUser();$plan=userPlan();
$planColors=['free'=>'var(--mist)','starter'=>'var(--blue)','pro'=>'var(--green)','agency'=>'var(--purple)'];
$planColor=$planColors[$plan]??'var(--mist)';
?>
<div id="sb-ov" onclick="cSB()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <div class="sb-logo"><?=e(appName())?></div>
    <span class="sb-plan" style="background:rgba(255,255,255,.07);color:<?=$planColor?>"><?=strtoupper($plan)?></span>
  </div>
  <nav class="sb-nav">
    <div class="sb-sec">MAIN</div>
    <a href="<?=e(appUrl())?>/dashboard" class="nl <?=$AP==='dashboard'?'active':''?>"><span class="ni">⬛</span>Dashboard</a>
    <a href="<?=e(appUrl())?>/orders" class="nl <?=$AP==='orders'?'active':''?>"><span class="ni">📋</span>My Orders</a>
    <a href="<?=e(appUrl())?>/orders/new" class="nl <?=$AP==='new_order'?'active':''?>"><span class="ni">➕</span>New Order</a>
    <a href="<?=e(appUrl())?>/invoices" class="nl <?=$AP==='invoices'?'active':''?>"><span class="ni">🧾</span>Invoices</a>
    <div class="sb-sec">SEO TOOLS</div>
    <a href="<?=e(appUrl())?>/projects" class="nl <?=$AP==='projects'?'active':''?>"><span class="ni">🗂</span>Projects</a>
    <a href="<?=e(appUrl())?>/rank-tracker" class="nl <?=$AP==='rank_tracker'?'active':''?>"><span class="ni">📈</span>Rank Tracker</a>
    <a href="<?=e(appUrl())?>/site-audit" class="nl <?=$AP==='site_audit'?'active':''?>"><span class="ni">🔍</span>Site Audit</a>
    <a href="<?=e(appUrl())?>/seo-tools" class="nl <?=$AP==='seo_tools'?'active':''?>"><span class="ni">🛠</span>SEO Tools</a>
    <a href="<?=e(appUrl())?>/ask-ai" class="nl <?=$AP==='ask_ai'?'active':''?>"><span class="ni">✨</span>Ask AI</a>
    <div class="sb-sec">AI AUTOPILOT</div>
    <a href="<?=e(appUrl())?>/autopilot" class="nl <?=$AP==='autopilot'?'active':''?>"><span class="ni">🤖</span>AI Autopilot<span class="nb" style="background:var(--acid);color:var(--ink)">NEW</span></a>
    <a href="<?=e(appUrl())?>/keyword-planner" class="nl <?=$AP==='kw_planner'?'active':''?>"><span class="ni">🎯</span>Keyword Planner</a>
    <a href="<?=e(appUrl())?>/content-writer" class="nl <?=$AP==='content_writer'?'active':''?>"><span class="ni">✍️</span>AI Content Writer</a>
    <a href="<?=e(appUrl())?>/competitor" class="nl <?=$AP==='competitor'?'active':''?>"><span class="ni">🕵️</span>Competitor Spy</a>
    <a href="<?=e(appUrl())?>/on-page" class="nl <?=$AP==='on_page'?'active':''?>"><span class="ni">📄</span>On-Page Optimizer</a>
    <a href="<?=e(appUrl())?>/link-prospector" class="nl <?=$AP==='link_pros'?'active':''?>"><span class="ni">🔎</span>Link Prospector</a>
    <a href="<?=e(appUrl())?>/seo-report" class="nl <?=$AP==='seo_report'?'active':''?>"><span class="ni">📊</span>AI SEO Report</a>
    <div class="sb-sec">ACCOUNT</div>
    <a href="<?=e(appUrl())?>/billing" class="nl <?=$AP==='billing'?'active':''?>"><span class="ni">💎</span>Billing & Plans</a>
    <a href="<?=e(appUrl())?>/profile" class="nl <?=$AP==='profile'?'active':''?>"><span class="ni">👤</span>Profile</a>
    <a href="<?=e(appUrl())?>/tickets" class="nl <?=$AP==='tickets'?'active':''?>"><span class="ni">🎫</span>Support Tickets</a>
    <a href="<?=e(appUrl())?>/referrals" class="nl <?=$AP==='referrals'?'active':''?>"><span class="ni">🎁</span>Referrals</a>
    <a href="<?=e(appUrl())?>/publishers" class="nl"><span class="ni">🌐</span>Publisher Directory</a>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">
      <?php if($u['avatar']??''): ?>
      <img src="<?=e($u['avatar'])?>" class="sb-av" style="object-fit:cover">
      <?php else: ?>
      <div class="sb-av"><?=strtoupper(substr($u['name']??'U',0,1))?></div>
      <?php endif; ?>
      <div><div class="sb-nm"><?=e($u['name']??'')?></div><div class="sb-rl"><?=strtoupper($plan)?> PLAN</div></div>
      <a href="<?=e(appUrl())?>/logout" class="sb-lo" title="Logout">⏻</a>
    </div>
  </div>
</aside>
