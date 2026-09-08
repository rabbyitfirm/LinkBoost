<?php
$success='';$error='';$form=['name'=>'','email'=>'','subject'=>'','message'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$msg=trim($_POST['message']??'');
    if(!$name||!$email||!$msg){$error='All fields required.';}
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){$error='Invalid email.';}
    else{
        try{db()->prepare("INSERT INTO `".pfx()."contact_messages`(name,email,subject,message,ip_address,created_at)VALUES(?,?,?,?,?,NOW())")->execute([$name,$email,$_POST['subject']??'',$msg,$_SERVER['REMOTE_ADDR']??'']);$success='Message sent! We\'ll reply within 24 hours.';}
        catch(Exception $e){$error='Error sending message.';}
    }
    $form=['name'=>$_POST['name']??'','email'=>$_POST['email']??'','subject'=>$_POST['subject']??'','message'=>$_POST['message']??''];
}
require LB_ROOT.'/app/views/public/contact.php';
