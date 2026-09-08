<?php
try{
    $niche=$_GET['niche']??'';$type=$_GET['type']??'';
    $wh="WHERE status=1";if($niche)$wh.=" AND niche LIKE ".db()->quote('%'.$niche.'%');if($type)$wh.=" AND link_type='$type'";
    $pubs=db()->query("SELECT * FROM `".pfx()."publishers` $wh ORDER BY is_featured DESC,da DESC")->fetchAll();
    $niches=db()->query("SELECT DISTINCT niche FROM `".pfx()."publishers` WHERE status=1 AND niche IS NOT NULL ORDER BY niche")->fetchAll(PDO::FETCH_COLUMN);
}catch(Exception $e){$pubs=[];$niches=[];}
ob_start();?>
<style>
.pub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:.85rem;padding:2.5rem 0}
.pub-card{background:var(--card);border:1px solid var(--border);padding:1.2rem;border-radius:4px;transition:all .22s;position:relative}
.pub-card:hover{border-color:rgba(184,255,60,.22);transform:translateY(-2px)}
</style>
<div style="padding:4rem 0 2rem;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)">
<div class="c"><h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Publisher <span style="color:var(--acid)">Directory</span></h1>
<p style="font-size:.72rem;color:var(--mist)">Real websites available for guest posts & niche edits. All manually verified.</p></div></div>
<div class="c">
<div style="display:flex;gap:.4rem;flex-wrap:wrap;padding:1.5rem 0 0;align-items:center">
  <a href="<?=e(appUrl())?>/publishers" class="ftab <?=!$niche&&!$type?'active':''?>">All</a>
  <?php foreach($niches as $n):?><a href="?niche=<?=urlencode($n)?>" class="ftab <?=$niche===$n?'active':''?>"><?=e($n)?></a><?php endforeach;?>
  <div style="margin-left:auto;display:flex;gap:.3rem">
    <a href="?type=guest_post<?=$niche?'&niche='.urlencode($niche):''?>" class="ftab <?=$type==='guest_post'?'active':''?>">Guest Posts</a>
    <a href="?type=niche_edit<?=$niche?'&niche='.urlencode($niche):''?>" class="ftab <?=$type==='niche_edit'?'active':''?>">Niche Edits</a>
  </div>
</div>
<div class="pub-grid">
<?php foreach($pubs as $pub):?>
<div class="pub-card">
  <?php if($pub['is_featured']):?><div style="position:absolute;top:.65rem;right:.65rem;background:rgba(184,255,60,.12);color:var(--acid);font-size:.48rem;font-weight:700;padding:.1em .45em;border-radius:2px;letter-spacing:.06em">FEATURED</div><?php endif;?>
  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.9rem;margin-bottom:.5rem;color:var(--acid)">🌐 <?=e($pub['domain'])?></div>
  <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-bottom:.7rem">
    <span style="font-size:.56rem;background:rgba(84,160,255,.12);color:var(--blue);padding:.12em .45em;border-radius:2px;font-weight:700">DA <?=(int)$pub['da']?></span>
    <span style="font-size:.56rem;background:rgba(162,155,254,.12);color:var(--purple);padding:.12em .45em;border-radius:2px;font-weight:700">DR <?=(int)$pub['dr']?></span>
    <?php if($pub['traffic']):?><span style="font-size:.56rem;background:rgba(85,239,196,.1);color:var(--green);padding:.12em .45em;border-radius:2px">📈 <?=e($pub['traffic'])?></span><?php endif;?>
    <span style="font-size:.56rem;background:rgba(255,255,255,.06);color:var(--mist);padding:.12em .45em;border-radius:2px"><?=e($pub['niche']??'General')?></span>
  </div>
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div><div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;color:var(--acid)"><?=currSym()?><?=number_format((float)$pub['price'],0)?></div>
    <div style="font-size:.56rem;color:var(--mist)"><?=e(str_replace('_',' ',ucfirst($pub['link_type']??'guest_post')))?></div></div>
    <a href="<?=e(appUrl())?>/orders/new" class="btn bp bsm">Order →</a>
  </div>
</div>
<?php endforeach;if(empty($pubs)):?><div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--mist)">No publishers found for this filter.</div><?php endif;?>
</div></div>
<?php $pageContent=ob_get_clean();$pageTitle='Publisher Directory — '.appName();include LB_ROOT.'/app/views/public/layout.php';
