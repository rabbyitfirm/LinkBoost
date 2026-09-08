<?php
define('LB_ROOT', __DIR__);
define('LB_START', microtime(true));
error_reporting(E_ALL); ini_set('display_errors', 0);

// ── Check for installer ────────────────────────────────────────────────────
if (!file_exists(LB_ROOT.'/storage/installed.lock')) {
    if (!str_starts_with($_SERVER['REQUEST_URI'] ?? '/', '/install')) {
        header('Location: /install/'); exit;
    }
}

// ── Load .env ──────────────────────────────────────────────────────────────
if (file_exists(LB_ROOT.'/.env')) {
    foreach (file(LB_ROOT.'/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
        $line=trim($line); if(!$line||$line[0]==='#') continue;
        if(strpos($line,'=')!==false){[$k,$v]=array_map('trim',explode('=',$line,2));$_ENV[$k]=trim($v,'"\'');putenv("$k=".trim($v,'"\''));}
    }
}

function env($k,$d=null){$v=$_ENV[$k]??getenv($k);return($v!==false&&$v!=='')? $v:$d;}

// ── Session ────────────────────────────────────────────────────────────────
ini_set('session.cookie_httponly',1);ini_set('session.gc_maxlifetime',86400);
if(session_status()===PHP_SESSION_NONE)session_start();

// ── Database ───────────────────────────────────────────────────────────────
function db(){
    static $p=null;
    if($p===null){
        $dsn='mysql:host='.env('DB_HOST','localhost').';port='.env('DB_PORT','3306').';dbname='.env('DB_DATABASE').';charset=utf8mb4';
        $p=new PDO($dsn,env('DB_USERNAME'),env('DB_PASSWORD'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $p;
}

// ── Helpers ────────────────────────────────────────────────────────────────
function pfx(){return env('DB_PREFIX','lb_');}
function appUrl(){return rtrim(env('APP_URL',''),'/');}
function appName(){return setting('site_name',env('APP_NAME','LinkParty'));}
function currency(){return env('CURRENCY','USD');}
function currSym(){return['USD'=>'$','EUR'=>'€','GBP'=>'£','INR'=>'₹','BDT'=>'৳','PKR'=>'₨','TRY'=>'₺','AUD'=>'A$','CAD'=>'C$'][currency()]??'$';}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES|ENT_HTML5,'UTF-8');}
function redirect($p){header('Location:'.appUrl().$p,true,302);exit;}
function isLoggedIn(){return!empty($_SESSION['user_id']);}
function isAdmin(){return in_array($_SESSION['user_role']??'',['admin','staff']);}
function userPlan(){return $_SESSION['user_plan']??'free';}
function csrf(){if(empty($_SESSION['_token']))$_SESSION['_token']=bin2hex(random_bytes(32));return $_SESSION['_token'];}
function verifyCsrf(){$t=$_POST['_token']??'';return!empty($_SESSION['_token'])&&hash_equals($_SESSION['_token'],$t);}
function requireLogin(){if(!isLoggedIn()){$_SESSION['after']=$_SERVER['REQUEST_URI'];redirect('/login');}}
function requireAdmin(){requireLogin();if(!isAdmin())redirect('/dashboard');}
function moneyFmt($n){return currSym().number_format((float)$n,2);}
function formatDate($d,$f='M j, Y'){return $d?date($f,strtotime($d)):'—';}
function excerpt($s,$l=150){$s=strip_tags($s);return strlen($s)>$l?substr($s,0,$l).'...':$s;}
function slug($s){return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));}
function genApiKey(){return 'lp_'.bin2hex(random_bytes(24));}

