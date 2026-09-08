<?php
define('LB_ROOT', dirname(__DIR__));
session_start();

// Already installed?
if (file_exists(LB_ROOT.'/storage/installed.lock') && !isset($_GET['force'])) {
    die('<div style="font-family:monospace;padding:2rem;background:#080810;color:#b8ff3c;min-height:100vh">✓ Already installed. <a href="/" style="color:#fff">Go to site →</a></div>');
}

$step = (int)($_GET['step'] ?? 1);
$error = '';
$success = '';

// ── STEP 3: Run installation ───────────────────────────────────────────────
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $host     = trim($_POST['db_host'] ?? 'localhost');
    $port     = trim($_POST['db_port'] ?? '3306');
    $dbname   = trim($_POST['db_name'] ?? '');
    $user     = trim($_POST['db_user'] ?? '');
    $pass     = $_POST['db_pass'] ?? '';
    $prefix   = trim($_POST['db_prefix'] ?? 'lb_');
    $appUrl   = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $appName  = trim($_POST['app_name'] ?? 'LinkParty');
    $currency = $_POST['currency'] ?? 'USD';
    $aName    = trim($_POST['admin_name'] ?? 'Admin');
    $aEmail   = trim($_POST['admin_email'] ?? '');
    $aPass    = $_POST['admin_pass'] ?? '';

    if (!$dbname || !$user || !$aEmail || !$aPass) {
        $error = 'All fields are required.';
        $step = 2;
    } elseif (strlen($aPass) < 8) {
        $error = 'Admin password must be at least 8 characters.';
        $step = 2;
    } else {
        try {
            // Test DB connection
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Run schema
            $sql = file_get_contents(LB_ROOT . '/schema.sql');
            // Replace lb_ prefix with chosen prefix
            $sql = str_replace('`lb_', '`'.$prefix, $sql);
            $sql = str_replace("pfx().'", "'".$prefix, $sql);
            foreach (explode(';', $sql) as $q) {
                $q = trim($q);
                if ($q) try { $pdo->exec($q); } catch (Exception $e) { /* ignore CREATE IF EXISTS errors */ }
            }

            // Create admin user
            $hash = password_hash($aPass, PASSWORD_BCRYPT, ['cost'=>12]);
            $pdo->prepare("INSERT INTO `{$prefix}users`(name,email,password,role,plan,status,email_verified,created_at)VALUES(?,?,?,'admin','agency',1,1,NOW()) ON DUPLICATE KEY UPDATE password=?,role='admin',plan='agency'")->execute([$aName,$aEmail,$hash,$hash]);

            // Insert settings
            $settings = [
                ['site_name',$appName,'general'],['site_tagline','Premium SEO & Backlink Platform','general'],
                ['accent_color','#b8ff3c','theme'],['bg_color','#080810','theme'],
                ['hero_title','The Complete SEO & Link Building Platform','theme'],
                ['hero_sub','Keyword research, site audits, rank tracking, and premium backlinks — all in one place.','theme'],
                ['hero_cta','Start Free Today →','theme'],['hero_cta2','View Pricing','theme'],
                ['stat1val','22+','theme'],['stat1lbl','SEO TOOLS','theme'],
                ['stat2val','12K+','theme'],['stat2lbl','PUBLISHER SITES','theme'],
                ['stat3val','98%','theme'],['stat3lbl','DELIVERY RATE','theme'],
                ['stat4val','2,400+','theme'],['stat4lbl','HAPPY CLIENTS','theme'],
                ['contact_email',$aEmail,'general'],['order_prefix','ORD-','general'],
                ['footer_text','All rights reserved.','general'],
                ['stripe_pub_key','','payment'],['stripe_secret_key','','payment'],
                ['google_client_id','','oauth'],['google_client_secret','','oauth'],
            ];
            foreach ($settings as [$k,$v,$g]) {
                $pdo->prepare("INSERT IGNORE INTO `{$prefix}settings`(`key`,`value`,`group`)VALUES(?,?,?)")->execute([$k,$v,$g]);
            }

            // Insert default plans
            $plansSql = [
                ["Free","free",0,0,1,10,1,3,1,1,0,0,'["Keyword Research","Basic SEO Tools","3 Link Orders","1 Project","AI Assistant"]',0,1,1],
                ["Starter","starter",29,249,3,100,5,20,1,1,0,0,'["All Free Features","3 Projects","100 Keywords Tracked","5 Site Audits","20 Link Orders","Priority Support"]',0,1,2],
                ["Pro","pro",79,699,10,500,20,100,1,1,1,0,'["All Starter Features","10 Projects","500 Keywords","20 Audits/mo","100 Orders","API Access","CSV Export"]',1,1,3],
                ["Agency","agency",199,1799,99,9999,999,999,1,1,1,1,'["Unlimited Everything","White-label Reports","API Access","Dedicated Support","Custom Branding"]',0,1,4],
            ];
            foreach ($plansSql as $pl) {
                $pdo->prepare("INSERT IGNORE INTO `{$prefix}plans`(name,slug,price_monthly,price_yearly,max_projects,max_keywords,max_audits,max_orders,ai_tools,seo_tools,api_access,white_label,features,is_popular,status,sort_order)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($pl);
            }
            // Insert default services
            $svcSql = [
                ["DA30+ Guest Post","guest_post","General","Editorial guest post on a real DA30+ website.",30,25,7,49.00,1,1,1],
                ["DA50+ Guest Post","guest_post","General","Premium guest post on DA50+ authority website.",50,45,10,99.00,1,1,2],
                ["Niche Edit — DR40+","niche_edit","General","Link insertion into existing content on aged DR40+ domain.",40,35,5,69.00,1,1,3],
                ["Tech/SaaS Guest Post","guest_post","Technology","Editorial placement on tech niche DA35+ site.",35,30,10,89.00,1,1,4],
                ["Finance Guest Post","guest_post","Finance","DA40+ guest post in finance/fintech niche.",40,35,10,129.00,0,1,5],
                ["Homepage Link DR50+","homepage_link","General","Permanent homepage backlink on DR50+ domain.",50,50,14,199.00,0,1,6],
            ];
            foreach ($svcSql as $sv) {
                $pdo->prepare("INSERT IGNORE INTO `{$prefix}services`(name,type,niche,description,min_da,min_dr,turnaround_days,price,is_featured,status,sort_order,created_at)VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW())")->execute($sv);
            }
            // Write .env
            $env = "APP_NAME=$appName\nAPP_URL=$appUrl\nAPP_DEBUG=false\n\n";
            $env .= "DB_HOST=$host\nDB_PORT=$port\nDB_DATABASE=$dbname\nDB_USERNAME=$user\nDB_PASSWORD=$pass\nDB_PREFIX=$prefix\n\n";
            $env .= "CURRENCY=$currency\nMAIL_FROM=noreply@".parse_url($appUrl,PHP_URL_HOST)."\nMAIL_FROM_NAME=$appName\n";
            file_put_contents(LB_ROOT.'/.env', $env);

            // Write lock file
            file_put_contents(LB_ROOT.'/storage/installed.lock', date('Y-m-d H:i:s')."\nURL: $appUrl\nAdmin: $aEmail");

            // Redirect to step 4 (success)
            header('Location: /install/?step=4'); exit;

        } catch (Exception $e) {
            $error = 'Installation failed: ' . $e->getMessage();
            $step = 2;
        }
    }
}

