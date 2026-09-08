<?php
$ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810');
$ht=setting('hero_title','The Complete SEO & Link Building Platform');
$hs=setting('hero_sub','Keyword research, site audits, rank tracking, and premium backlinks — all in one place.');
$hc=setting('hero_cta','Start Free Today →');$hc2=setting('hero_cta2','View Pricing');
ob_start();?>
<style>
.hero{padding:6rem 0 4rem;text-align:center;background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(184,255,60,.09),transparent 70%)}
h1{font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.8rem,5vw,3.5rem);line-height:1.08;letter-spacing:-.03em;margin-bottom:1.1rem;max-width:860px;margin-inline:auto}
h1 em{color:var(--acid);font-style:normal}
.hs{font-size:clamp(.78rem,2vw,.95rem);color:var(--mist);margin-bottom:2rem;line-height:1.8;max-width:560px;margin-inline:auto}
.hcta{display:flex;gap:.7rem;justify-content:center;flex-wrap:wrap}
.stats{display:grid;grid-template-columns:repeat(4,1fr);padding:2.5rem 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin:3rem 0}
.sv2{font-family:'Syne',sans-serif;font-weight:800;font-size:1.85rem;color:var(--acid);text-align:center;line-height:1}
.sl2{font-size:.52rem;letter-spacing:.12em;color:var(--mist);text-align:center;margin-top:.25rem}
.section{padding:5rem 0}
.sh{font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.3rem,3vw,2rem);margin-bottom:.4rem;text-align:center}
.ss{font-size:.75rem;color:var(--mist);text-align:center;margin-bottom:3rem;line-height:1.7}
.feats{display:grid;grid-template-columns:repeat(3,1fr);gap:.85rem}
.feat{background:var(--card);border:1px solid var(--border);padding:1.4rem;border-radius:4px;transition:border-color .22s}
.feat:hover{border-color:rgba(184,255,60,.22)}
.fi{font-size:1.5rem;margin-bottom:.7rem}
.ft{font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem;margin-bottom:.4rem}
.fd2{font-size:.66rem;color:var(--mist);line-height:1.7}
.svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:.85rem}
.svc{background:var(--card);border:1px solid var(--border);padding:1.2rem;border-radius:4px;transition:all .22s;display:flex;flex-direction:column}
.svc:hover{transform:translateY(-3px);border-color:rgba(184,255,60,.22);box-shadow:0 12px 32px rgba(0,0,0,.3)}
.svc-type{font-size:.48rem;font-weight:700;letter-spacing:.07em;padding:.12em .42em;border-radius:2px;display:inline-block;background:rgba(184,255,60,.1);color:var(--acid);margin-bottom:.6rem}
.svc-nm{font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem;margin-bottom:.35rem}
.svc-px{font-family:'Syne',sans-serif;font-weight:800;font-size:1.35rem;color:var(--acid);margin-top:auto;padding-top:.6rem}
.tools-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.55rem}
.tool-it{background:var(--card);border:1px solid var(--border);padding:.7rem .85rem;font-size:.64rem;border-radius:3px;display:flex;align-items:center;gap:.5rem;transition:all .18s;text-decoration:none;color:var(--mist)}
.tool-it:hover{border-color:rgba(184,255,60,.25);color:var(--paper);background:rgba(184,255,60,.03)}
.cta-block{background:linear-gradient(135deg,rgba(184,255,60,.08),rgba(84,160,255,.05));border:1px solid rgba(184,255,60,.15);padding:4rem;text-align:center;border-radius:8px;margin:4rem 0}
.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.85rem}
.blog-card{background:var(--card);border:1px solid var(--border);border-radius:4px;overflow:hidden;transition:border-color .22s}
.blog-card:hover{border-color:rgba(184,255,60,.2)}
.blog-img{height:140px;background:linear-gradient(135deg,var(--card2),rgba(184,255,60,.04));display:flex;align-items:center;justify-content:center;font-size:2.5rem}
.blog-body{padding:1rem}
.blog-cat{font-size:.5rem;font-weight:700;letter-spacing:.1em;color:var(--acid);margin-bottom:.4rem;text-transform:uppercase}
.blog-title{font-family:'Syne',sans-serif;font-weight:700;font-size:.82rem;line-height:1.35;margin-bottom:.5rem}
.blog-ex{font-size:.64rem;color:var(--mist);line-height:1.6}
@media(max-width:900px){.feats{grid-template-columns:1fr 1fr}.blog-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.feats{grid-template-columns:1fr}.stats{grid-template-columns:1fr 1fr}.blog-grid{grid-template-columns:1fr}}
</style>

