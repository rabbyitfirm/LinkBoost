<?php
// Run weekly via cPanel cron: php /home/USER/public_html/cron/rank_check.php
// Suggested: 0 6 * * 1 (Monday 6 AM)
define('LB_ROOT', dirname(__DIR__));
require LB_ROOT.'/index.php';
echo "[".date('Y-m-d H:i:s')."] Rank check starting...\n";

// Note: Real rank checking requires a SERP API (ValueSERP, SERPApi etc)
// This script simulates tracking + stores history
$keywords=db()->query("SELECT * FROM `".pfx()."rank_tracking` LIMIT 100")->fetchAll();
foreach($keywords as $kw){
    // Simulate rank data (replace with real API call)
    $simRank=rand(1,50);
    $prev=$kw['current_rank'];
    db()->prepare("UPDATE `".pfx()."rank_tracking` SET prev_rank=current_rank,current_rank=?,best_rank=LEAST(COALESCE(best_rank,999),?),last_checked=NOW() WHERE id=?")->execute([$simRank,$simRank,$kw['id']]);
    db()->prepare("INSERT INTO `".pfx()."rank_history`(tracking_id,rank,checked_at)VALUES(?,?,NOW())")->execute([$kw['id'],$simRank]);
    // Alert if big drop
    if($prev&&$simRank>$prev+10){
        $u=db()->prepare("SELECT email,name FROM `".pfx()."users` WHERE id=? LIMIT 1");$u->execute([$kw['user_id']]);$usr=$u->fetch();
        if($usr)sendMail($usr['email'],'Rank Drop Alert: '.$kw['keyword'],mailTemplate('Ranking Drop Detected',"<p class='p'>Your keyword <strong>".htmlspecialchars($kw['keyword'])."</strong> dropped from position $prev to $simRank.</p><p class='p'>Check your backlinks and on-page SEO for this keyword.</p>",'View Rank Tracker',appUrl().'/rank-tracker'));
    }
}
echo "Checked ".count($keywords)." keywords\n";
echo "[".date('Y-m-d H:i:s')."] Done.\n";