function planLimit($key){
    static $lim=null;
    if($lim===null){$p=userPlan();try{$s=db()->prepare("SELECT * FROM `".pfx()."plans` WHERE slug=? LIMIT 1");$s->execute([$p]);$lim=$s->fetch()?:[];}catch(Exception $e){$lim=[];}}
    return $lim[$key]??0;
}
function currentUser(){
    if(!isLoggedIn())return null;
    static $u=null;
    if($u===null){try{$s=db()->prepare("SELECT * FROM `".pfx()."users` WHERE id=? AND status=1 LIMIT 1");$s->execute([$_SESSION['user_id']]);$u=$s->fetch()?:null;}catch(Exception $e){$u=null;}}
    return $u;
}
function setting($k,$d=''){
    static $c=[];
    if(!isset($c[$k])){try{$s=db()->prepare("SELECT value FROM `".pfx()."settings` WHERE `key`=? LIMIT 1");$s->execute([$k]);$r=$s->fetch();$c[$k]=$r?$r['value']:$d;}catch(Exception $e){$c[$k]=$d;}}
    return $c[$k];
}
function badge($s){
    $m=['pending'=>'warn','in_progress'=>'info','review'=>'purple','completed'=>'success','cancelled'=>'danger','active'=>'success','inactive'=>'muted','paid'=>'success','failed'=>'danger','refunded'=>'warn','draft'=>'muted','published'=>'success','free'=>'muted','starter'=>'info','pro'=>'success','agency'=>'purple','admin'=>'success','staff'=>'info','client'=>'muted','trial'=>'warn','expired'=>'danger','running'=>'info'];
    return '<span class="bdg b-'.($m[$s]??'muted').'">'.e(strtoupper(str_replace('_',' ',$s))).'</span>';
}
function planBadge($p){
    $i=['free'=>'🆓','starter'=>'⚡','pro'=>'🚀','agency'=>'🏢'];
    $c=['free'=>'b-muted','starter'=>'b-info','pro'=>'b-success','agency'=>'b-purple'];
    return '<span class="bdg '.($c[$p]??'b-muted').'">'.($i[$p]??'').' '.strtoupper($p).'</span>';
}
function notify($uid,$type,$title,$msg='',$link=''){
    try{db()->prepare("INSERT INTO `".pfx()."notifications`(user_id,type,title,message,link,is_read,created_at)VALUES(?,?,?,?,?,0,NOW())")->execute([$uid,$type,$title,$msg,$link]);}
    catch(Exception $e){}
}
function logAct($a,$d=''){
    try{$u=currentUser();db()->prepare("INSERT INTO `".pfx()."activity_log`(user_id,action,description,ip_address,created_at)VALUES(?,?,?,?,NOW())")->execute([$u['id']??null,$a,$d,$_SERVER['REMOTE_ADDR']??null]);}
    catch(Exception $e){}
}
function sendMail($to,$subject,$body){
    $from=env('MAIL_FROM','noreply@'.($_SERVER['HTTP_HOST']??'localhost'));
    $name=env('MAIL_FROM_NAME',appName());
    $headers="From: $name <$from>\r\nReply-To: $from\r\nContent-Type: text/html; charset=UTF-8\r\nMIME-Version: 1.0\r\nX-Mailer: PHP/".phpversion();
    return @mail($to,$subject,$body,$headers);
}
function mailTemplate($title,$content,$btn='',$btnUrl=''){
    $ac=setting('accent_color','#b8ff3c');$bg='#080810';
    return "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>body{background:#f4f4f4;font-family:Arial,sans-serif;margin:0;padding:20px}.wrap{max-width:580px;margin:0 auto;background:#fff;border-radius:6px;overflow:hidden}.header{background:$bg;padding:24px 32px}.logo{color:$ac;font-size:20px;font-weight:800}.body{padding:32px}.h1{font-size:22px;font-weight:700;color:#111;margin-bottom:12px}.p{font-size:15px;color:#555;line-height:1.7;margin-bottom:16px}.btn{display:inline-block;background:$ac;color:$bg;padding:12px 28px;font-weight:700;text-decoration:none;border-radius:4px;margin:16px 0;font-size:15px}.footer{background:#f9f9f9;padding:16px 32px;font-size:12px;color:#999;text-align:center}</style></head><body><div class='wrap'><div class='header'><div class='logo'>".appName()."</div></div><div class='body'><div class='h1'>$title</div>$content".($btn&&$btnUrl?"<a href='$btnUrl' class='btn'>$btn</a>":'')."</div><div class='footer'>".appName()." &mdash; ".date('Y')."</div></div></body></html>";
}

// ── Router ─────────────────────────────────────────────────────────────────
$uri=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);
$url=trim($uri,'/');

// Special: sitemap.xml + robots.txt
if($url==='sitemap.xml'){require LB_ROOT.'/app/controllers/public/sitemap.php';exit;}
if($url==='robots.txt'){require LB_ROOT.'/app/controllers/public/robots.php';exit;}

