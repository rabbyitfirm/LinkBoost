<?php
header('Content-Type: application/json');
$method=$_SERVER['REQUEST_METHOD'];
$endpoint=$GLOBALS['route_params'][0]??'';

// Auth via API key header or query param
$key=$_SERVER['HTTP_X_API_KEY']??$_GET['api_key']??'';
$apiUser=null;
if($key){
    try{$s=db()->prepare("SELECT * FROM `".pfx()."users` WHERE api_key=? AND status=1 LIMIT 1");$s->execute([$key]);$apiUser=$s->fetch()?:null;}
    catch(Exception $e){}
}
if(!$apiUser){http_response_code(401);echo json_encode(['error'=>'Invalid or missing API key','hint'=>'Pass X-API-Key header or ?api_key= parameter']);exit;}

// Check API access on plan
$plan=db()->prepare("SELECT * FROM `".pfx()."plans` WHERE slug=? LIMIT 1");$plan->execute([$apiUser['plan']]);$planData=$plan->fetch();
if(!($planData['api_access']??0)){http_response_code(403);echo json_encode(['error'=>'API access requires Pro or Agency plan']);exit;}

$uid=(int)$apiUser['id'];

switch($endpoint){
    case 'me':
        echo json_encode(['id'=>$uid,'name'=>$apiUser['name'],'email'=>$apiUser['email'],'plan'=>$apiUser['plan'],'api_key'=>$apiUser['api_key']]);break;
    case 'orders':
        $orders=db()->query("SELECT o.*,s.name AS service_name FROM `".pfx()."orders` o LEFT JOIN `".pfx()."services` s ON s.id=o.service_id WHERE o.client_id=$uid ORDER BY o.created_at DESC LIMIT 50")->fetchAll();
        echo json_encode(['orders'=>$orders,'count'=>count($orders)]);break;
    case 'keywords':
        $kw=db()->query("SELECT * FROM `".pfx()."rank_tracking` WHERE user_id=$uid ORDER BY created_at DESC")->fetchAll();
        echo json_encode(['keywords'=>$kw,'count'=>count($kw)]);break;
    case 'projects':
        $pr=db()->query("SELECT * FROM `".pfx()."projects` WHERE user_id=$uid ORDER BY name")->fetchAll();
        echo json_encode(['projects'=>$pr]);break;
    case 'audits':
        $au=db()->query("SELECT id,url,status,score,issues_critical,issues_warning,created_at FROM `".pfx()."audits` WHERE user_id=$uid ORDER BY created_at DESC LIMIT 20")->fetchAll();
        echo json_encode(['audits'=>$au]);break;
    default:
        echo json_encode(['error'=>'Unknown endpoint','available'=>['me','orders','keywords','projects','audits'],'docs'=>'Pass X-API-Key header with your API key']);
}
