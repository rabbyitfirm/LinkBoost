<?php
requireLogin();$user=currentUser();$activePage='on_page';$pageTitle='On-Page Optimizer';
ob_start();?>
<div class="ph"><div><div class="pt">📄 On-Page SEO Optimizer</div><div class="ps">Paste your content — AI scores it and tells you exactly what to fix</div></div></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;align-items:start">
<div>
<div class="panel" style="margin-bottom:.9rem"><div class="pnh"><div class="pnt">Your Content</div></div><div class="pnb">
  <div class="fg"><label class="fl">TARGET KEYWORD</label><input type="text" id="opKw" class="fc" placeholder="The keyword you want to rank for" required></div>
  <div class="fg"><label class="fl">PAGE URL (optional)</label><input type="url" id="opUrl" class="fc" placeholder="https://yoursite.com/page"></div>
  <div class="fg"><label class="fl">YOUR CONTENT / HTML</label><textarea id="opContent" class="fc" rows="12" placeholder="Paste your page title, H1, full article content, and meta description here..."></textarea></div>
  <button class="btn bp" onclick="runOnPage()" id="opBtn">📊 Analyze & Score →</button>
</div></div>
</div>

<div id="opResults">
<div style="background:var(--card);border:1px solid var(--border);border-radius:4px;padding:2rem;text-align:center;color:var(--mist)">
  <div style="font-size:2rem;margin-bottom:.7rem">📄</div>
  <div style="font-size:.7rem">Paste your content and click Analyze</div>
</div>
</div>
</div>

<script>
const API='https://api.anthropic.com/v1/messages';
async function runOnPage(){
  var kw=document.getElementById('opKw').value.trim();
  var content=document.getElementById('opContent').value.trim();
  if(!kw||!content)return alert('Keyword and content are required');
  document.getElementById('opBtn').disabled=true;document.getElementById('opBtn').textContent='Analyzing...';
  try{
    var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:1800,
        system:'You are an expert on-page SEO analyst. Return ONLY valid JSON.',
        messages:[{role:'user',content:`Analyze this content for SEO optimization.
Keyword: "${kw}"
Content:
${content.substring(0,3000)}

Return JSON:
{
  "score": 72,
  "grade": "B",
  "keyword_analysis": {"density":"1.8%","frequency":12,"placement":{"title":true,"h1":true,"first_paragraph":true,"meta_desc":true},"lsi_found":["related","terms"],"lsi_missing":["should add these"]},
  "title_analysis": {"current":"Current title if found","score":80,"issues":["issue"],"suggestion":"Better title version"},
  "meta_analysis": {"found":true,"length":145,"score":75,"suggestion":"Better meta description"},
  "heading_analysis": {"h1_count":1,"h2_count":5,"h3_count":8,"issues":["any issues"],"suggestions":["improvements"]},
  "content_analysis": {"word_count":1450,"readability":"Good","sentence_length":"Optimal","passive_voice":"Low","issues":["content issues"]},
  "internal_links": {"found":3,"suggestion":"Add 2-3 more internal links to related pages"},
  "image_alt_tags": {"issues":["Missing alt tags"]},
  "fixes": [
    {"priority":"High","element":"Title Tag","issue":"Missing keyword","fix":"Add keyword in first 3 words","impact":"Big ranking boost"},
    {"priority":"Medium","element":"Meta Desc","issue":"Too short","fix":"Expand to 145-155 chars with CTA","impact":"Better CTR"},
    {"priority":"Low","element":"Images","issue":"No alt tags","fix":"Add descriptive alt text","impact":"Image ranking + accessibility"}
  ]
}`}]})});
    var d=await r.json();
    var data=JSON.parse(d.content?.map(b=>b.text||'').join('').replace(/```json|```/g,'').trim());
    renderOnPage(data);
  }catch(e){document.getElementById('opResults').innerHTML='<div class="al al-e">'+e.message+'</div>';}
  document.getElementById('opBtn').disabled=false;document.getElementById('opBtn').textContent='📊 Re-analyze';
}

