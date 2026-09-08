<?php ob_start();?>
<style>
.price-hero{padding:4.5rem 0 2rem;text-align:center;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)}
.price-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:.9rem;padding:3rem 0}
.price-card{background:var(--card);border:1px solid var(--border);padding:1.6rem;border-radius:4px;display:flex;flex-direction:column;position:relative;transition:border-color .22s}
.price-card.popular{border-color:rgba(184,255,60,.35)}
.price-card:hover{border-color:rgba(184,255,60,.25)}
</style>
<div class="price-hero"><div class="c">
<h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.6rem,4vw,2.6rem);margin-bottom:.5rem">Simple, Transparent <span style="color:var(--acid)">Pricing</span></h1>
<p style="font-size:.75rem;color:var(--mist);line-height:1.7">Start free, upgrade when you need more power. No hidden fees.</p>
</div></div>
<div class="c"><div class="price-grid">
<?php foreach($plans as $pl):$feats=json_decode($pl['features']??'[]',true)??[];?>
<div class="price-card <?=$pl['is_popular']?'popular':''?>">
  <?php if($pl['is_popular']):?><div style="position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:var(--acid);color:var(--ink);font-size:.48rem;font-weight:700;padding:.18em .7em;border-radius:20px;letter-spacing:.06em;white-space:nowrap">MOST POPULAR</div><?php endif;?>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:.3rem"><?=e($pl['name'])?></div>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:2.2rem;color:<?=$pl['price_monthly']>0?'var(--acid)':'var(--green)'?>;line-height:1;margin:.5rem 0">
    <?=$pl['price_monthly']>0?currSym().number_format((float)$pl['price_monthly'],0):'FREE'?><?php if($pl['price_monthly']>0):?><span style="font-size:.6rem;color:var(--mist);font-weight:400">/mo</span><?php endif;?></div>
  <?php if($pl['price_yearly']>0):?><div style="font-size:.6rem;color:var(--mist);margin-bottom:.7rem"><?=currSym().number_format((float)$pl['price_yearly'],0)?>/yr — save <?=round((1-$pl['price_yearly']/($pl['price_monthly']*12))*100)?>%</div><?php endif;?>
  <div style="display:flex;flex-direction:column;gap:.32rem;margin:1rem 0 1.2rem;flex:1">
    <?php foreach($feats as $f):?><div style="font-size:.65rem;display:flex;gap:.4rem"><span style="color:var(--acid)">✓</span><span style="color:var(--mist)"><?=e($f)?></span></div><?php endforeach;?>
  </div>
  <?php if($pl['price_monthly']>0):?><a href="<?=e(appUrl())?>/register" class="btn <?=$pl['is_popular']?'bp':'bs'?>" style="justify-content:center">Get Started →</a>
  <?php else:?><a href="<?=e(appUrl())?>/register" class="btn bs" style="justify-content:center">Start Free</a><?php endif;?>
</div>
<?php endforeach;?></div></div>
<div style="text-align:center;padding:3rem 0 0;border-top:1px solid var(--border)"><div class="c">
<div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:.6rem">Need Enterprise or Custom?</div>
<p style="font-size:.7rem;color:var(--mist);margin-bottom:1.2rem">Volume discounts, white-label, and dedicated support available.</p>
<a href="<?=e(appUrl())?>/contact" class="btn bs" style="padding:.65rem 1.6rem">Contact Us →</a>
</div></div>
<?php $pageContent=ob_get_clean();$pageTitle='Pricing — '.appName();include LB_ROOT.'/app/views/public/layout.php';
