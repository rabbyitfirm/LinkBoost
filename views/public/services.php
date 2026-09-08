<?php ob_start();?>
<style>
.ph2{padding:4rem 0 2.5rem;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)}
.ph2 h1{font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.6rem,4vw,2.6rem);margin-bottom:.5rem}
.svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.9rem;padding:3rem 0}
.svc-card{background:var(--card);border:1px solid var(--border);padding:1.4rem;border-radius:4px;transition:all .22s;display:flex;flex-direction:column}
.svc-card:hover{border-color:rgba(184,255,60,.22);transform:translateY(-3px)}
</style>
<div class="ph2"><div class="c"><div class="bdg2" style="display:inline-block;background:rgba(184,255,60,.1);color:var(--acid);font-size:.52rem;font-weight:700;letter-spacing:.1em;padding:.2em .7em;border-radius:20px;margin-bottom:1rem">LINK BUILDING MARKETPLACE</div>
<h1>Premium Backlink <span style="color:var(--acid)">Services</span></h1>
<p style="font-size:.75rem;color:var(--mist);line-height:1.7;max-width:500px">Every link manually verified. Real websites, real traffic, real rankings.</p></div></div>
<div class="c"><div class="svc-grid">
<?php foreach($services as $svc):?>
<div class="svc-card">
  <div style="font-size:.48rem;font-weight:700;letter-spacing:.07em;color:var(--acid);background:rgba(184,255,60,.08);padding:.12em .42em;border-radius:2px;display:inline-block;margin-bottom:.7rem"><?=e(strtoupper(str_replace('_',' ',$svc['type'])))?></div>
  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:.4rem"><?=e($svc['name'])?></div>
  <?php if($svc['niche']):?><div style="font-size:.58rem;color:var(--mist);margin-bottom:.5rem">Niche: <?=e($svc['niche'])?></div><?php endif;?>
  <?php if($svc['description']):?><p style="font-size:.66rem;color:var(--mist);line-height:1.7;margin-bottom:.8rem"><?=e($svc['description'])?></p><?php endif;?>
  <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.8rem">
    <span style="font-size:.55rem;background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.14em .48em;border-radius:2px">DA<?=(int)$svc['min_da']?>+</span>
    <span style="font-size:.55rem;background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.14em .48em;border-radius:2px">DR<?=(int)$svc['min_dr']?>+</span>
    <span style="font-size:.55rem;background:rgba(255,255,255,.04);border:1px solid var(--border);padding:.14em .48em;border-radius:2px">⏱<?=(int)$svc['turnaround_days']?> days</span>
    <?php if($svc['is_featured']):?><span style="font-size:.55rem;background:rgba(184,255,60,.08);color:var(--acid);border:1px solid rgba(184,255,60,.2);padding:.14em .48em;border-radius:2px">★ FEATURED</span><?php endif;?>
  </div>
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto">
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:var(--acid)"><?=currSym()?><?=number_format((float)$svc['price'],0)?></div>
    <a href="<?=e(appUrl())?>/orders/new" class="btn bp" style="font-size:.64rem">Order Now →</a>
  </div>
</div>
<?php endforeach;if(empty($services)):?><div style="text-align:center;padding:4rem;color:var(--mist)">No services available yet.</div><?php endif;?>
</div></div>
<div style="background:rgba(184,255,60,.04);border-top:1px solid rgba(184,255,60,.12);padding:3rem 0;text-align:center">
<div class="c"><div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;margin-bottom:.5rem">Need a Custom Package?</div>
<p style="font-size:.72rem;color:var(--mist);margin-bottom:1.2rem">Contact us for bulk orders, custom niches, or enterprise agreements.</p>
<a href="<?=e(appUrl())?>/contact" class="btn bp" style="padding:.7rem 1.8rem">Talk to Us →</a></div></div>
<?php $pageContent=ob_get_clean();$pageTitle='Services — '.appName();include LB_ROOT.'/app/views/public/layout.php';
