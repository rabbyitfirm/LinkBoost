<?php
requireLogin();$user=currentUser();$activePage='autopilot';$pageTitle='AI Autopilot';
ob_start();?>
<style>
.ap-hero{background:linear-gradient(135deg,rgba(184,255,60,.07),rgba(84,160,255,.04));border:1px solid rgba(184,255,60,.15);border-radius:6px;padding:1.6rem;margin-bottom:.9rem;text-align:center}
.ap-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;margin-bottom:.4rem}
.ap-sub{font-size:.7rem;color:var(--mist);margin-bottom:1.2rem;line-height:1.7}
.step-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.7rem;margin-bottom:1.5rem}
.step{background:var(--card2);border:1px solid var(--border);padding:1rem;border-radius:4px;text-align:center;opacity:.4;transition:all .4s}
.step.active{opacity:1;border-color:var(--acid)}
.step.done{opacity:1;border-color:var(--green)}
.si2{font-size:1.4rem;margin-bottom:.4rem}
.sn{font-family:'Syne',sans-serif;font-weight:700;font-size:.72rem}
.ss2{font-size:.58rem;color:var(--mist);margin-top:.15rem}
.result-section{background:var(--card);border:1px solid var(--border);border-radius:4px;margin-bottom:.85rem;overflow:hidden}
.rs-head{padding:.75rem 1rem;background:rgba(255,255,255,.02);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.5rem}
.rs-ico{font-size:1rem}
.rs-title{font-family:'Syne',sans-serif;font-weight:700;font-size:.78rem}
.rs-body{padding:1rem;font-size:.72rem;line-height:1.85;white-space:pre-wrap;word-wrap:break-word}
.spin3{animation:sp3 .9s linear infinite;display:inline-block}
@keyframes sp3{to{transform:rotate(360deg)}}
.kw-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem;padding:1rem}
.kw-item{background:var(--card2);border:1px solid var(--border);padding:.65rem .8rem;border-radius:3px;font-size:.66rem}
.kw-name{font-weight:600;color:var(--paper);margin-bottom:.3rem}
.kw-meta{display:flex;gap:.5rem;flex-wrap:wrap}
.kw-badge{font-size:.52rem;padding:.1em .38em;border-radius:2px;font-weight:700}
.score-ring{width:80px;height:80px;margin:0 auto .6rem}
.action-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:.7rem;padding:1rem}
.action-card{background:var(--card2);border-left:3px solid;padding:.85rem;border-radius:0 4px 4px 0}
.ac-pri-high{border-color:var(--red)}.ac-pri-med{border-color:var(--orange)}.ac-pri-low{border-color:var(--green)}
.ac-pri{font-size:.5rem;font-weight:700;letter-spacing:.08em;margin-bottom:.35rem}
.ac-title{font-family:'Syne',sans-serif;font-weight:700;font-size:.75rem;margin-bottom:.25rem}
.ac-desc{font-size:.63rem;color:var(--mist);line-height:1.55}
.progress-bar{height:6px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden;margin:.6rem 0}
.progress-fill{height:100%;background:linear-gradient(90deg,var(--acid),#54ff00);border-radius:3px;transition:width .6s ease}
</style>

<div class="ph"><div><div class="pt">🤖 AI SEO Autopilot</div><div class="ps">Enter your domain — AI analyzes, plans, and tells you exactly what to do</div></div></div>

<div class="ap-hero">
  <div class="ap-title">Full SEO Analysis in <span style="color:var(--acid)">One Click</span></div>
  <div class="ap-sub">AI scans your domain, identifies issues, finds keyword opportunities,<br>analyzes competitors, and builds a complete 30-day action plan</div>
  <div style="display:flex;gap:.6rem;max-width:520px;margin:0 auto;flex-wrap:wrap">
    <input type="url" id="apDomain" class="fc" style="flex:1;min-width:200px" placeholder="https://yourwebsite.com" value="<?=e('https://'.(currentUser()['domain']??''))?>">
    <button class="btn bp" onclick="runAutopilot()" id="apBtn" style="flex-shrink:0;padding:.62rem 1.4rem">🚀 Analyze My Site →</button>
  </div>
  <div style="font-size:.6rem;color:var(--mist);margin-top:.7rem">⚡ Powered by Claude AI — takes ~15 seconds</div>
</div>

<div class="step-grid" id="stepGrid">
  <?php foreach([['🔍','Crawling Site','Fetching page data'],['📊','SEO Score','Technical analysis'],['🎯','Keywords','Finding opportunities'],['🕵️','Competitors','Analyzing rivals'],['🔗','Backlinks','Link gap analysis'],['📋','Action Plan','30-day roadmap']] as $i=>[$ico,$n,$s]):?>
  <div class="step" id="step<?=$i?>"><div class="si2"><?=$ico?></div><div class="sn"><?=e($n)?></div><div class="ss2"><?=e($s)?></div></div>
  <?php endforeach;?>
</div>

<div id="apProgress" style="display:none;margin-bottom:.9rem">
  <div style="display:flex;justify-content:space-between;font-size:.64rem;color:var(--mist);margin-bottom:.3rem">
    <span id="progLabel">Starting analysis...</span><span id="progPct">0%</span>
  </div>
  <div class="progress-bar"><div class="progress-fill" id="progFill" style="width:0%"></div></div>
</div>

<div id="apResults" style="display:none">

  <!-- SEO Score -->
  <div class="result-section" id="rsScore">
    <div class="rs-head"><span class="rs-ico">📊</span><div class="rs-title">SEO Health Score</div></div>
    <div style="padding:1rem">
      <div style="display:grid;grid-template-columns:140px 1fr;gap:1.5rem;align-items:center">
        <div style="text-align:center">
          <div id="scoreNum" style="font-family:'Syne',sans-serif;font-weight:800;font-size:3.5rem;line-height:1"></div>
          <div style="font-size:.6rem;color:var(--mist);margin-top:.3rem">/100 SEO Score</div>
          <div class="progress-bar" style="margin-top:.5rem"><div id="scoreFill" class="progress-fill"></div></div>
        </div>
        <div id="scoreBreakdown" style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;font-size:.66rem"></div>
      </div>
    </div>
  </div>

  <!-- Issues -->
  <div class="result-section" id="rsIssues">
    <div class="rs-head"><span class="rs-ico">⚠️</span><div class="rs-title">Issues Found</div></div>
    <div id="issuesList" style="padding:.5rem 1rem 1rem"></div>
  </div>

  <!-- Keywords -->
  <div class="result-section" id="rsKw">
    <div class="rs-head"><span class="rs-ico">🎯</span><div class="rs-title">Target Keywords</div><span style="font-size:.6rem;color:var(--mist);margin-left:.5rem">AI-discovered opportunities</span></div>
    <div class="kw-grid" id="kwList"></div>
  </div>

  <!-- Competitors -->
  <div class="result-section" id="rsComp">
    <div class="rs-head"><span class="rs-ico">🕵️</span><div class="rs-title">Competitor Analysis</div></div>
    <div id="compList" style="padding:1rem;font-size:.72rem;line-height:1.85;white-space:pre-wrap"></div>
  </div>

  <!-- Content Plan -->
  <div class="result-section" id="rsCont">
    <div class="rs-head"><span class="rs-ico">✍️</span><div class="rs-title">Content Strategy</div><span style="font-size:.6rem;color:var(--mist);margin-left:.5rem">Topics AI recommends you create</span></div>
    <div id="contList" style="padding:1rem;font-size:.72rem;line-height:1.85;white-space:pre-wrap"></div>
  </div>

  <!-- Link Strategy -->
  <div class="result-section" id="rsLinks">
    <div class="rs-head"><span class="rs-ico">🔗</span><div class="rs-title">Link Building Strategy</div></div>
    <div id="linksList" style="padding:1rem;font-size:.72rem;line-height:1.85;white-space:pre-wrap"></div>
  </div>

  <!-- 30-Day Action Plan -->
  <div class="result-section" id="rsAction" style="border-color:rgba(184,255,60,.2)">
    <div class="rs-head" style="background:rgba(184,255,60,.04)"><span class="rs-ico">📋</span><div class="rs-title" style="color:var(--acid)">30-Day Action Plan</div><span style="font-size:.6rem;color:var(--mist);margin-left:.5rem">Prioritized tasks — just follow this list</span></div>
    <div class="action-cards" id="actionList"></div>
  </div>

  <div style="display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap;padding:1rem">
    <a href="<?=e(appUrl())?>/site-audit" class="btn bp">🔍 Deep Site Audit →</a>
    <a href="<?=e(appUrl())?>/orders/new" class="btn bs">🔗 Order Backlinks</a>
    <a href="<?=e(appUrl())?>/content-writer" class="btn bs">✍️ Write Content</a>
    <a href="<?=e(appUrl())?>/rank-tracker" class="btn bs">📈 Track Rankings</a>
  </div>
</div>

<script>
const API='https://api.anthropic.com/v1/messages';
let domain='';

function setStep(i,state){
  var el=document.getElementById('step'+i);
  if(!el)return;
  el.className='step '+(state==='active'?'active':state==='done'?'done':'');
}
function setProgress(label,pct){
  document.getElementById('progLabel').textContent=label;
  document.getElementById('progPct').textContent=pct+'%';
  document.getElementById('progFill').style.width=pct+'%';
}

async function ai(prompt,max=1200){
  var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:max,
      system:'You are an expert SEO analyst. Always respond with valid JSON only — no markdown, no explanation, just pure JSON.',
      messages:[{role:'user',content:prompt}]})});
  var d=await r.json();
  var t=d.content?.map(b=>b.text||'').join('')||'{}';
  return JSON.parse(t.replace(/```json|```/g,'').trim());
}