function renderOnPage(d){
  var sc=d.score||0;var scCol=sc>=80?'var(--green)':sc>=50?'var(--orange)':'var(--red)';
  var html=`
  <div style="text-align:center;background:var(--card);border:1px solid var(--border);padding:1.2rem;border-radius:4px;margin-bottom:.9rem">
    <div style="font-family:Syne,sans-serif;font-weight:800;font-size:3rem;color:${scCol};line-height:1">${sc}</div>
    <div style="font-size:1.5rem;font-family:Syne,sans-serif;font-weight:700;color:${scCol}">${d.grade||'B'}</div>
    <div style="font-size:.6rem;color:var(--mist);margin-top:.3rem">On-Page SEO Score /100</div>
    <div style="height:6px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden;margin:.6rem 1rem 0"><div style="height:100%;background:${scCol};width:${sc}%;border-radius:3px;transition:width .5s"></div></div>
  </div>

  <div class="panel" style="margin-bottom:.7rem"><div class="pnh"><div class="pnt">🔑 Keyword Usage</div></div><div style="padding:.85rem;font-size:.68rem;display:flex;flex-direction:column;gap:.4rem">
    <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Keyword Density</span><strong style="color:${parseFloat(d.keyword_analysis?.density||0)>3?'var(--orange)':'var(--green)'}">${d.keyword_analysis?.density||'—'}</strong></div>
    <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Frequency</span><strong>${d.keyword_analysis?.frequency||'—'}×</strong></div>
    <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.3rem">
      ${Object.entries(d.keyword_analysis?.placement||{}).map(([k,v])=>`<span class="bdg ${v?'b-success':'b-danger'}">${v?'✓':'✗'} ${k.replace('_',' ')}</span>`).join('')}
    </div>
    ${(d.keyword_analysis?.lsi_missing||[]).length?`<div style="margin-top:.4rem;padding:.5rem .7rem;background:rgba(255,159,67,.05);border:1px solid rgba(255,159,67,.18);border-radius:3px">⚠ Add these LSI terms: <strong style="color:var(--orange)">${d.keyword_analysis.lsi_missing.join(', ')}</strong></div>`:''}
  </div></div>

  <div class="panel" style="margin-bottom:.7rem"><div class="pnh"><div class="pnt">🔧 Priority Fixes</div></div><div style="padding:.5rem .85rem 1rem">
    ${(d.fixes||[]).map(f=>`<div style="display:flex;gap:.7rem;padding:.6rem 0;border-bottom:1px solid var(--border);align-items:flex-start">
      <span class="bdg ${f.priority==='High'?'b-danger':f.priority==='Medium'?'b-warn':'b-success'}" style="flex-shrink:0;margin-top:.1rem">${f.priority}</span>
      <div style="flex:1;font-size:.67rem">
        <strong>${f.element}</strong>: ${f.issue}
        <div style="color:var(--mist);margin-top:.15rem">→ ${f.fix}</div>
        <div style="color:var(--acid);font-size:.6rem;margin-top:.1rem">Impact: ${f.impact}</div>
      </div>
    </div>`).join('')}
  </div></div>

  ${d.title_analysis?.suggestion?`<div class="panel" style="margin-bottom:.7rem"><div class="pnh"><div class="pnt">💡 Better Title</div><span class="bdg b-success">SUGGESTED</span></div><div style="padding:.85rem;font-size:.75rem;color:var(--acid);font-family:Syne,sans-serif;font-weight:700">${d.title_analysis.suggestion}</div></div>`:''}
  ${d.meta_analysis?.suggestion?`<div class="panel"><div class="pnh"><div class="pnt">💡 Better Meta Description</div></div><div style="padding:.85rem;font-size:.7rem;line-height:1.6;color:var(--mist)">${d.meta_analysis.suggestion}</div></div>`:''}`;

  document.getElementById('opResults').innerHTML=html;
}
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
