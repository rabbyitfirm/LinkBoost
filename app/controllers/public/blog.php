<?php
$page=max(1,(int)($_GET['page']??1));$perPage=9;$offset=($page-1)*$perPage;
try{$posts=db()->query("SELECT b.*,u.name AS author FROM `".pfx()."blog_posts` b LEFT JOIN `".pfx()."users` u ON u.id=b.author_id WHERE b.status='published' ORDER BY b.created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
$total=(int)db()->query("SELECT COUNT(*) FROM `".pfx()."blog_posts` WHERE status='published'")->fetchColumn();
}catch(Exception $e){$posts=[];$total=0;}
require LB_ROOT.'/app/views/public/blog.php';
