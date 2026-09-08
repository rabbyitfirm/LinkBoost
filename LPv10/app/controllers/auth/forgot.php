<?php
if(isLoggedIn())redirect('/dashboard');$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $email=trim($_POST['email']??'');
    if(!$email||!filter_var($email,FILTER_VALIDATE_EMAIL)){$error='Valid email required.';}else{
        $s=db()->prepare("SELECT id FROM `".pfx()."users` WHERE email=? AND status=1 LIMIT 1");$s->execute([$email]);
        if($s->fetch()){$token=bin2hex(random_bytes(32));db()->prepare("DELETE FROM `".pfx()."password_resets` WHERE email=?")->execute([$email]);db()->prepare("INSERT INTO `".pfx()."password_resets`(email,token,created_at)VALUES(?,?,NOW())")->execute([$email,$token]);}
        $success='If that email exists, a reset link has been sent.';
    }
}require LB_ROOT.'/app/views/auth/forgot.php';
