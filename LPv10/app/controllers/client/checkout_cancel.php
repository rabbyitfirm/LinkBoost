<?php
requireLogin();$activePage='billing';$pageTitle='Payment Cancelled';
ob_start();?>
<div style="max-width:480px;margin:4rem auto;text-align:center;padding:1rem">
  <div style="font-size:3.5rem;margin-bottom:.8rem">↩️</div>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;margin-bottom:.5rem">Payment Cancelled</div>
  <div style="font-size:.72rem;color:var(--mist);line-height:1.8;margin-bottom:1.5rem">
    No charges were made. You can try again anytime.
  </div>
  <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
    <a href="<?=e(appUrl())?>/billing" class="btn bp">View Plans →</a>
    <a href="<?=e(appUrl())?>/dashboard" class="btn bs">Dashboard</a>
  </div>
</div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
