<?php ob_start();?>
<div style="padding:4rem 0 2rem;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.06),transparent)">
<div class="c"><h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Blog & <span style="color:var(--acid)">Resources</span></h1>
<p style="font-size:.72rem;color:var(--mist)">SEO tips, link building guides, and platform updates</p></div></div>
<div class="c" style="padding:3rem 0">
<?php if(empty($posts)):?><div style="text-align:center;padding:4rem;color:var(--mist)">No posts yet.</div>
<?php else:?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:.9rem">
<?php foreach($posts as $post):?>
<a href="<?=e(appUrl())?>/blog/<?=e($post['slug'])?>" style="display:flex;flex-direction:column;background:var(--card);border:1px solid var(--border);border-radius:4px;overflow:hidden;transition:all .22s;text-decoration:none" onmouseover="this.style.borderColor='rgba(184,255,60,.22)'" onmouseout="this.style.borderColor='var(--border)'">
  <?php if($post['featured_image']):?><img src="<?=e($post['featured_image'])?>" alt="" style="width:100%;height:170px;object-fit:cover">
  <?php else:?><div style="height:130px;background:linear-gradient(135deg,var(--card2),rgba(184,255,60,.04));display:flex;align-items:center;justify-content:center;font-size:2.5rem">📝</div><?php endif;?>
  <div style="padding:1rem;flex:1;display:flex;flex-direction:column">
    <?php if($post['category']):?><div style="font-size:.5rem;font-weight:700;letter-spacing:.1em;color:var(--acid);text-transform:uppercase;margin-bottom:.4rem"><?=e($post['category'])?></div><?php endif;?>
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem;line-height:1.35;margin-bottom:.5rem"><?=e($post['title'])?></div>
    <div style="font-size:.63rem;color:var(--mist);line-height:1.6;flex:1"><?=e(excerpt($post['excerpt']??$post['content']??'',110))?></div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.8rem;padding-top:.7rem;border-top:1px solid var(--border);font-size:.55rem;color:var(--mist)">
      <span><?=e($post['author']??'Team')?></span><span><?=formatDate($post['created_at'],'M j, Y')?></span>
    </div>
  </div>
</a>
<?php endforeach;?>
</div>
<?php if($total>9):?><div style="text-align:center;margin-top:2rem">
  <?php if($page>1):?><a href="?page=<?=$page-1?>" class="btn bs">← Prev</a><?php endif;?>
  <?php if($page*9<$total):?><a href="?page=<?=$page+1?>" class="btn bs">Next →</a><?php endif;?>
</div><?php endif;?>
<?php endif;?>
</div>
<?php $pageContent=ob_get_clean();$pageTitle='Blog — '.appName();include LB_ROOT.'/app/views/public/layout.php';
