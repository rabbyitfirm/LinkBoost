<?php
requireLogin();$user=currentUser();$activePage='link_pros';$pageTitle='Link Prospector';
ob_start();?>
<div class="ph"><div><div class="pt">🔎 AI Link Prospector</div><div class="ps">Find backlink opportunities — AI discovers sites that WANT to link to you</div></div></div>

<div class="panel" style="margin-bottom:.9rem"><div class="pnb">
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.6rem;align-items:end">
    <div class="fg" style="margin-bottom:0"><label class="fl">YOUR DOMAIN</label><input type="text" id="lpDomain" class="fc" placeholder="yoursite.com"></div>
    <div class="fg" style="margin-bottom:0"><label class="fl">NICHE/TOPIC</label><input type="text" id="lpNiche" class="fc" placeholder="e.g. digital marketing, SaaS"></div>
    <div class="fg" style="margin-bottom:0"><label class="fl">STRATEGY</label>
      <select id="lpStrategy" class="fc">
        <option value="guest_post">Guest Post Opportunities</option>
        <option value="resource_page">Resource Page Links</option>
        <option value="broken_link">Broken Link Building</option>
        <option value="skyscraper">Skyscraper Opportunities</option>
        <option value="haro">HARO/PR Opportunities</option>
        <option value="partnership">Partnership & Collab</option>
      </select>
    </div>
    <button class="btn bp" onclick="findLinks()" id="lpBtn" style="height:40px">🔎 Find Links →</button>
  </div>
</div></div>

<div id="lpLoading" style="display:none;text-align:center;padding:3rem;color:var(--mist)">
  <div style="font-size:1.8rem;animation:sp3 .9s linear infinite;display:inline-block;margin-bottom:.7rem">🔎</div>
  <div style="font-size:.7rem">Prospecting link opportunities...</div>
</div>
<div id="lpResults" style="display:none"></div>

<script>
const API='https://api.anthropic.com/v1/messages';
async function findLinks(){
  var dom=document.getElementById('lpDomain').value.trim()||'my website';
  var niche=document.getElementById('lpNiche').value.trim()||'digital marketing';
  var strat=document.getElementById('lpStrategy').value;
  document.getElementById('lpBtn').disabled=true;
  document.getElementById('lpLoading').style.display='block';
  document.getElementById('lpResults').style.display='none';

  var stratLabels={guest_post:'Guest Posts',resource_page:'Resource Pages',broken_link:'Broken Links',skyscraper:'Skyscraper',haro:'HARO/PR',partnership:'Partnerships'};

  try{
    var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:1800,
        system:'You are an expert link building specialist. Return ONLY valid JSON.',
        messages:[{role:'user',content:`Find ${stratLabels[strat]} link building opportunities for ${dom} in the ${niche} niche.

Return JSON:
{
  "strategy": "${stratLabels[strat]}",
  "opportunities": [
    {
      "site_type": "Type of website",
      "examples": ["example domain type","another type"],
      "da_range": "DA 30-60",
      "difficulty": "Medium",
      "approach": "Exactly how to get this link",
      "template_subject": "Email subject line to use",
      "template_opening": "First 2 sentences of outreach email",
      "success_rate": "25%",
      "time_investment": "2 hours"
    }
  ],
  "search_queries": ["Google search to find prospects 1","search query 2","search query 3"],
  "tools_to_use": ["Tool 1 and how","Tool 2"],
  "monthly_target": "How many links to aim for",
  "first_step": "The very first thing to do right now"
}`}]})});
    var d=await r.json();
    var data=JSON.parse(d.content?.map(b=>b.text||'').join('').replace(/```json|```/g,'').trim());
    renderLP(data);
  }catch(e){document.getElementById('lpResults').innerHTML='<div class="al al-e">'+e.message+'</div>';document.getElementById('lpResults').style.display='block';}
  document.getElementById('lpBtn').disabled=false;
  document.getElementById('lpLoading').style.display='none';
}

function renderLP(d){
  var html='';
  if(d.first_step)html+=`<div class="al al-s" style="font-size:.7rem">⚡ <strong>Do This First:</strong> ${d.first_step}</div>`;

  html+=`<div style="display:flex;flex-direction:column;gap:.8rem;margin-bottom:.9rem">`;
  (d.opportunities||[]).forEach((op,i)=>{
    html+=`<div class="panel">
      <div class="pnh">
        <div class="pnt">${i+1}. ${op.site_type}</div>
        <div class="pna">
          <span class="bdg b-info">${op.da_range||''}</span>
          <span class="bdg ${op.difficulty==='Low'?'b-success':op.difficulty==='Medium'?'b-warn':'b-danger'}">${op.difficulty}</span>
          <span class="bdg b-muted">Success: ${op.success_rate||'—'}</span>
        </div>
      </div>
      <div style="padding:.85rem;display:grid;grid-template-columns:1fr 1fr;gap:.8rem;font-size:.68rem">
        <div>
          <div style="color:var(--mist);margin-bottom:.3rem;font-size:.55rem;letter-spacing:.08em">APPROACH</div>
          <div style="line-height:1.6">${op.approach}</div>
          ${op.examples?.length?`<div style="margin-top:.5rem;color:var(--mist);font-size:.6rem">Example sites: ${op.examples.join(', ')}</div>`:''}
        </div>
        <div>
          <div style="background:var(--card2);border:1px solid var(--border);padding:.7rem;border-radius:3px">
            <div style="color:var(--mist);font-size:.55rem;letter-spacing:.08em;margin-bottom:.3rem">OUTREACH TEMPLATE</div>
            <div style="color:var(--acid);font-weight:600;margin-bottom:.2rem">Subject: ${op.template_subject||'—'}</div>
            <div style="color:var(--mist);font-style:italic">${op.template_opening||'—'}</div>
          </div>
          <div style="margin-top:.4rem;font-size:.6rem;color:var(--mist)">⏱ Time: ${op.time_investment||'—'}</div>
        </div>
      </div>
    </div>`;
  });
  html+='</div>';

  if(d.search_queries?.length){
    html+=`<div class="panel"><div class="pnh"><div class="pnt">🔍 Google Search Queries to Find Prospects</div></div><div style="padding:.85rem;display:flex;flex-direction:column;gap:.4rem">
      ${d.search_queries.map(q=>`<div style="display:flex;align-items:center;gap:.6rem;font-size:.68rem">
        <span style="color:var(--mist);flex-shrink:0">→</span>
        <code style="background:rgba(255,255,255,.05);border:1px solid var(--border);padding:.2em .6em;border-radius:2px;flex:1">${q}</code>
        <a href="https://www.google.com/search?q=${encodeURIComponent(q)}" target="_blank" class="btn bi bxs">Search</a>
      </div>`).join('')}
    </div></div>`;
  }

  if(d.monthly_target)html+=`<div class="al al-i" style="margin-top:.7rem">🎯 Monthly target: <strong>${d.monthly_target}</strong></div>`;

  document.getElementById('lpResults').innerHTML=html;
  document.getElementById('lpResults').style.display='block';
}
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
