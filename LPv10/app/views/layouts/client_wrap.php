<?php
$user=currentUser();
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($pageTitle??'Dashboard')?> — <?=e(appName())?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php include LB_ROOT.'/app/views/layouts/css.php'; ?>
</head><body>
<?php include LB_ROOT.'/app/views/layouts/client_sidebar.php'; ?>
<div class="main">
<div class="topbar">
  <div class="tb-l"><button class="hbtn" onclick="oSB()">☰</button><div class="tb-title"><?=e($pageTitle??'Dashboard')?></div></div>
  <div class="tb-r">
    <div style="position:relative;margin-right:.2rem">
      <button onclick="toggleNotif()" id="notifBtn" style="background:none;border:none;color:var(--mist);cursor:pointer;font-size:1rem;padding:.3rem;position:relative">
        🔔
        <?php try{$nc=(int)db()->query("SELECT COUNT(*) FROM `".pfx()."notifications` WHERE user_id=".((int)($_SESSION['user_id']??0))." AND is_read=0")->fetchColumn();if($nc>0):?><span style="position:absolute;top:0;right:0;width:16px;height:16px;background:var(--red);color:#fff;border-radius:50%;font-size:.5rem;display:flex;align-items:center;justify-content:center;font-weight:700"><?=$nc?></span><?php endif;}catch(Exception $e){} ?>
      </button>
      <div id="notifPanel" style="display:none;position:absolute;right:0;top:calc(100% + 6px);width:300px;background:var(--card);border:1px solid var(--border);border-radius:4px;z-index:500;box-shadow:0 12px 32px rgba(0,0,0,.4);max-height:360px;overflow-y:auto">
        <div style="padding:.6rem .85rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
          <span style="font-family:Syne,sans-serif;font-weight:700;font-size:.74rem">Notifications</span>
          <button onclick="markAllRead()" style="background:none;border:none;color:var(--acid);cursor:pointer;font-size:.58rem">Mark all read</button>
        </div>
        <div id="notifList" style="padding:.3rem 0"><div style="padding:1rem;text-align:center;color:var(--mist);font-size:.66rem">Loading...</div></div>
      </div>
    </div>
    <a href="<?=e(appUrl())?>/orders/new" class="btn bp bsm">+ Order</a>
    <?php if(userPlan()==='free'):?><a href="<?=e(appUrl())?>/billing" class="btn bsm" style="background:linear-gradient(135deg,var(--acid),#7dff00);color:var(--ink)">⚡ Upgrade</a><?php endif;?>
  </div>
</div>
<div class="pg"><?=$pageContent??''?></div></div>
<?=$modals??''?>
<?=$js??''?>
<script>
function toggleNotif(){var p=document.getElementById('notifPanel');if(p.style.display==='none'){p.style.display='block';loadNotifs();}else p.style.display='none';}
document.addEventListener('click',function(e){if(!e.target.closest('#notifBtn')&&!e.target.closest('#notifPanel'))document.getElementById('notifPanel').style.display='none';});
function loadNotifs(){
  fetch('<?=e(appUrl())?>/api/notifications?action=list').then(r=>r.json()).then(d=>{
    var html=d.length?d.map(n=>`<div style="padding:.55rem .85rem;border-bottom:1px solid rgba(255,255,255,.04);display:flex;gap:.5rem;align-items:flex-start;background:${n.is_read?'':'rgba(184,255,60,.03)'}">
      <span style="font-size:.9rem;flex-shrink:0">${{info:'ℹ️',success:'✅',warning:'⚠️',order:'📋',billing:'💳',system:'⚙️'}[n.type]||'🔔'}</span>
      <div><div style="font-size:.66rem;font-weight:600">${n.title}</div><div style="font-size:.6rem;color:var(--mist)">${n.message||''}</div></div>
    </div>`).join(''):'<div style="padding:1rem;text-align:center;color:var(--mist);font-size:.66rem">No notifications</div>';
    document.getElementById('notifList').innerHTML=html;
    fetch('<?=e(appUrl())?>/api/notifications?action=read_all');
    document.querySelectorAll('[id^="notifBtn"] span').forEach(e=>e.remove());
  }).catch(()=>{document.getElementById('notifList').innerHTML='<div style="padding:1rem;text-align:center;color:var(--mist);font-size:.66rem">Could not load</div>';});
}
function markAllRead(){fetch('<?=e(appUrl())?>/api/notifications?action=read_all').then(()=>loadNotifs());}
</script>
</body></html>