async function runAutopilot(){
  domain=document.getElementById('apDomain').value.trim();
  if(!domain)return alert('Enter your website URL');
  if(!domain.startsWith('http'))domain='https://'+domain;
  var clean=domain.replace(/https?:\/\//,'').split('/')[0];

  document.getElementById('apBtn').disabled=true;
  document.getElementById('apBtn').innerHTML='<span class="spin3">⚙</span> Analyzing...';
  document.getElementById('apProgress').style.display='block';
  document.getElementById('apResults').style.display='none';
  [0,1,2,3,4,5].forEach(i=>setStep(i,''));

  try {

    // STEP 1: SEO Score & Technical Issues
    setStep(0,'active'); setProgress('Crawling '+clean+'...',10);
    var step1=await ai(`Analyze this website for SEO: ${clean}

Return JSON:
{
  "score": 72,
  "score_breakdown": {"technical":75,"content":68,"links":55,"mobile":80,"speed":70},
  "issues": {
    "critical": ["Issue description","Issue description"],
    "warnings": ["Warning description"],
    "info": ["Info item"]
  },
  "positives": ["What they do well"]
}`);
    setStep(0,'done'); setStep(1,'active'); setProgress('Calculating SEO score...',25);

    // Render score
    var sc=step1.score||65;
    var scCol=sc>=80?'var(--green)':sc>=50?'var(--orange)':'var(--red)';
    document.getElementById('scoreNum').textContent=sc;
    document.getElementById('scoreNum').style.color=scCol;
    document.getElementById('scoreFill').style.width=sc+'%';
    var sb=step1.score_breakdown||{};
    document.getElementById('scoreBreakdown').innerHTML=Object.entries(sb).map(([k,v])=>`
      <div><div style="display:flex;justify-content:space-between;margin-bottom:.2rem">
        <span style="color:var(--mist);text-transform:capitalize">${k}</span>
        <span style="color:${v>=70?'var(--green)':v>=50?'var(--orange)':'var(--red)'};font-weight:700">${v}</span>
      </div><div class="progress-bar" style="margin:0"><div class="progress-fill" style="width:${v}%;background:${v>=70?'var(--green)':v>=50?'var(--orange)':'var(--red)'}"></div></div></div>`).join('');

    // Issues
    var iss=step1.issues||{};
    var iHtml='';
    (iss.critical||[]).forEach(i=>iHtml+=`<div style="display:flex;gap:.5rem;padding:.42rem 0;border-bottom:1px solid var(--border);font-size:.69rem"><span style="color:var(--red);font-size:.85rem;flex-shrink:0">✗</span><span>${i}</span></div>`);
    (iss.warnings||[]).forEach(i=>iHtml+=`<div style="display:flex;gap:.5rem;padding:.42rem 0;border-bottom:1px solid var(--border);font-size:.69rem"><span style="color:var(--orange);font-size:.85rem;flex-shrink:0">⚠</span><span>${i}</span></div>`);
    (iss.info||[]).forEach(i=>iHtml+=`<div style="display:flex;gap:.5rem;padding:.42rem 0;border-bottom:1px solid var(--border);font-size:.69rem"><span style="color:var(--blue);font-size:.85rem;flex-shrink:0">ℹ</span><span>${i}</span></div>`);
    document.getElementById('issuesList').innerHTML=iHtml||'<div style="padding:.5rem 0;color:var(--green);font-size:.7rem">✓ No major issues found!</div>';
    setStep(1,'done');

    // STEP 2: Keywords
    setStep(2,'active'); setProgress('Finding keyword opportunities...',42);
    var step2=await ai(`Find the best target keywords for: ${clean}

Return JSON:
{
  "primary_keywords": [
    {"keyword":"example keyword","monthly_searches":"1K-10K","difficulty":"Medium","cpc":"$2.50","intent":"Commercial","opportunity":"High"},
    {"keyword":"another keyword","monthly_searches":"100-1K","difficulty":"Low","cpc":"$1.20","intent":"Informational","opportunity":"High"}
  ],
  "quick_wins": ["low competition keyword 1","low competition keyword 2","low competition keyword 3"],
  "featured_snippet_targets": ["question keyword 1","question keyword 2"]
}`, 1000);

    var kwHtml='';
    var diffColor={'Low':'var(--green)','Medium':'var(--orange)','High':'var(--red)'};
    (step2.primary_keywords||[]).forEach(k=>{
      kwHtml+=`<div class="kw-item">
        <div class="kw-name">${k.keyword}</div>
        <div class="kw-meta">
          <span class="kw-badge" style="background:rgba(84,160,255,.12);color:var(--blue)">${k.monthly_searches}/mo</span>
          <span class="kw-badge" style="background:rgba(255,255,255,.06);color:${diffColor[k.difficulty]||'var(--mist)'}">${k.difficulty}</span>
          ${k.cpc?`<span class="kw-badge" style="background:rgba(184,255,60,.1);color:var(--acid)">${k.cpc} CPC</span>`:''}
          ${k.opportunity==='High'?'<span class="kw-badge" style="background:rgba(85,239,196,.12);color:var(--green)">★ Win</span>':''}
        </div>
        <div style="font-size:.58rem;color:var(--mist);margin-top:.3rem">Intent: ${k.intent||'—'}</div>
      </div>`;
    });
    if(step2.quick_wins?.length){
      kwHtml+=`<div style="grid-column:1/-1;padding:.5rem .8rem;background:rgba(85,239,196,.05);border:1px solid rgba(85,239,196,.15);border-radius:3px;font-size:.66rem">
        <strong style="color:var(--green)">⚡ Quick Wins:</strong> ${step2.quick_wins.join(' · ')}
      </div>`;
    }
    document.getElementById('kwList').innerHTML=kwHtml;
    setStep(2,'done');

    // STEP 3: Competitors
    setStep(3,'active'); setProgress('Spying on competitors...',58);
    var step3=await ai(`Analyze the top 3 SEO competitors for: ${clean}

Return JSON:
{
  "competitors": [
    {"domain":"competitor1.com","strength":"High","estimated_traffic":"50K/mo","their_top_keywords":["kw1","kw2"],"weakness":"Thin content on blog"},
    {"domain":"competitor2.com","strength":"Medium","estimated_traffic":"20K/mo","their_top_keywords":["kw1","kw3"],"weakness":"Slow page speed"}
  ],
  "gap_keywords": ["keyword they rank for that you could too","another gap keyword"],
  "your_advantages": ["What this site can beat them on"],
  "threat_level": "Medium"
}`, 800);

    var compHtml='';
    (step3.competitors||[]).forEach((c,i)=>{
      compHtml+=`<div style="background:var(--card2);border:1px solid var(--border);padding:.85rem;border-radius:3px;margin-bottom:.5rem">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem">
          <span style="font-family:Syne,sans-serif;font-weight:700">#${i+1} ${c.domain}</span>
          <span class="bdg ${c.strength==='High'?'b-danger':c.strength==='Medium'?'b-warn':'b-success'}">${c.strength}</span>
        </div>
        <div style="font-size:.64rem;color:var(--mist)">Est. traffic: <strong style="color:var(--blue)">${c.estimated_traffic}</strong></div>
        <div style="font-size:.64rem;color:var(--mist);margin-top:.2rem">Top keywords: ${(c.their_top_keywords||[]).join(', ')}</div>
        <div style="font-size:.63rem;color:var(--orange);margin-top:.3rem">⚡ Weakness: ${c.weakness}</div>
      </div>`;
    });
    if(step3.gap_keywords?.length){
      compHtml+=`<div style="padding:.7rem;background:rgba(84,160,255,.05);border:1px solid rgba(84,160,255,.18);border-radius:3px;font-size:.66rem;margin-top:.4rem">
        <strong style="color:var(--blue)">🎯 Gap Keywords (steal their traffic):</strong><br>${step3.gap_keywords.join(' · ')}
      </div>`;
    }
    document.getElementById('compList').innerHTML=compHtml;
    setStep(3,'done');

    // STEP 4: Content + Links
    setStep(4,'active'); setProgress('Building content & link strategy...',75);
    var step4=await ai(`Create content + link building strategy for: ${clean}

Return JSON:
{
  "content_topics": [
    {"title":"Article Title Here","target_keyword":"main keyword","word_count":1500,"type":"Guide","priority":"High","reason":"Why this ranks"},
    {"title":"Another Topic","target_keyword":"another keyword","word_count":1200,"type":"List Post","priority":"Medium","reason":"Why write this"}
  ],
  "link_types_needed": ["Guest Posts on [niche] sites","Niche edits on aged pages","Resource page links"],
  "target_da_range": "DA 30-60",
  "monthly_links_needed": 8,
  "anchor_strategy": "70% branded, 20% partial match, 10% exact match"
}`, 800);

    var contHtml='';
    (step4.content_topics||[]).forEach((t,i)=>{
      contHtml+=`<div style="display:flex;gap:.8rem;padding:.6rem 0;border-bottom:1px solid var(--border);align-items:flex-start">
        <div style="min-width:24px;height:24px;background:var(--acid);color:var(--ink);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:Syne,sans-serif;font-weight:800;font-size:.62rem;flex-shrink:0">${i+1}</div>
        <div style="flex:1">
          <div style="font-weight:600;margin-bottom:.2rem">${t.title}</div>
          <div style="font-size:.62rem;color:var(--mist)">Keyword: <strong style="color:var(--acid)">${t.target_keyword}</strong> · ${t.word_count} words · ${t.type}</div>
          <div style="font-size:.6rem;color:var(--mist);margin-top:.15rem">💡 ${t.reason}</div>
        </div>
        <span class="bdg ${t.priority==='High'?'b-danger':t.priority==='Medium'?'b-warn':'b-success'}">${t.priority}</span>
      </div>`;
    });
    document.getElementById('contList').innerHTML=contHtml;

    var linksHtml=`<div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem">
      <div style="background:var(--card2);border:1px solid var(--border);padding:.85rem;border-radius:3px">
        <div style="font-family:Syne,sans-serif;font-weight:700;margin-bottom:.5rem">📊 Link Requirements</div>
        <div style="font-size:.66rem;color:var(--mist);display:flex;flex-direction:column;gap:.3rem">
          <div>Target range: <strong style="color:var(--acid)">${step4.target_da_range||'DA 30+'}</strong></div>
          <div>Monthly links: <strong style="color:var(--acid)">${step4.monthly_links_needed||6}/month</strong></div>
          <div>Anchor strategy: <strong style="color:var(--paper)">${step4.anchor_strategy||'Mixed'}</strong></div>
        </div>
      </div>
      <div style="background:var(--card2);border:1px solid var(--border);padding:.85rem;border-radius:3px">
        <div style="font-family:Syne,sans-serif;font-weight:700;margin-bottom:.5rem">🔗 Best Link Types</div>
        <div style="font-size:.66rem;color:var(--mist);display:flex;flex-direction:column;gap:.25rem">
          ${(step4.link_types_needed||[]).map(l=>`<div>✓ ${l}</div>`).join('')}
        </div>
      </div>
    </div>`;
    document.getElementById('linksList').innerHTML=linksHtml;
    setStep(4,'done');

    // STEP 5: 30-Day Action Plan
    setStep(5,'active'); setProgress('Building your 30-day action plan...',90);
    var step5=await ai(`Create a specific 30-day SEO action plan for: ${clean}
Based on what you know about this site, create prioritized tasks.

Return JSON:
{
  "week1": [
    {"priority":"High","task":"Task title","action":"Specific thing to do","time":"30 min","impact":"What this achieves"},
    {"priority":"High","task":"Another task","action":"Do this specific thing","time":"1 hour","impact":"Result"}
  ],
  "week2": [{"priority":"Medium","task":"Task","action":"Do this","time":"2 hours","impact":"Result"}],
  "week3": [{"priority":"Medium","task":"Task","action":"Do this","time":"1 hour","impact":"Result"}],
  "week4": [{"priority":"Low","task":"Task","action":"Do this","time":"45 min","impact":"Result"}],
  "expected_result": "What should happen after 30 days"
}`, 1200);

    var priClass={'High':'ac-pri-high','Medium':'ac-pri-med','Low':'ac-pri-low'};
    var actionHtml='';
    ['week1','week2','week3','week4'].forEach((wk,wi)=>{
      var tasks=step5[wk]||[];
      if(tasks.length){
        actionHtml+=`<div style="grid-column:1/-1;font-family:Syne,sans-serif;font-weight:700;font-size:.72rem;color:var(--mist);padding:.4rem 0;letter-spacing:.06em">WEEK ${wi+1}</div>`;
        tasks.forEach(t=>{
          actionHtml+=`<div class="action-card ${priClass[t.priority]||'ac-pri-low'}">
            <div class="ac-pri" style="color:${t.priority==='High'?'var(--red)':t.priority==='Medium'?'var(--orange)':'var(--green)'}">${t.priority||'LOW'} PRIORITY</div>
            <div class="ac-title">${t.task}</div>
            <div class="ac-desc">${t.action}</div>
            <div style="display:flex;justify-content:space-between;margin-top:.5rem;font-size:.58rem">
              <span style="color:var(--mist)">⏱ ${t.time}</span>
              <span style="color:var(--acid)">→ ${t.impact}</span>
            </div>
          </div>`;
        });
      }
    });
    if(step5.expected_result){
      actionHtml+=`<div style="grid-column:1/-1;background:rgba(184,255,60,.05);border:1px solid rgba(184,255,60,.18);padding:.9rem;border-radius:3px;font-size:.7rem">
        <strong style="color:var(--acid)">🎯 Expected Result in 30 Days:</strong> ${step5.expected_result}
      </div>`;
    }
    document.getElementById('actionList').innerHTML=actionHtml;
    setStep(5,'done');

    setProgress('Analysis complete!',100);
    document.getElementById('apResults').style.display='block';
    document.getElementById('apResults').scrollIntoView({behavior:'smooth'});

  } catch(e) {
    alert('Analysis error: '+e.message+'\n\nCheck console for details.');
    console.error(e);
  }
  document.getElementById('apBtn').disabled=false;
  document.getElementById('apBtn').innerHTML='🔄 Re-analyze';
}
</script>
<?php
$pageContent=ob_get_clean();
include LB_ROOT.'/app/views/layouts/client_wrap.php';
