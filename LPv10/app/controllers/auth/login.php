<?php
if(isLoggedIn())redirect(isAdmin()?'/admin':'/dashboard');
$error='';$email='';$ge=$_SESSION['login_error']??'';unset($_SESSION['login_error']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid request.';}else{
        $email=trim($_POST['email']??'');$pass=$_POST['password']??'';
        if(!$email||!$pass){$error='Email and password required.';}else{
            try{
                $s=db()->prepare("SELECT * FROM `".pfx()."users` WHERE email=? AND status=1 LIMIT 1");
                $s->execute([$email]);$u=$s->fetch();
                if($u&&password_verify($pass,$u['password'])){
                    session_regenerate_id(true);
                    $_SESSION['user_id']=$u['id'];$_SESSION['user_role']=$u['role'];$_SESSION['user_name']=$u['name'];$_SESSION['user_plan']=$u['plan'];
                    db()->prepare("UPDATE `".pfx()."users` SET last_login=NOW() WHERE id=?")->execute([$u['id']]);
                    logAct('LOGIN','Logged in');$to=$_SESSION['after']??null;unset($_SESSION['after']);
                    redirect($to?:($u['role']==='admin'||$u['role']==='staff'?'/admin':'/dashboard'));
                }else{$error='Invalid email or password.';}
            }catch(Exception $e){$error='DB error: check .env settings.';}
        }
    }
}
require LB_ROOT.'/app/views/auth/login.php';
