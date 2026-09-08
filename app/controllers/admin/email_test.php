<?php
requireAdmin();$user=currentUser();$activePage='settings';$pageTitle='Email Test';
$result='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){
    $to=$_POST['to']??$user['email'];
    $body=mailTemplate('Test Email','<p class="p">This is a test email from '.appName().'. Your email system is working correctly!</p>','Visit Site',appUrl().'/');
    $ok=sendMail($to,'Test Email — '.appName(),$body);
    $result=$ok?'<div class="al al-s">✓ Email sent to '.e($to).'</div>':'<div class="al al-e">✗ Email failed. Check server mail config.</div>';
}
ob_start();?>
<div class="ph"><div><div class="pt">Email Test</div></div></div>
<?=$result?>
<div class="panel" style="max-width:480px"><div class="pnh"><div class="pnt">Send Test Email</div></div><div class="pnb">
<form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
<div class="fg"><label class="fl">SEND TO</label><input type="email" name="to" class="fc" value="<?=e($user['email'])?>" required></div>
<button type="submit" class="btn bp">Send Test Email →</button>
</form>
<div style="margin-top:1rem;font-size:.65rem;color:var(--mist);line-height:1.7">
This uses PHP's built-in mail() function. For reliable delivery:<br>
✓ Configure cPanel → Email → MX Records<br>
✓ Or use SMTP with a service like SendGrid/Mailgun<br>
✓ Add SPF + DKIM records to your domain DNS
</div>
</div></div>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/admin_wrap.php';
