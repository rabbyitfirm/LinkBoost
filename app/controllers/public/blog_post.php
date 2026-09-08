<?php
$slug=$GLOBALS['route_params'][0]??'';
if(!$slug)redirect('/blog');
try{$s=db()->prepare("SELECT b.*,u.name AS author FROM `".pfx()."blog_posts` b LEFT JOIN `".pfx()."users` u ON u.id=b.author_id WHERE b.slug=? AND b.status='published' LIMIT 1");$s->execute([$slug]);$post=$s->fetch();}
catch(Exception $e){$post=null;}
if(!$post){http_response_code(404);require LB_ROOT.'/app/views/errors/404.php';exit;}
db()->prepare("UPDATE `".pfx()."blog_posts` SET views=views+1 WHERE id=?")->execute([$post['id']]);
require LB_ROOT.'/app/views/public/blog_post.php';