<div class="hero"><div class="c">
  <h1><?=e($ht)?></h1>
  <p class="hs"><?=e($hs)?></p>
  <div class="hcta">
    <a href="<?=e(appUrl())?>/register" class="btn bp" style="padding:.75rem 1.8rem;font-size:.8rem"><?=e($hc)?></a>
    <a href="<?=e(appUrl())?>/pricing" class="btn bs" style="padding:.75rem 1.4rem;font-size:.8rem"><?=e($hc2)?></a>
  </div>
  <div style="margin-top:2rem;font-size:.62rem;color:var(--mist)">✓ Free plan available &nbsp; ✓ No credit card needed &nbsp; ✓ 22+ SEO tools included</div>
</div></div>

<div class="c"><div class="stats">
  <?php for($i=1;$i<=4;$i++):?>
  <div><div class="sv2"><?=e(setting("stat{$i}val",'—'))?></div><div class="sl2"><?=e(setting("stat{$i}lbl",'—'))?></div></div>
  <?php endfor;?>
</div></div>

<div class="section" style="background:var(--card2)"><div class="c">
  <div class="sh">Everything You Need to <em style="color:var(--acid)">Dominate SEO</em></div>
  <div class="ss">From link building to technical audits — one platform for your entire SEO workflow</div>
  <div class="feats">
    <?php foreach([
      ['🔗','Premium Backlinks','Guest posts, niche edits, and homepage links from real DA30-90 websites. Manual outreach, no PBNs.'],
      ['📈','Rank Tracker','Track unlimited keywords daily across Google. Desktop & mobile, any country.'],
      ['🔍','Site Audit','Deep technical SEO analysis powered by AI. Find and fix critical issues fast.'],
      ['🛠','SEO Tools Suite','22 free tools: keyword research, LSI, meta generator, schema builder, and more.'],
      ['✨','Ask AI','Get instant SEO answers, content ideas, and strategy advice from Claude AI.'],
      ['📋','Order Dashboard','Manage all your link orders in one place with real-time status tracking.'],
    ] as [$ico,$t,$d]):?>
    <div class="feat"><div class="fi"><?=$ico?></div><div class="ft"><?=e($t)?></div><div class="fd2"><?=e($d)?></div></div>
    <?php endforeach;?>
  </div>
</div></div>

<?php if(!empty($services)):?>
<div class="section"><div class="c">
  <div class="sh">Featured <em style="color:var(--acid)">Link Building</em> Services</div>
  <div class="ss">Handpicked packages from our publisher network. Every link manually verified.</div>
  <div class="svc-grid">
  <?php foreach($services as $svc):?>
    <div class="svc">
      <div class="svc-type"><?=e(strtoupper(str_replace('_',' ',$svc['type'])))?></div>
      <div class="svc-nm"><?=e($svc['name'])?></div>
      <?php if($svc['description']):?><div style="font-size:.63rem;color:var(--mist);line-height:1.6;margin-bottom:.5rem"><?=e(substr($svc['description'],0,90))?>...</div><?php endif;?>
      <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-bottom:.5rem">
        <span style="font-size:.52rem;background:rgba(255,255,255,.05);border:1px solid var(--border);padding:.1em .4em;border-radius:2px;color:var(--mist)">DA<?=(int)$svc['min_da']?>+</span>
        <span style="font-size:.52rem;background:rgba(255,255,255,.05);border:1px solid var(--border);padding:.1em .4em;border-radius:2px;color:var(--mist)">DR<?=(int)$svc['min_dr']?>+</span>
        <span style="font-size:.52rem;background:rgba(255,255,255,.05);border:1px solid var(--border);padding:.1em .4em;border-radius:2px;color:var(--mist)">⏱<?=(int)$svc['turnaround_days']?>d</span>
      </div>
      <div class="svc-px"><?=currSym()?><?=number_format((float)$svc['price'],0)?><span style="font-size:.58rem;color:var(--mist);font-weight:400"> /link</span></div>
      <a href="<?=e(appUrl())?>/orders/new" class="btn bp" style="margin-top:.7rem;justify-content:center">Order Now</a>
    </div>
  <?php endforeach;?>
  </div>
  <div style="text-align:center;margin-top:1.5rem"><a href="<?=e(appUrl())?>/services" class="btn bs" style="padding:.65rem 1.6rem">View All Services →</a></div>
