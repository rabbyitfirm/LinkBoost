<?php
// Run daily via cPanel cron: php /home/USER/public_html/cron/daily.php
// Suggested time: 0 3 * * * (3 AM daily)
define('LB_ROOT', dirname(__DIR__));
require LB_ROOT.'/index.php'; // loads helpers

echo "[".date('Y-m-d H:i:s')."] Daily cron starting...\n";

// 1. Expire old plan subscriptions
$expired=db()->prepare("UPDATE `".pfx()."users` SET plan='free' WHERE plan!='free' AND plan_expires_at IS NOT NULL AND plan_expires_at < NOW()");
$expired->execute();echo "Plans expired: ".$expired->rowCount()."\n";

// 2. Notify admin of pending orders older than 3 days
$old=db()->query("SELECT COUNT(*) FROM `".pfx()."orders` WHERE status='pending' AND created_at < DATE_SUB(NOW(),INTERVAL 3 DAY)")->fetchColumn();
if($old>0){$email=setting('contact_email');sendMail($email,'Action Needed: '.$old.' Pending Orders',mailTemplate('Pending Orders Alert',"<p class='p'>You have <strong>$old orders</strong> that have been pending for over 3 days. Please process them.</p>",'View Orders',appUrl().'/admin/orders'));}
echo "Pending alert: $old old orders\n";

// 3. Clean old activity logs (keep 90 days)
db()->exec("DELETE FROM `".pfx()."activity_log` WHERE created_at < DATE_SUB(NOW(),INTERVAL 90 DAY)");
echo "Old logs cleaned\n";

// 4. Clean expired password reset tokens
db()->exec("DELETE FROM `".pfx()."password_resets` WHERE created_at < DATE_SUB(NOW(),INTERVAL 2 HOUR)");
echo "Old tokens cleaned\n";

echo "[".date('Y-m-d H:i:s')."] Done.\n";
