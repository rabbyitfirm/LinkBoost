<?php
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $body=json_decode(file_get_contents('php://input'),true)??[];
    if($body['_log']??false){
        try{$tool=$body['tool']??'unknown';db()->prepare("INSERT INTO `".pfx()."tool_usage`(user_id,tool,created_at)VALUES(?,?,NOW())")->execute([$_SESSION['user_id']??null,$tool]);}
        catch(Exception $e){}
        echo json_encode(['ok'=>true]);exit;
    }
    if(isLoggedIn()){
        try{$url=$body['url']??'';if(!$url){echo json_encode(['error'=>'URL required']);exit;}
        db()->prepare("INSERT INTO `".pfx()."audits`(user_id,url,status,created_at)VALUES(?,?,'completed',NOW())")->execute([$_SESSION['user_id'],$url]);
        echo json_encode(['ok'=>true,'id'=>db()->lastInsertId()]);}
        catch(Exception $e){echo json_encode(['error'=>$e->getMessage()]);}
    }
}
