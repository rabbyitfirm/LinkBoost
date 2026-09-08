<?php
requireLogin();$user=currentUser();$activePage='kw_planner';$pageTitle='AI Keyword Planner';
ob_start();?>
<style>
.kp-tabs{display:flex;gap:.4rem;margin-bottom:.9rem;flex-wrap:wrap}
.kpt{padding:.38rem .85rem;font-size:.66rem;font-family:'Syne',sans-serif;font-weight:600;background:var(--card);border:1px solid var(--border);color:var(--mist);cursor:pointer;border-radius:3px;transition:all .2s;text-decoration:none}
.kpt:hover,.kpt.active{border-color:var(--acid);color:var(--acid);background:rgba(184,255,60,.05)}
.kw-table{width:100%;border-collapse:collapse;font-size:.68rem}
.kw-table th{font-size:.5rem;letter-spacing:.09em;color:var(--mist);padding:.4rem .8rem;text-align:left;background:rgba(255,255,255,.02);border-bottom:1px solid var(--border)}
.kw-table td{padding:.52rem .8rem;border-bottom:1px solid rgba(255,255,255,.03)}
.kw-table tr:hover td{background:rgba(255,255,255,.015)}
.diff-bar{height:6px;border-radius:3px;display:inline-block;vertical-align:middle;margin-right:.3rem}
</style>

<div class="ph"><div><div class="pt">🎯 AI Keyword Planner</div><div class="ps">Find keywords, clusters, and full strategies — AI does the research</div></div></div>

<div class="panel" style="margin-bottom:.9rem"><div class="pnb">
  <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:.6rem;align-items:end;flex-wrap:wrap">
    <div class="fg" style="margin-bottom:0"><label class="fl">SEED KEYWORD OR DOMAIN</label><input type="text" id="kwSeed" class="fc" placeholder="e.g. link building services OR yoursite.com"></div>
    <div class="fg" style="margin-bottom:0"><label class="fl">MODE</label>
      <select id="kwMode" class="fc">
        <option value="research">Keyword Research (find opportunities)</option>
        <option value="cluster">Keyword Clustering (group by topic)</option>
        <option value="competitive">Competitive Gap (what rivals rank for)</option>
        <option value="longtail">Long-tail Mining (low difficulty wins)</option>
        <option value="questions">Question Keywords (PAA targets)</option>
        <option value="local">Local SEO Keywords (location-based)</option>
      </select>
    </div>
    <button class="btn bp" onclick="runKwPlanner()" id="kwBtn" style="height:40px">🎯 Find Keywords →</button>
  </div>
</div></div>

<div id="kwLoading" style="display:none;text-align:center;padding:2.5rem;color:var(--mist)">
  <div style="font-size:1.8rem;animation:sp3 .9s linear infinite;display:inline-block;margin-bottom:.7rem">⚙</div>
  <div style="font-size:.7rem" id="kwLoadMsg">Researching keywords...</div>
</div>

<div id="kwResults" style="display:none"></div>

<script>
const API='https://api.anthropic.com/v1/messages';
async function runKwPlanner(){
  var seed=document.getElementById('kwSeed').value.trim();
  var mode=document.getElementById('kwMode').value;
  if(!seed)return alert('Enter a keyword or domain');
  document.getElementById('kwBtn').disabled=true;
  document.getElementById('kwLoading').style.display='block';
  document.getElementById('kwResults').style.display='none';
  var msgs={research:'Analyzing keyword data...',cluster:'Clustering keywords by topic...',competitive:'Finding competitor keyword gaps...',longtail:'Mining long-tail opportunities...',questions:'Extracting question keywords...',local:'Finding local keyword opportunities...'};
  document.getElementById('kwLoadMsg').textContent=msgs[mode]||'Researching...';
  var prompts={
    research:`Research keywords for: "${seed}"\nReturn JSON: {"keywords":[{"keyword":"...","volume":"1K-10K","difficulty":35,"cpc":"$2.50","intent":"Commercial","trend":"Growing","score":8}],"strategy_notes":"...","total_opportunity":"..."}`,
    cluster:`Cluster keywords around: "${seed}"\nReturn JSON: {"clusters":[{"topic":"Topic Name","keywords":["kw1","kw2","kw3"],"main_page":"Suggested page title","difficulty":"Medium","potential":"High"}],"pillar_page":"Main topic page suggestion"}`,
    competitive:`Find keywords competitors of "${seed}" rank for that "${seed}" could target.\nReturn JSON: {"gap_keywords":[{"keyword":"...","competitor_ranking":"...","difficulty":"Low","volume":"500-2K","opportunity":"Why you can rank here"}],"strategy":"How to attack these"}`,
    longtail:`Find low-competition long-tail keywords for: "${seed}"\nReturn JSON: {"quick_wins":[{"keyword":"long tail keyword here","difficulty":15,"volume":"50-500","intent":"Commercial","why_easy":"Reason"}],"estimated_timeframe":"How long to rank"}`,
    questions:`Find question-based keywords for: "${seed}"\nReturn JSON: {"questions":[{"question":"How do you...?","search_volume":"500-2K","featured_snippet_chance":"High","answer_format":"List","difficulty":"Low"}],"paa_strategy":"How to target these"}`,
    local:`Find local SEO keywords for: "${seed}"\nReturn JSON: {"local_keywords":[{"keyword":"...","city":"City Name","volume":"100-500","competition":"Low","gmb_relevant":true}],"local_strategy":"How to dominate local search"}`
  };
  try{
    var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:1500,
        system:'You are an expert keyword researcher. Return ONLY valid JSON, no markdown.',
        messages:[{role:'user',content:prompts[mode]}]})});
    var d=await r.json();
    var data=JSON.parse(d.content?.map(b=>b.text||'').join('').replace(/```json|```/g,'').trim());
    renderKwResults(mode,data,seed);
  }catch(e){document.getElementById('kwResults').innerHTML='<div class="al al-e">Error: '+e.message+'</div>';document.getElementById('kwResults').style.display='block';}
  document.getElementById('kwBtn').disabled=false;
  document.getElementById('kwLoading').style.display='none';
}

