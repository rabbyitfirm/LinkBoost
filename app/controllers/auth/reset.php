<?php
if(isLoggedIn())redirect('/dashboard');$error='';$success='';$token=$_GET['token']??$_POST['token']??'';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $pass=$_POST['password']??'';$pass2=$_POST['password2']??'';
    if(!$token)$error='Invalid reset link.';
    elseif(strlen($pass)<8)$error='Password min 8 chars.';
    elseif($pass!==$pass2)$error='Passwords do not match.';
    else{
        $s=db()->prepare("SELECT * FROM `".pfx()."password_resets` WHERE token=? AND created_at>DATE_SUB(NOW(),INTERVAL 2 HOUR) LIMIT 1");$s->execute([$token]);$r=$s->fetch();
        if(!$r){$error='Invalid or expired link.';}else{
            db()->prepare("UPDATE `".pfx()."users` SET password=? WHERE email=?")->execute([password_hash($pass,PASSWORD_BCRYPT,['cost'=>12]),$r['email']]);
            db()->prepare("DELETE FROM `".pfx()."password_resets` WHERE email=?")->execute([$r['email']]);
            $success='Password reset! <a href="'.appUrl().'/login" style="color:var(--acid)">Login →</a>';
        }
    }
}require LB_ROOT.'/app/views/auth/reset.php';
