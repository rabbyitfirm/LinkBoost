<?php
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode([]);exit;}
$uid=(int)$_SESSION['user_id'];$a=$_GET['action']??'list';
if($a==='list'){echo json_encode(db()->query("SELECT * FROM `".pfx()."notifications` WHERE user_id=$uid ORDER BY created_at DESC LIMIT 20")->fetchAll());}
elseif($a==='read_all'){db()->prepare("UPDATE `".pfx()."notifications` SET is_read=1 WHERE user_id=?")->execute([$uid]);echo json_encode(['ok'=>true]);}
elseif($a==='count'){echo json_encode(['count'=>(int)db()->query("SELECT COUNT(*) FROM `".pfx()."notifications` WHERE user_id=$uid AND is_read=0")->fetchColumn()]);}