// ── STEP 1: Test DB connection ─────────────────────────────────────────────
$dbOk = false; $phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');
$extCheck = ['pdo'=>extension_loaded('pdo'),'pdo_mysql'=>extension_loaded('pdo_mysql'),'curl'=>extension_loaded('curl'),'json'=>extension_loaded('json'),'mbstring'=>extension_loaded('mbstring')];
$allExt = !in_array(false, $extCheck);

// Guess app URL
$guessUrl = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost');

$ac='#b8ff3c';$bg='#080810';
?><!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>LinkParty — Setup Wizard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:<?=$bg?>;color:#f0ede6;font-family:'DM Mono',monospace;min-height:100vh;padding:2rem 1rem}
.wrap{max-width:680px;margin:0 auto}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;margin-bottom:2rem;display:flex;align-items:center;gap:.5rem}
.badge{background:<?=$ac?>;color:<?=$bg?>;padding:.1em .4em;font-size:.5rem;font-weight:700;letter-spacing:.05em;border-radius:3px}
.steps{display:flex;gap:0;margin-bottom:2rem;background:rgba(255,255,255,.04);border-radius:4px;overflow:hidden;border:1px solid rgba(255,255,255,.07)}
.st{flex:1;padding:.65rem;text-align:center;font-size:.6rem;font-family:'Syne',sans-serif;font-weight:700;border-right:1px solid rgba(255,255,255,.07);color:#8888aa}
.st.done{background:rgba(85,239,196,.08);color:#55efc4}
.st.active{background:rgba(184,255,60,.08);color:<?=$ac?>}
.st:last-child{border-right:none}
.card{background:#0f0f1e;border:1px solid rgba(255,255,255,.07);border-radius:4px;padding:2rem;margin-bottom:1rem}
.card-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;margin-bottom:.4rem}
.card-sub{font-size:.68rem;color:#8888aa;margin-bottom:1.5rem;line-height:1.7}
.row{display:grid;grid-template-columns:1fr 1fr;gap:.7rem}
.fg{display:flex;flex-direction:column;gap:.25rem;margin-bottom:.7rem}
.fl{font-size:.52rem;letter-spacing:.09em;color:#8888aa}
.fc{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:#f0ede6;padding:.65rem .85rem;font-family:'DM Mono',monospace;font-size:.76rem;width:100%;outline:none;transition:border-color .2s;border-radius:3px}
.fc:focus{border-color:rgba(184,255,60,.4)}
.fc option{background:#0f0f1e}
.btn{padding:.72rem 1.4rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.76rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;transition:all .18s;border-radius:3px}
.bp{background:<?=$ac?>;color:<?=$bg?>}.bp:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(184,255,60,.3)}
.bs{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);color:#8888aa}
.al{padding:.7rem .88rem;border:1px solid;font-size:.68rem;margin-bottom:1rem;line-height:1.6;border-radius:3px}
.al-e{border-color:rgba(255,77,109,.3);background:rgba(255,77,109,.06);color:#ff4d6d}
.al-s{border-color:rgba(85,239,196,.3);background:rgba(85,239,196,.06);color:#55efc4}
.check-row{display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:.7rem}
.check-row:last-child{border-bottom:none}
.ok{color:#55efc4;font-weight:700}.fail{color:#ff4d6d;font-weight:700}
.big-check{text-align:center;padding:2rem 0}
.big-ico{font-size:4rem;margin-bottom:1rem}
.big-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:<?=$ac?>;margin-bottom:.5rem}
</style>
</head><body><div class="wrap">
<div class="logo">LinkParty <span class="badge">SETUP WIZARD</span></div>

<div class="steps">
  <div class="st <?=$step>=1?($step>1?'done':'active'):''?>">1. Requirements</div>
  <div class="st <?=$step>=2?($step>2?'done':'active'):''?>">2. Configuration</div>
  <div class="st <?=$step>=3?($step>3?'done':'active'):''?>">3. Installing</div>
  <div class="st <?=$step>=4?'active':''?>">4. Complete</div>
</div>

<?php if($error):?><div class="al al-e">✗ <?=htmlspecialchars($error)?></div><?php endif;?>

<?php if($step===1): ?>
<!-- STEP 1: Requirements -->
<div class="card">
  <div class="card-title">System Requirements</div>
  <div class="card-sub">Checking your server environment before installation</div>

  <div class="check-row"><span>PHP Version (<?=PHP_VERSION?>)</span><span class="<?=$phpOk?'ok':'fail'?>"><?=$phpOk?'✓ OK':'✗ Need 7.4+'?></span></div>
  <?php foreach($extCheck as $ext=>$ok):?>
  <div class="check-row"><span>PHP Extension: <?=$ext?></span><span class="<?=$ok?'ok':'fail'?>"><?=$ok?'✓ OK':'✗ Missing'?></span></div>
  <?php endforeach;?>
  <div class="check-row"><span>storage/ writable</span><span class="<?=is_writable(LB_ROOT.'/storage')?'ok':'fail'?>"><?=is_writable(LB_ROOT.'/storage')?'✓ OK':'✗ Not writable — chmod 755 storage/'?></span></div>
  <div class="check-row"><span>Root dir writable (.env)</span><span class="<?=is_writable(LB_ROOT)?'ok':'fail'?>"><?=is_writable(LB_ROOT)?'✓ OK':'✗ Make public_html writable'?></span></div>

  <?php $canProceed=$phpOk&&$allExt&&is_writable(LB_ROOT.'/storage')&&is_writable(LB_ROOT);?>
  <div style="margin-top:1.5rem;display:flex;gap:.5rem">
    <?php if($canProceed):?>
    <a href="?step=2" class="btn bp">Continue → Configure</a>
    <?php else:?>
    <div style="font-size:.7rem;color:#ff4d6d">Fix the issues above, then <a href="?step=1" style="color:#b8ff3c">refresh this page</a>.</div>
    <?php endif;?>
  </div>
</div>

<?php elseif($step===2||$step===3): ?>
<!-- STEP 2: Configuration -->
<div class="card">
  <div class="card-title">Database & Site Configuration</div>
  <div class="card-sub">Enter your database credentials from cPanel</div>
  <form method="post" action="?step=3">
    <div style="font-size:.58rem;letter-spacing:.1em;color:<?=$ac?>;margin-bottom:.6rem">DATABASE</div>
    <div class="row">
      <div class="fg"><label class="fl">DB HOST</label><input name="db_host" class="fc" value="localhost" required></div>
      <div class="fg"><label class="fl">DB PORT</label><input name="db_port" class="fc" value="3306"></div>
    </div>
    <div class="fg"><label class="fl">DATABASE NAME</label><input name="db_name" class="fc" placeholder="linkhpiy_linkboost" required></div>
    <div class="row">
      <div class="fg"><label class="fl">DB USERNAME</label><input name="db_user" class="fc" placeholder="linkhpiy_linkboost" required></div>
      <div class="fg"><label class="fl">DB PASSWORD</label><input type="password" name="db_pass" class="fc" required></div>
    </div>
    <div class="fg"><label class="fl">TABLE PREFIX</label><input name="db_prefix" class="fc" value="lb_"></div>

    <div style="font-size:.58rem;letter-spacing:.1em;color:<?=$ac?>;margin:.9rem 0 .6rem">SITE SETTINGS</div>
    <div class="fg"><label class="fl">SITE NAME</label><input name="app_name" class="fc" value="LinkParty"></div>
    <div class="fg"><label class="fl">SITE URL (no trailing slash)</label><input name="app_url" class="fc" value="<?=htmlspecialchars($guessUrl)?>" required></div>
    <div class="fg"><label class="fl">CURRENCY</label>
      <select name="currency" class="fc"><option value="USD">USD — $</option><option value="EUR">EUR — €</option><option value="GBP">GBP — £</option><option value="BDT">BDT — ৳</option><option value="PKR">PKR — ₨</option><option value="INR">INR — ₹</option><option value="TRY">TRY — ₺</option><option value="AUD">AUD — A$</option></select>
    </div>

    <div style="font-size:.58rem;letter-spacing:.1em;color:<?=$ac?>;margin:.9rem 0 .6rem">ADMIN ACCOUNT</div>
    <div class="fg"><label class="fl">ADMIN NAME</label><input name="admin_name" class="fc" value="Admin" required></div>
    <div class="fg"><label class="fl">ADMIN EMAIL</label><input type="email" name="admin_email" class="fc" placeholder="admin@yoursite.com" required></div>
    <div class="fg"><label class="fl">ADMIN PASSWORD (min 8 chars)</label><input type="password" name="admin_pass" class="fc" required></div>

    <div style="margin-top:1.2rem;display:flex;gap:.5rem">
      <button type="submit" class="btn bp">🚀 Install Now →</button>
      <a href="?step=1" class="btn bs">← Back</a>
    </div>
  </form>
</div>

<?php elseif($step===4): ?>
<!-- STEP 4: Success -->
<div class="card">
  <div class="big-check">
    <div class="big-ico">🎉</div>
    <div class="big-title">Installation Complete!</div>
    <div style="font-size:.72rem;color:#8888aa;line-height:1.8;margin-bottom:1.5rem">
      LinkParty has been installed successfully.<br>
      Your site is ready to go live.
    </div>
    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);padding:1rem;border-radius:4px;text-align:left;font-size:.68rem;margin-bottom:1.5rem">
      <div style="margin-bottom:.5rem;color:#8888aa">Next Steps:</div>
      <div style="display:flex;flex-direction:column;gap:.35rem">
        <div>✓ <strong style="color:#b8ff3c">Delete the /install folder</strong> from your server (security)</div>
        <div>✓ Go to <strong>Admin → Settings</strong> to configure Google OAuth & Stripe</div>
        <div>✓ Go to <strong>Admin → Customizer</strong> to set your colors & content</div>
        <div>✓ Add your services in <strong>Admin → Services</strong></div>
      </div>
    </div>
    <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
      <a href="/" class="btn bp">🏠 Visit Site →</a>
      <a href="/admin" class="btn bs">⬛ Admin Panel</a>
    </div>
  </div>
</div>
<?php endif;?>

</div></body></html>
