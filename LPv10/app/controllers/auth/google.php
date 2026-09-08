<?php
$cid=setting('google_client_id');$csec=setting('google_client_secret');$redir=appUrl().'/auth/google/callback';
$action=$GLOBALS['route_params'][0]??'';
if(strpos($action,'callback')!==false||($_GET['code']??'')){
    $code=$_GET['code']??'';$state=$_GET['state']??'';
    if(!$code||empty($_SESSION['gs'])||$state!==$_SESSION['gs']){$_SESSION['login_error']='Google login failed.';redirect('/login');}
    try{
        $tr=@file_get_contents('https://oauth2.googleapis.com/token',false,stream_context_create(['http'=>['method'=>'POST','header'=>'Content-Type: application/x-www-form-urlencoded','content'=>http_build_query(['code'=>$code,'client_id'=>$cid,'client_secret'=>$csec,'redirect_uri'=>$redir,'grant_type'=>'authorization_code'])]]));
        $td=json_decode($tr,true);$at=$td['access_token']??'';
        if(!$at)throw new Exception('No access token');
        $ui=json_decode(@file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo',false,stream_context_create(['http'=>['header'=>'Authorization: Bearer '.$at]])),true);
        $ge=$ui['email']??'';$gn=$ui['name']??'User';$gp=$ui['picture']??'';$gid=$ui['id']??'';
        if(!$ge)throw new Exception('No email from Google');
        $s=db()->prepare("SELECT * FROM `".pfx()."users` WHERE email=? LIMIT 1");$s->execute([$ge]);$u=$s->fetch();
        if($u){if(!$u['status']){$_SESSION['login_error']='Account suspended.';redirect('/login');}db()->prepare("UPDATE `".pfx()."users` SET google_id=?,avatar=?,updated_at=NOW() WHERE id=?")->execute([$gid,$gp,$u['id']]);}
        else{db()->prepare("INSERT INTO `".pfx()."users`(name,email,password,role,plan,status,email_verified,google_id,avatar,created_at)VALUES(?,?,'','client','free',1,1,?,?,NOW())")->execute([$gn,$ge,$gid,$gp]);$s2=db()->prepare("SELECT * FROM `".pfx()."users` WHERE email=? LIMIT 1");$s2->execute([$ge]);$u=$s2->fetch();}
        session_regenerate_id(true);$_SESSION['user_id']=$u['id'];$_SESSION['user_role']=$u['role'];$_SESSION['user_name']=$u['name'];$_SESSION['user_plan']=$u['plan'];
        db()->prepare("UPDATE `".pfx()."users` SET last_login=NOW() WHERE id=?")->execute([$u['id']]);
        logAct('GOOGLE_LOGIN','Google sign-in');$to=$_SESSION['after']??null;unset($_SESSION['after']);
        redirect($to?:($u['role']==='admin'?'/admin':'/dashboard'));
    }catch(Exception $e){$_SESSION['login_error']='Google error: '.$e->getMessage();redirect('/login');}
}else{
    if(!$cid||!$csec){$_SESSION['login_error']='Google login not configured.';redirect('/login');}
    $state=bin2hex(random_bytes(16));$_SESSION['gs']=$state;
    header('Location:https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query(['client_id'=>$cid,'redirect_uri'=>$redir,'response_type'=>'code','scope'=>'email profile','state'=>$state]),true,302);exit;
}
