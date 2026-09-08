<?php
header('Content-Type: application/json');
$url=trim(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH),'/');
if(str_contains($url,'unsubscribe')){
    $email=$_GET['email']??'';
    if($email){db()->prepare("UPDATE `".pfx()."newsletter` SET status='unsubscribed' WHERE email=?")->execute([$email]);}
    echo json_encode(['ok'=>true,'msg'=>'Unsubscribed']);exit;
}
$email=trim($_POST['email']??$_GET['email']??'');
$name=trim($_POST['name']??'');
if(!$email||!filter_var($email,FILTER_VALIDATE_EMAIL)){echo json_encode(['ok'=>false,'msg'=>'Valid email required']);exit;}
try{db()->prepare("INSERT INTO `".pfx()."newsletter`(email,name,status,created_at)VALUES(?,?,'subscribed',NOW()) ON DUPLICATE KEY UPDATE status='subscribed'")->execute([$email,$name]);
sendMail($email,'Welcome to '.appName().' Newsletter',mailTemplate('Thanks for subscribing!','<p class="p">You are now subscribed to the '.appName().' newsletter. You will receive SEO tips, platform updates, and exclusive offers.</p>','Visit Platform',appUrl().'/'));
echo json_encode(['ok'=>true,'msg'=>'Subscribed! Check your email.']);}
catch(Exception $e){echo json_encode(['ok'=>false,'msg'=>'Error: '.$e->getMessage()]);}
