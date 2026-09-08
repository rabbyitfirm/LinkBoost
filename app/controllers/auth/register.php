<?php
if(isLoggedIn())redirect('/dashboard');$error='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$pass=$_POST['password']??'';$pass2=$_POST['password2']??'';
    if(!$name||!$email||!$pass)$error='All fields required.';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Invalid email.';
    elseif(strlen($pass)<8)$error='Password min 8 characters.';
    elseif($pass!==$pass2)$error='Passwords do not match.';
    else{
        try{
            $c=db()->prepare("SELECT id FROM `".pfx()."users` WHERE email=? LIMIT 1");$c->execute([$email]);
            if($c->fetch()){$error='Email already registered.';}else{
                $hash=password_hash($pass,PASSWORD_BCRYPT,['cost'=>12]);
                db()->prepare("INSERT INTO `".pfx()."users`(name,email,password,role,plan,status,email_verified,created_at)VALUES(?,?,?,'client','free',1,0,NOW())")->execute([$name,$email,$hash]);
                $id=db()->lastInsertId();session_regenerate_id(true);
                $_SESSION['user_id']=$id;$_SESSION['user_role']='client';$_SESSION['user_name']=$name;$_SESSION['user_plan']='free';
                logAct('REGISTER','New user');redirect('/dashboard');
            }
        }catch(Exception $e){$error='Error: '.$e->getMessage();}
    }
}
require LB_ROOT.'/app/views/auth/register.php';
