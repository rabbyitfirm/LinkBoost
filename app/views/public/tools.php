<?php
// Redirect to seo-tools if logged in, else show landing
if(isLoggedIn()){redirect('/seo-tools');}
$activeTool=$_GET['tool']??'keyword';
ob_start();?>
<style>
.tools-hero{padding:4rem 0 2rem;text-align:center;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)}
.tool-tabs2{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.4rem;margin-bottom:1.5rem}
.tt2{padding:.55rem .8rem;font-size:.63rem;font-family:'Syne',sans-serif;font-weight:600;background:var(--card);border:1px solid var(--border);color:var(--mist);cursor:pointer;text-align:left;transition:all .18s;border-radius:3px;text-decoration:none;display:block}
.tt2:hover,.tt2.active{border-color:var(--acid);color:var(--acid);background:rgba(184,255,60,.04)}
.tt2 span{display:block;font-size:.85rem;margin-bottom:.1rem}
.out2{background:rgba(255,255,255,.03);border:1px solid var(--border);padding:.9rem;font-size:.72rem;line-height:1.8;min-height:100px;white-space:pre-wrap;word-wrap:break-word;border-radius:3px;margin-top:.85rem}
</style>
<div class="tools-hero"><div class="c">
<h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.6rem,4vw,2.6rem);margin-bottom:.5rem">Free <span style="color:var(--acid)">SEO Tools</span></h1>
<p style="font-size:.75rem;color:var(--mist);line-height:1.7">22 AI-powered tools — no account needed</p>
</div></div>
<div class="c" style="padding:2.5rem 0">
<?php
$tools=[['keyword','🔍','Keyword Research'],['density','📊','Keyword Density'],['lsi','🧩','LSI Keywords'],['longtail','🎯','Long-tail Keywords'],['meta','🏷','Meta Generator'],['title_gen','✍️','Title Generator'],['schema','🏗','Schema Builder'],['readability','📖','Readability'],['paraphrase','♻️','Paraphraser'],['seo_brief','📋','SEO Brief'],['outline','📝','Outline Generator'],['anchor','🔗','Anchor Text'],['link_value','💎','Link Estimator'],['outreach','📧','Outreach Email'],['url_slug','🔤','URL Slug'],['robots','🤖','Robots.txt'],['sitemap','🗺','Sitemap'],['utm','🔗','UTM Builder'],['word_count','📏','Word Counter'],['case','Aa','Case Converter'],['url_encode','🔒','URL Encoder'],['redirects','↪️','Redirect Checker']];
?>
<div class="tool-tabs2">
<?php foreach($tools as [$key,$ico,$name]):?>
<a href="?tool=<?=e($key)?>" class="tt2 <?=$activeTool===$key?'active':''?>"><span><?=$ico?></span><?=e($name)?></a>
<?php endforeach;?>
</div>
<?php
$toolDefs=['keyword'=>['Keyword Research','Enter a seed keyword to get suggestions, difficulty, and CPC data.','<div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.8rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">SEED KEYWORD</label><input type="text" id="tin" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.7rem .88rem;font-family:DM Mono,monospace;font-size:.8rem;width:100%;outline:none;border-radius:3px" placeholder="e.g. buy backlinks" required></div>','Analyze keyword "{v}". Provide: difficulty (0-100), search volume range, CPC, 10 related keywords with metrics, and top 3 content angles.'],
'density'=>['Keyword Density','Analyze keyword density in your content.','<div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.7rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">CONTENT</label><textarea id="tin" rows="5" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px;resize:vertical" placeholder="Paste content..." required></textarea></div><div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.7rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">TARGET KEYWORD</label><input type="text" id="t2" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.7rem .88rem;font-family:DM Mono,monospace;font-size:.8rem;width:100%;outline:none;border-radius:3px" placeholder="keyword" required></div>','Analyze keyword density for "{v2}" in: {v}\n\nProvide density %, count, rating, and optimization tips.'],
'meta'=>['Meta Generator','Generate SEO-optimized title + description.','<div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.7rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">PAGE TOPIC</label><input type="text" id="tin" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.7rem .88rem;font-family:DM Mono,monospace;font-size:.8rem;width:100%;outline:none;border-radius:3px" placeholder="e.g. buy guest posts" required></div>','Generate 3 meta title + description variations for "{v}". Include character counts. Mark the best option.']];

// Default for tools not in the short list
$default=['lsi'=>['LSI Keywords','Find semantically related keywords.','<div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.7rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">KEYWORD</label><input type="text" id="tin" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.7rem .88rem;font-family:DM Mono,monospace;font-size:.8rem;width:100%;outline:none;border-radius:3px" placeholder="main keyword" required></div>','Generate 25 LSI keywords for "{v}" grouped by type.']];

$td=$toolDefs[$activeTool]??$default[$activeTool]??['SEO Tool','Describe what you need help with.','<div style="display:flex;flex-direction:column;gap:.26rem;margin-bottom:.7rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">YOUR INPUT</label><textarea id="tin" rows="3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px;resize:vertical" placeholder="Enter your content..." required></textarea></div>','Provide SEO analysis for: {v}'];
?>
<div style="background:var(--card);border:1px solid var(--border);padding:1.4rem;border-radius:4px">
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:.2rem"><?=e($td[0])?></div>
  <div style="font-size:.65rem;color:var(--mist);margin-bottom:.9rem"><?=e($td[1])?></div>
  <?=$td[2]?>
  <button onclick="runPublicTool()" id="ptBtn" style="margin-top:.7rem;padding:.62rem 1.2rem;font-family:Syne,sans-serif;font-weight:700;font-size:.72rem;border:none;cursor:pointer;background:var(--acid);color:var(--ink);border-radius:3px;transition:all .18s;display:inline-flex;align-items:center;gap:.4rem">⚙ Run Analysis →</button>
  <div class="out2" id="ptOut" style="display:none"></div>
</div>
</div>
<div style="text-align:center;padding:3rem 0;border-top:1px solid var(--border);margin-top:1.5rem">
<div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:.5rem">Want All 22 Tools + Dashboard?</div>
<p style="font-size:.7rem;color:var(--mist);margin-bottom:1.2rem">Sign up free and unlock rank tracking, site audit, and more.</p>
<a href="<?=e(appUrl())?>/register" class="btn bp" style="padding:.7rem 1.8rem">Create Free Account →</a>
</div>
<script>
var ptpl=<?=json_encode($td[3]??'Analyze: {v}')?>;
async function runPublicTool(){
  var v=document.getElementById("tin")?.value?.trim()||"";
  var v2=document.getElementById("t2")?.value?.trim()||"";
  if(!v)return alert("Please fill in the field");
  var prompt=ptpl.replace(/{v}/g,v).replace(/{v2}/g,v2);
  var btn=document.getElementById("ptBtn");var out=document.getElementById("ptOut");
  btn.disabled=true;btn.textContent="Analyzing...";out.style.display="block";out.textContent="Running AI analysis...";
  try{
    var r=await fetch("https://api.anthropic.com/v1/messages",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({model:"claude-sonnet-4-20250514",max_tokens:800,messages:[{role:"user",content:prompt}]})});
    var d=await r.json();out.textContent=d.content?.map(b=>b.text||"").join("")||"No response";
  }catch(e){out.textContent="Error: "+e.message;}
  btn.disabled=false;btn.textContent="⚙ Run Again";
}
</script>
<?php $pageContent=ob_get_clean();$pageTitle='Free SEO Tools — '.appName();include LB_ROOT.'/app/views/public/layout.php';
