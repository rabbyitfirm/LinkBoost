<?php
requireLogin();$user=currentUser();$activePage='competitor';$pageTitle='Competitor Spy';
ob_start();?>
<div class="ph"><div><div class="pt">🕵️ Competitor Spy</div><div class="ps">Reverse-engineer any competitor's SEO strategy instantly</div></div></div>

<div class="panel" style="margin-bottom:.9rem"><div class="pnb">
  <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:.6rem;align-items:end">
    <div class="fg" style="margin-bottom:0"><label class="fl">YOUR DOMAIN</label><input type="text" id="yourDomain" class="fc" placeholder="yoursite.com"></div>
    <div class="fg" style="margin-bottom:0"><label class="fl">COMPETITOR DOMAIN</label><input type="text" id="compDomain" class="fc" placeholder="competitor.com"></div>
    <button class="btn bp" onclick="spyCompetitor()" id="spyBtn" style="height:40px">🕵️ Spy Now →</button>
  </div>
  <div class="fh" style="margin-top:.5rem">AI will reverse-engineer their entire SEO strategy and find your gaps</div>
</div></div>

<div id="spyLoading" style="display:none;text-align:center;padding:3rem;color:var(--mist)">
  <div style="font-size:1.8rem;animation:sp3 .9s linear infinite;display:inline-block;margin-bottom:.7rem">🕵️</div>
  <div style="font-size:.7rem">Analyzing competitor strategy...</div>
</div>

<div id="spyResults" style="display:none"></div>

<script>
const API='https://api.anthropic.com/v1/messages';
async function spyCompetitor(){
  var you=document.getElementById('yourDomain').value.trim()||'my site';
  var comp=document.getElementById('compDomain').value.trim();
  if(!comp)return alert('Enter competitor domain');
  document.getElementById('spyBtn').disabled=true;
  document.getElementById('spyLoading').style.display='block';
  document.getElementById('spyResults').style.display='none';

  try{
    var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:2000,
        system:'You are an expert competitive SEO analyst. Return ONLY valid JSON.',
        messages:[{role:'user',content:`Analyze ${comp} vs ${you} for SEO.
Return JSON:
{
  "competitor": "${comp}",
  "overview": {"domain_authority":55,"estimated_traffic":"80K/mo","total_keywords":3500,"top_country":"US","niche":"SEO Tools"},
  "top_keywords":[{"keyword":"link building","rank":4,"volume":"22K","type":"Commercial"},{"keyword":"buy backlinks","rank":8,"volume":"8K","type":"Transactional"}],
  "content_strategy":{"post_frequency":"3/week","avg_word_count":2200,"top_formats":["How-to guides","List posts","Case studies"],"topics":["Topic 1","Topic 2","Topic 3"]},
  "link_profile":{"total_backlinks":"45K","referring_domains":1200,"top_link_types":["Guest posts","HARO","Partnerships"],"avg_da_of_links":42},
  "gap_analysis":{"keywords_they_rank_you_dont":["gap kw 1","gap kw 2","gap kw 3"],"content_topics_to_cover":["Topic 1","Topic 2"],"link_opportunities":["Where to get links they have"]},
  "their_weaknesses":["Weakness 1","Weakness 2","Weakness 3"],
  "attack_plan":["Step 1 to beat them","Step 2","Step 3","Step 4","Step 5"],
  "time_to_overtake":"6-12 months with consistent effort"
}`}]})});
    var d=await r.json();
    var data=JSON.parse(d.content?.map(b=>b.text||'').join('').replace(/```json|```/g,'').trim());
    renderSpy(data);
  }catch(e){document.getElementById('spyResults').innerHTML='<div class="al al-e">'+e.message+'</div>';document.getElementById('spyResults').style.display='block';}
  document.getElementById('spyBtn').disabled=false;
  document.getElementById('spyLoading').style.display='none';
}