</div></div>
<?php endif;?>

<div class="section" style="background:var(--card2)"><div class="c">
  <div class="sh">Free <em style="color:var(--acid)">SEO Tools</em></div>
  <div class="ss">22 AI-powered tools to supercharge your SEO workflow</div>
  <div class="tools-grid">
  <?php foreach([['keyword','🔍','Keyword Research'],['lsi','🧩','LSI Keywords'],['meta','🏷','Meta Generator'],['schema','🏗','Schema Builder'],['readability','📖','Readability'],['paraphrase','♻️','Paraphraser'],['seo_brief','📋','SEO Brief'],['anchor','🔗','Anchor Text'],['robots','🤖','Robots.txt'],['sitemap','🗺','Sitemap'],['utm','🔗','UTM Builder'],['word_count','📏','Word Counter'],['outreach','📧','Outreach Email'],['url_slug','🔤','URL Slug'],['title_gen','✍️','Title Generator'],['outline','📝','Article Outline'],['case','Aa','Case Converter'],['url_encode','🔒','URL Encoder'],['redirects','↪️','Redirect Checker'],['density','📊','Keyword Density'],['longtail','🎯','Long-tail Finder'],['link_value','💎','Link Estimator']] as [$k,$ico,$n]):?>
  <a href="<?=e(appUrl())?>/tools?tool=<?=e($k)?>" class="tool-it"><?=$ico?> <?=e($n)?></a>
  <?php endforeach;?>
  </div>
  <div style="text-align:center;margin-top:1.5rem"><a href="<?=e(appUrl())?>/register" class="btn bp" style="padding:.7rem 1.8rem">Get Free Access →</a></div>
</div></div>

<?php
// Testimonials
try{$testimonials=db()->query("SELECT * FROM `".pfx()."testimonials` WHERE is_featured=1 ORDER BY sort_order LIMIT 6")->fetchAll();}catch(Exception $e){$testimonials=[];}
if(!empty($testimonials)):?>
<div class="section" style="background:var(--card2)"><div class="c">
  <div class="sh">Trusted by <em style="color:var(--acid)">2,400+ SEOs</em></div>
  <div class="ss">Real results from real customers</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.85rem">
  <?php foreach($testimonials as $t):?>
  <div style="background:var(--card);border:1px solid var(--border);padding:1.3rem;border-radius:4px;display:flex;flex-direction:column;gap:.7rem">
    <div style="color:var(--acid);font-size:1rem"><?=str_repeat('★',(int)($t['rating']??5))?></div>
    <div style="font-size:.72rem;color:var(--mist);line-height:1.75;flex:1">"<?=e($t['content'])?>"</div>
    <div style="display:flex;align-items:center;gap:.6rem;padding-top:.5rem;border-top:1px solid var(--border)">
      <?php if($t['avatar']):?><img src="<?=e($t['avatar'])?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover">
      <?php else:?><div style="width:32px;height:32px;border-radius:50%;background:var(--acid);display:flex;align-items:center;justify-content:center;font-family:Syne,sans-serif;font-weight:800;font-size:.7rem;color:var(--ink)"><?=strtoupper(substr($t['name']??'?',0,1))?></div><?php endif;?>
      <div><div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.74rem"><?=e($t['name'])?></div>
      <div style="font-size:.58rem;color:var(--mist)"><?=e($t['role']??'')?></div></div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