$routes=[
    ''                      => 'public/home',
    'services'              => 'public/services',
    'pricing'               => 'public/pricing',
    'blog'                  => 'public/blog',
    'blog/([^/]+)'          => 'public/blog_post',
    'contact'               => 'public/contact',
    'tools'                 => 'public/tools',
    'ask-ai'                => 'public/ask_ai',
    'login'                 => 'auth/login',
    'logout'                => 'auth/logout',
    'register'              => 'auth/register',
    'forgot-password'       => 'auth/forgot',
    'reset-password'        => 'auth/reset',
    'auth/google'           => 'auth/google',
    'auth/google/callback'  => 'auth/google',
    'verify-email'          => 'auth/verify_email',
    'dashboard'             => 'client/dashboard',
    'orders'                => 'client/orders',
    'orders/new'            => 'client/order_new',
    'invoices'              => 'client/invoices',
    'projects'              => 'client/projects',
    'rank-tracker'          => 'client/rank_tracker',
    'site-audit'            => 'client/site_audit',
    'seo-tools'             => 'client/seo_tools',
    'billing'               => 'client/billing',
    'profile'               => 'client/profile',
    'checkout'              => 'client/checkout',
    'checkout/success'      => 'client/checkout_success',
    'checkout/cancel'       => 'client/checkout_cancel',
    'autopilot'             => 'client/autopilot',
    'keyword-planner'       => 'client/keyword_planner',
    'content-writer'        => 'client/content_writer',
    'competitor'            => 'client/competitor',
    'on-page'               => 'client/on_page',
    'link-prospector'       => 'client/link_prospector',
    'seo-report'            => 'client/seo_report',
    'admin'                 => 'admin/dashboard',
    'admin/orders'          => 'admin/orders',
    'admin/services'        => 'admin/services',
    'admin/clients'         => 'admin/clients',
    'admin/payments'        => 'admin/payments',
    'admin/invoices'        => 'admin/invoices',
    'admin/plans'           => 'admin/plans',
    'admin/blog'            => 'admin/blog',
    'admin/pages'           => 'admin/pages',
    'admin/customizer'      => 'admin/customizer',
    'admin/settings'        => 'admin/settings',
    'admin/staff'           => 'admin/staff',
    'admin/logs'            => 'admin/logs',
    'admin/messages'        => 'admin/messages',
    'admin/tools'           => 'admin/tools',
    'admin/autopilot'       => 'admin/autopilot',
    'admin/email-test'      => 'admin/email_test',
    // TICKETS
    'tickets'                => 'client/tickets',
    'tickets/([0-9]+)'       => 'client/ticket_view',
    'admin/tickets'          => 'admin/tickets',
    'admin/tickets/([0-9]+)' => 'admin/ticket_view',
    // COUPONS
    'admin/coupons'          => 'admin/coupons',
    // REPORTS
    'admin/reports'          => 'admin/reports',
    // PUBLISHERS
    'publishers'             => 'public/publishers',
    'admin/publishers'       => 'admin/publishers',
    // REFERRALS
    'referrals'              => 'client/referrals',
    // REVIEWS
    'review/([0-9]+)'        => 'client/review',
    // NEWSLETTER
    'newsletter/subscribe'   => 'api/newsletter',
    'newsletter/unsubscribe' => 'api/newsletter',
    // LEGAL PAGES
    'faq'                    => 'public/faq',
    'terms'                  => 'public/terms',
    'privacy'                => 'public/privacy',
    'admin/testimonials'     => 'admin/testimonials',
    'admin/newsletter'       => 'admin/newsletter',
    // NOTIFICATIONS API
    'api/notifications'      => 'api/notifications',
    'api/audit'              => 'api/audit',
    'api/v1/([^/]+)'        => 'api/v1',
    'webhook/stripe'        => 'api/stripe_webhook',
];

$matched=null;$params=[];
foreach($routes as $pat=>$ctrl){
    if(preg_match('#^'.$pat.'$#',$url,$m)){$matched=$ctrl;$params=array_slice($m,1);break;}
}
$GLOBALS['route_params']=$params;

if($matched){
    $f=LB_ROOT.'/app/controllers/'.$matched.'.php';
    if(file_exists($f))require $f;
    else{http_response_code(500);die('<pre style="padding:2rem;background:#0a0a0f;color:#ff4d4d">MISSING CONTROLLER: '.e($matched.'.php').'</pre>');}
}else{
    http_response_code(404);
    $ef=LB_ROOT.'/app/views/errors/404.php';
    file_exists($ef)?require $ef:die('<h1 style="font-family:monospace;padding:2rem">404 Not Found</h1>');
}