function renderSpy(d){
  var ov=d.overview||{};
  var html=`
  <div class="sg" style="margin-bottom:.9rem">
    <div class="sc"><div class="sl">DOMAIN AUTHORITY</div><div class="sv">${ov.domain_authority||'—'}</div></div>
    <div class="sc"><div class="sl">EST. TRAFFIC</div><div class="sv" style="color:var(--blue)">${ov.estimated_traffic||'—'}</div></div>
    <div class="sc"><div class="sl">KEYWORDS</div><div class="sv">${ov.total_keywords||'—'}</div></div>
    <div class="sc"><div class="sl">NICHE</div><div class="sv" style="font-size:.9rem">${ov.niche||'—'}</div></div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;margin-bottom:.9rem">
  <div class="panel"><div class="pnh"><div class="pnt">🏆 Their Top Keywords</div></div>
  <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse;font-size:.67rem">
  <thead><tr><th style="padding:.4rem .8rem;text-align:left;border-bottom:1px solid var(--border);font-size:.5rem;letter-spacing:.09em;color:var(--mist)">KEYWORD</th><th style="padding:.4rem .8rem;text-align:left;border-bottom:1px solid var(--border);font-size:.5rem;letter-spacing:.09em;color:var(--mist)">RANK</th><th style="padding:.4rem .8rem;text-align:left;border-bottom:1px solid var(--border);font-size:.5rem;letter-spacing:.09em;color:var(--mist)">VOLUME</th></tr></thead><tbody>
  ${(d.top_keywords||[]).map(k=>`<tr><td style="padding:.45rem .8rem;border-bottom:1px solid rgba(255,255,255,.03);font-weight:600">${k.keyword}</td><td style="padding:.45rem .8rem;border-bottom:1px solid rgba(255,255,255,.03);color:${k.rank<=3?'var(--green)':k.rank<=10?'var(--acid)':'var(--mist)'};font-weight:700">#${k.rank}</td><td style="padding:.45rem .8rem;border-bottom:1px solid rgba(255,255,255,.03);color:var(--blue)">${k.volume}</td></tr>`).join('')}
  </tbody></table></div></div>

  <div class="panel"><div class="pnh"><div class="pnt">🔗 Link Profile</div></div><div style="padding:1rem;font-size:.68rem;display:flex;flex-direction:column;gap:.5rem">
    <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Total Backlinks</span><strong style="color:var(--acid)">${(d.link_profile||{}).total_backlinks||'—'}</strong></div>
    <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Referring Domains</span><strong>${(d.link_profile||{}).referring_domains||'—'}</strong></div>
    <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Avg DA of Links</span><strong>${(d.link_profile||{}).avg_da_of_links||'—'}</strong></div>
    <div style="margin-top:.3rem;padding-top:.5rem;border-top:1px solid var(--border)">
      <div style="color:var(--mist);margin-bottom:.3rem">Top Link Types:</div>
      ${(d.link_profile?.top_link_types||[]).map(l=>`<div>✓ ${l}</div>`).join('')}
    </div>
  </div></div></div>

  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.9rem;margin-bottom:.9rem">
  <div class="panel"><div class="pnh"><div class="pnt">🎯 Keyword Gaps</div><span class="bdg b-success">STEAL THESE</span></div><div style="padding:1rem;display:flex;flex-direction:column;gap:.3rem">
    ${(d.gap_analysis?.keywords_they_rank_you_dont||[]).map(k=>`<div style="font-size:.68rem;padding:.3rem .5rem;background:rgba(85,239,196,.06);border:1px solid rgba(85,239,196,.15);border-radius:2px">${k}</div>`).join('')}
  </div></div>
  <div class="panel"><div class="pnh"><div class="pnt">💀 Their Weaknesses</div></div><div style="padding:1rem;display:flex;flex-direction:column;gap:.4rem">
    ${(d.their_weaknesses||[]).map((w,i)=>`<div style="font-size:.67rem;display:flex;gap:.4rem"><span style="color:var(--red)">✗</span><span>${w}</span></div>`).join('')}
  </div></div>
  <div class="panel"><div class="pnh"><div class="pnt">📝 Content to Create</div></div><div style="padding:1rem;display:flex;flex-direction:column;gap:.3rem">
    ${(d.gap_analysis?.content_topics_to_cover||[]).map(t=>`<div style="font-size:.67rem;display:flex;gap:.4rem"><span style="color:var(--acid)">+</span><span>${t}</span></div>`).join('')}
  </div></div></div>

  <div class="panel" style="border-color:rgba(184,255,60,.2)"><div class="pnh" style="background:rgba(184,255,60,.04)"><div class="pnt" style="color:var(--acid)">⚔️ How to Beat ${d.competitor||'Them'}</div><span class="bdg b-info">${d.time_to_overtake||'6-12 months'}</span></div>
  <div style="padding:1rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.6rem">
    ${(d.attack_plan||[]).map((s,i)=>`<div style="background:var(--card2);border:1px solid var(--border);padding:.8rem;border-radius:3px"><div style="font-family:Syne,sans-serif;font-weight:800;color:var(--acid);font-size:1.2rem;margin-bottom:.3rem">${i+1}</div><div style="font-size:.68rem;line-height:1.5">${s}</div></div>`).join('')}
  </div></div>`;

  document.getElementById('spyResults').innerHTML=html;
  document.getElementById('spyResults').style.display='block';
}
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
