<?php ob_start();?>
<article style="max-width:760px;margin:0 auto;padding:3.5rem 1.5rem">
  <?php if($post['featured_image']):?><img src="<?=e($post['featured_image'])?>" alt="" style="width:100%;border-radius:4px;margin-bottom:2rem;max-height:350px;object-fit:cover"><?php endif;?>
  <?php if($post['category']):?><div style="font-size:.52rem;font-weight:700;letter-spacing:.1em;color:var(--acid);text-transform:uppercase;margin-bottom:.8rem"><?=e($post['category'])?></div><?php endif;?>
  <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.4rem,3.5vw,2.2rem);line-height:1.15;margin-bottom:.8rem"><?=e($post['title'])?></h1>
  <div style="display:flex;gap:1rem;align-items:center;margin-bottom:2.5rem;font-size:.62rem;color:var(--mist);border-bottom:1px solid var(--border);padding-bottom:1rem">
    <span>By <?=e($post['author']??'Team')?></span>
    <span>•</span><span><?=formatDate($post['created_at'],'M j, Y')?></span>
    <span>•</span><span><?=(int)$post['views']?> views</span>
  </div>
  <div style="font-size:.78rem;line-height:1.9;color:var(--paper)"><?=$post['content']?></div>
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border)">
    <a href="<?=e(appUrl())?>/blog" style="color:var(--acid);font-size:.68rem;text-decoration:none">← Back to Blog</a>
  </div>
</article>
<div style="background:rgba(184,255,60,.04);border-top:1px solid rgba(184,255,60,.12);padding:3rem 0;text-align:center">
<div class="c"><div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;margin-bottom:.5rem">Want Better Rankings?</div>
<p style="font-size:.7rem;color:var(--mist);margin-bottom:1.2rem">Try our premium link building services and SEO tools.</p>
<a href="<?=e(appUrl())?>/register" class="btn bp" style="padding:.7rem 1.8rem">Start Free →</a>
</div></div>
<?php $pageContent=ob_get_clean();
$pageTitle=e($post['meta_title']??$post['title'].' — '.appName());
$pageMeta='<meta name="description" content="'.e($post['meta_desc']??excerpt($post['content']??'',155)).'">';
include LB_ROOT.'/app/views/public/layout.php';