</div></div>
<?php endif;?>

<!-- Newsletter -->
<div style="background:linear-gradient(135deg,rgba(84,160,255,.06),rgba(184,255,60,.04));border-top:1px solid rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.05);padding:3rem 0">
<div class="c" style="max-width:560px;text-align:center">
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;margin-bottom:.4rem">📧 Get SEO Tips Weekly</div>
  <div style="font-size:.7rem;color:var(--mist);margin-bottom:1.2rem">Join 5,000+ SEOs getting actionable tips every week. Unsubscribe anytime.</div>
  <div style="display:flex;gap:.4rem;max-width:420px;margin:0 auto" id="nlForm">
    <input type="email" id="nlEmail" class="fc" style="flex:1" placeholder="your@email.com">
    <button class="btn bp" onclick="nlSubmit()">Subscribe →</button>
  </div>
  <div id="nlMsg" style="font-size:.65rem;margin-top:.5rem;display:none"></div>
</div></div>
<script>
async function nlSubmit(){
  var e=document.getElementById('nlEmail').value.trim();
  if(!e)return;
  var r=await fetch('/newsletter/subscribe',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'email='+encodeURIComponent(e)});
  var d=await r.json();
  var m=document.getElementById('nlMsg');
  m.style.display='block';m.style.color=d.ok?'var(--green)':'var(--red)';m.textContent=d.msg;
  if(d.ok)document.getElementById('nlEmail').value='';
}
</script>

<?php if(!empty($posts)):?>
<div class="section"><div class="c">
  <div class="sh">Latest from the <em style="color:var(--acid)">Blog</em></div>
  <div class="ss">SEO guides, link building tips, and platform updates</div>
  <div class="blog-grid">
  <?php foreach($posts as $post):?>
    <a href="<?=e(appUrl())?>/blog/<?=e($post['slug'])?>" class="blog-card">
      <div class="blog-img"><?php $icons=['SEO'=>'🔍','Link Building'=>'🔗','Tools'=>'🛠','Tips'=>'💡'];echo $icons[$post['category']??'']??'📝'?></div>
      <div class="blog-body">
        <?php if($post['category']):?><div class="blog-cat"><?=e($post['category'])?></div><?php endif;?>
        <div class="blog-title"><?=e($post['title'])?></div>
        <div class="blog-ex"><?=e(excerpt($post['excerpt']??$post['content']??'',100))?></div>
      </div>
    </a>
  <?php endforeach;?>
  </div>
  <div style="text-align:center;margin-top:1.5rem"><a href="<?=e(appUrl())?>/blog" class="btn bs" style="padding:.65rem 1.6rem">All Articles →</a></div>
</div></div>
<?php endif;?>

<div class="c"><div class="cta-block">
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.3rem,3.5vw,2.2rem);margin-bottom:.6rem">Ready to Build Better Backlinks?</div>
  <div style="font-size:.76rem;color:var(--mist);margin-bottom:1.8rem;line-height:1.7">Join 2,400+ SEOs using <?=e(appName())?> to grow organic traffic</div>
  <div style="display:flex;gap:.7rem;justify-content:center;flex-wrap:wrap">
    <a href="<?=e(appUrl())?>/register" class="btn bp" style="padding:.75rem 2rem;font-size:.8rem">Start Free Today →</a>
    <a href="<?=e(appUrl())?>/contact" class="btn bs" style="padding:.75rem 1.6rem;font-size:.8rem">Talk to Sales</a>
  </div>
</div></div>

<?php
$pageContent=ob_get_clean();
$pageTitle=setting('hero_title',appName().' — SEO & Backlink Platform');
include LB_ROOT.'/app/views/public/layout.php';
