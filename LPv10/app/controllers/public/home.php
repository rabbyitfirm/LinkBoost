<?php
try{
    $pdo=db();$p=pfx();
    $services=$pdo->query("SELECT * FROM `{$p}services` WHERE status=1 AND is_featured=1 ORDER BY sort_order LIMIT 6")->fetchAll();
    $posts=$pdo->query("SELECT id,title,slug,excerpt,category,featured_image,created_at FROM `{$p}blog_posts` WHERE status='published' ORDER BY created_at DESC LIMIT 3")->fetchAll();
    $cfg=$pdo->query("SELECT `key`,`value` FROM `{$p}settings`")->fetchAll(PDO::FETCH_KEY_PAIR);
}catch(Exception $e){$services=$posts=[];$cfg=[];}
require LB_ROOT.'/app/views/public/home.php';