function diffColor(d){return d<30?'var(--green)':d<60?'var(--orange)':'var(--red)';}

function renderKwResults(mode,data,seed){
  var html='';
  if(mode==='research'&&data.keywords){
    html=`<div class="panel"><div class="pnh"><div class="pnt">Keywords for "${seed}"</div><div class="pna"><span class="bdg b-info">${data.keywords.length} keywords found</span></div></div>
    <div style="overflow-x:auto"><table class="kw-table"><thead><tr><th>#</th><th>KEYWORD</th><th>VOLUME</th><th>DIFFICULTY</th><th>CPC</th><th>INTENT</th><th>TREND</th><th>SCORE</th></tr></thead><tbody>`;
    data.keywords.forEach((k,i)=>{
      var diff=parseInt(k.difficulty)||50;
      html+=`<tr><td style="color:var(--mist)">${i+1}</td><td style="font-weight:600">${k.keyword}</td><td style="color:var(--blue)">${k.volume||'—'}</td>
      <td><div class="diff-bar" style="width:${diff}px;max-width:60px;background:${diffColor(diff)}"></div><span style="color:${diffColor(diff)}">${diff}</span></td>
      <td style="color:var(--acid)">${k.cpc||'—'}</td><td><span class="bdg ${k.intent==='Commercial'?'b-success':k.intent==='Informational'?'b-info':'b-muted'}">${k.intent||'—'}</span></td>
      <td style="color:${k.trend==='Growing'?'var(--green)':'var(--mist)'}">${k.trend||'—'}</td>
      <td style="font-family:Syne,sans-serif;font-weight:800;color:var(--acid)">${k.score||'—'}/10</td></tr>`;
    });
    html+=`</tbody></table></div>`;
    if(data.strategy_notes)html+=`<div style="padding:.8rem 1rem;font-size:.68rem;color:var(--mist);border-top:1px solid var(--border)">💡 ${data.strategy_notes}</div>`;
    html+='</div>';
  }else if(mode==='cluster'&&data.clusters){
    html=`<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:.8rem">`;
    data.clusters.forEach(c=>{
      html+=`<div class="panel"><div class="pnh"><div class="pnt">📁 ${c.topic}</div><span class="bdg ${c.potential==='High'?'b-success':'b-info'}">${c.potential||''}</span></div>
      <div style="padding:.8rem;font-size:.66rem">
        <div style="color:var(--mist);margin-bottom:.4rem">Main page: <strong style="color:var(--acid)">${c.main_page}</strong></div>
        <div style="display:flex;flex-wrap:wrap;gap:.3rem">${(c.keywords||[]).map(k=>`<span style="background:rgba(255,255,255,.05);border:1px solid var(--border);padding:.12em .4em;border-radius:2px;font-size:.6rem">${k}</span>`).join('')}</div>
      </div></div>`;
    });
    html+='</div>';
    if(data.pillar_page)html+=`<div class="al al-i" style="margin-top:.7rem">🏛 Pillar page suggestion: <strong>${data.pillar_page}</strong></div>`;
  }else{
    // Generic render for other modes
    html=`<div class="panel"><div class="pnh"><div class="pnt">Results</div></div><div style="padding:1rem;font-size:.72rem;line-height:1.8;white-space:pre-wrap">${JSON.stringify(data,null,2)}</div></div>`;
  }
  document.getElementById('kwResults').innerHTML=html;
  document.getElementById('kwResults').style.display='block';
}
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
