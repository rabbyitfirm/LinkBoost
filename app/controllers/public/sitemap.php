<?php
header('Content-Type: application/xml; charset=utf-8');
$base=appUrl();
$urls=[[$base.'/',1.0,'daily'],[$base.'/services',0.9,'weekly'],[$base.'/pricing',0.9,'weekly'],[$base.'/tools',0.8,'weekly'],[$base.'/blog',0.8,'daily'],[$base.'/contact',0.5,'monthly'],[$base.'/ask-ai',0.7,'weekly']];
try{$posts=db()->query("SELECT slug,updated_at,created_at FROM `".pfx()."blog_posts` WHERE status='published' ORDER BY created_at DESC LIMIT 200")->fetchAll();foreach($posts as $p)$urls[]=[$base.'/blog/'.rawurlencode($p['slug']),0.7,'weekly',$p['updated_at']??$p['created_at']];}catch(Exception $e){}
echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($urls as $u):[$loc,$pri,$freq]=array_pad($u,3,'weekly');$mod=isset($u[3])?date('Y-m-d',strtotime($u[3])):date('Y-m-d');?>
<url><loc><?=e($loc)?></loc><lastmod><?=$mod?></lastmod><changefreq><?=$freq?></changefreq><priority><?=$pri?></priority></url>
<?php endforeach;?>
</urlset>
