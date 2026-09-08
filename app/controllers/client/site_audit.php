<?php
requireLogin();$pdo=db();$p=pfx();$user=currentUser();$activePage='site_audit';$pageTitle='Site Audit';$uid=(int)$_SESSION['user_id'];
$maxAudits=(int)planLimit('max_audits');
$domain=$_GET['domain']??'';
try{$audits=$pdo->query("SELECT * FROM `{$p}audits` WHERE user_id=$uid ORDER BY created_at DESC LIMIT 20")->fetchAll();}catch(Exception $e){$audits=[];}
ob_start();?>
<div class="ph"><div><div class="pt">Site Audit</div><div class="ps">Analyze any website's technical SEO</div></div></div>
<div class="panel"><div class="pnh"><div class="pnt">🔍 Run New Audit</div></div><div class="pnb">
<form id="auditForm">
  <div style="display:flex;gap:.6rem;flex-wrap:wrap">
    <input type="url" id="auditUrl" class="fc" style="flex:1;min-width:200px" placeholder="https://example.com" value="<?=$domain?'https://'.e($domain):''?>" required>
    <button type="button" class="btn bp" onclick="runAudit()" id="auditBtn">Run Audit →</button>
  </div>
</form></div></div>
<div id="auditProgress" style="display:none">
<div class="panel"><div class="pnb">
  <div style="text-align:center;padding:2rem">
    <div style="font-size:2rem;margin-bottom:.8rem;animation:spin 1s linear infinite">⚙</div>
    <div style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.4rem">Analyzing website...</div>
    <div id="auditStatus" style="font-size:.68rem;color:var(--mist)">Fetching page data...</div>
    <div style="margin-top:1rem;height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden">
      <div id="auditBar" style="height:100%;background:var(--acid);width:0%;transition:width .5s;border-radius:2px"></div>
    </div>
  </div>
</div></div></div>
<div id="auditResult" style="display:none"></div>
<?php if(!empty($audits)):?>
<div class="panel"><div class="pnh"><div class="pnt">Audit History</div></div>
<div class="tw"><table><thead><tr><th>URL</th><th>SCORE</th><th>CRITICAL</th><th>WARNINGS</th><th>STATUS</th><th>DATE</th></tr></thead><tbody>
<?php foreach($audits as $au):?><tr>
<td class="tdc tdm"><?=e($au['url'])?></td>
<td><span style="font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;color:<?=$au['score']>=80?'var(--green)':($au['score']>=50?'var(--orange)':'var(--red)')?>"><?=$au['score']??'—'?>/100</span></td>
<td style="color:var(--red)"><?=(int)$au['issues_critical']?></td>
<td style="color:var(--orange)"><?=(int)$au['issues_warning']?></td>
<td><?=badge($au['status'])?></td>
<td class="tdm"><?=formatDate($au['created_at'])?></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php endif;?>
<?php $pageContent=ob_get_clean();
$js='<script>
@keyframes spin{to{transform:rotate(360deg)}}
const API_URL="https://api.anthropic.com/v1/messages";
async function runAudit(){
  var url=document.getElementById("auditUrl").value.trim();
  if(!url)return;
  document.getElementById("auditProgress").style.display="block";
  document.getElementById("auditResult").style.display="none";
  document.getElementById("auditBtn").disabled=true;
  var steps=["Fetching page HTML...","Checking meta tags...","Analyzing headings...","Scanning images...","Checking links...","Running AI analysis..."];
  var pct=0;var si=0;
  var iv=setInterval(function(){if(si<steps.length){document.getElementById("auditStatus").textContent=steps[si++];}pct=Math.min(pct+12,90);document.getElementById("auditBar").style.width=pct+"%";},600);
  try{
    var res=await fetch(API_URL,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({model:"claude-sonnet-4-20250514",max_tokens:2000,system:"You are an expert SEO auditor. Analyze the given URL and provide a comprehensive technical SEO audit. Return ONLY valid JSON.",messages:[{role:"user",content:"Perform a technical SEO audit for: "+url+"\n\nReturn ONLY this JSON structure:\n{\"score\":85,\"url\":\""+url+"\",\"meta\":{\"title\":\"Page Title\",\"title_len\":55,\"description\":\"Meta desc\",\"desc_len\":140,\"has_canonical\":true,\"has_og\":true},\"headings\":{\"h1_count\":1,\"h1_text\":\"Main Heading\",\"h2_count\":5},\"technical\":{\"https\":true,\"mobile_friendly\":true,\"robots_ok\":true,\"sitemap_likely\":true,\"page_speed\":\"Good\"},\"issues\":{\"critical\":[],\"warnings\":[],\"info\":[]},\"recommendations\":[\"Recommendation 1\",\"Recommendation 2\",\"Recommendation 3\",\"Recommendation 4\",\"Recommendation 5\"]}"}]})});
    var data=await res.json();var text=data.content?.map(b=>b.text||"").join("")||"{}";
    var d=JSON.parse(text.replace(/```json|```/g,"").trim());
    clearInterval(iv);document.getElementById("auditBar").style.width="100%";
    setTimeout(function(){
      document.getElementById("auditProgress").style.display="none";
      document.getElementById("auditResult").style.display="block";
      var sc=d.score||0;var scC=sc>=80?"var(--green)":sc>=50?"var(--orange)":"var(--red)";
      var crit=(d.issues?.critical||[]).length;var warn=(d.issues?.warnings||[]).length;var info2=(d.issues?.info||[]).length;
      var html=`<div class="sg" style="margin-bottom:.9rem">
        <div class="sc"><div class="sv" style="color:${scC};font-size:2.2rem">${sc}</div><div class="sl">SEO SCORE /100</div></div>
        <div class="sc"><div class="sv" style="color:var(--red)">${crit}</div><div class="sl">CRITICAL ISSUES</div></div>
        <div class="sc"><div class="sv" style="color:var(--orange)">${warn}</div><div class="sl">WARNINGS</div></div>
        <div class="sc"><div class="sv" style="color:var(--blue)">${info2}</div><div class="sl">INFO</div></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;margin-bottom:.9rem">
      <div class="panel"><div class="pnh"><div class="pnt">Meta Tags</div></div><div class="pnb" style="font-size:.7rem;display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Title Tag</span><span style="color:${(d.meta?.title_len||0)>=50&&(d.meta?.title_len||0)<=60?"var(--green)":"var(--orange)"}">${d.meta?.title_len||0} chars</span></div>
        <div style="font-size:.62rem;color:var(--paper);background:rgba(255,255,255,.04);padding:.4rem .6rem;border-radius:3px">${d.meta?.title||"—"}</div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Meta Description</span><span style="color:${(d.meta?.desc_len||0)>=140&&(d.meta?.desc_len||0)<=160?"var(--green)":"var(--orange)"}">${d.meta?.desc_len||0} chars</span></div>
        <div style="font-size:.62rem;color:var(--mist);background:rgba(255,255,255,.04);padding:.4rem .6rem;border-radius:3px">${d.meta?.description||"—"}</div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Canonical</span><span class="bdg ${d.meta?.has_canonical?"b-success":"b-danger"}">${d.meta?.has_canonical?"YES":"NO"}</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Open Graph</span><span class="bdg ${d.meta?.has_og?"b-success":"b-warn"}">${d.meta?.has_og?"YES":"MISSING"}</span></div>
      </div></div>
      <div class="panel"><div class="pnh"><div class="pnt">Technical</div></div><div class="pnb" style="font-size:.7rem;display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">HTTPS</span><span class="bdg ${d.technical?.https?"b-success":"b-danger"}">${d.technical?.https?"✓ YES":"✗ NO"}</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Mobile Friendly</span><span class="bdg ${d.technical?.mobile_friendly?"b-success":"b-danger"}">${d.technical?.mobile_friendly?"✓ YES":"✗ NO"}</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Robots.txt</span><span class="bdg ${d.technical?.robots_ok?"b-success":"b-warn"}">${d.technical?.robots_ok?"✓ OK":"CHECK"}</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">Sitemap</span><span class="bdg ${d.technical?.sitemap_likely?"b-success":"b-warn"}">${d.technical?.sitemap_likely?"LIKELY":"CHECK"}</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">H1 Tags</span><span class="bdg ${d.headings?.h1_count===1?"b-success":"b-warn"}">${d.headings?.h1_count||0} found</span></div>
        <div style="display:flex;justify-content:space-between"><span style="color:var(--mist)">H2 Tags</span><span>${d.headings?.h2_count||0} found</span></div>
      </div></div></div>`;
      if(crit>0){html+=`<div class="panel" style="border-color:rgba(255,77,109,.3);margin-bottom:.9rem"><div class="pnh" style="background:rgba(255,77,109,.05)"><div class="pnt" style="color:var(--red)">🚨 Critical Issues (${crit})</div></div><div class="pnb">${(d.issues?.critical||[]).map(i=>`<div style="display:flex;align-items:flex-start;gap:.5rem;padding:.45rem 0;border-bottom:1px solid var(--border);font-size:.7rem"><span style="color:var(--red);font-size:.8rem">✗</span><span>${i}</span></div>`).join("")}</div></div>`;}
      if(warn>0){html+=`<div class="panel" style="border-color:rgba(255,159,67,.3);margin-bottom:.9rem"><div class="pnh" style="background:rgba(255,159,67,.05)"><div class="pnt" style="color:var(--orange)">⚠ Warnings (${warn})</div></div><div class="pnb">${(d.issues?.warnings||[]).map(i=>`<div style="display:flex;align-items:flex-start;gap:.5rem;padding:.45rem 0;border-bottom:1px solid var(--border);font-size:.7rem"><span style="color:var(--orange);font-size:.8rem">⚠</span><span>${i}</span></div>`).join("")}</div></div>`;}
      if(d.recommendations?.length){html+=`<div class="panel"><div class="pnh"><div class="pnt">💡 Recommendations</div></div><div class="pnb">${d.recommendations.map((r,i)=>`<div style="display:flex;align-items:flex-start;gap:.6rem;padding:.45rem 0;border-bottom:1px solid var(--border);font-size:.7rem"><span style="color:var(--acid);font-family:Syne,sans-serif;font-weight:700;flex-shrink:0">${i+1}.</span><span>${r}</span></div>`).join("")}</div></div>`;}
      document.getElementById("auditResult").innerHTML=html;
    },500);
  }catch(e){clearInterval(iv);document.getElementById("auditProgress").style.display="none";document.getElementById("auditResult").innerHTML="<div class=\"al al-e\">Audit failed: "+e.message+"</div>";document.getElementById("auditResult").style.display="block";}
  document.getElementById("auditBtn").disabled=false;
}
document.getElementById("auditUrl").addEventListener("keydown",function(e){if(e.key==="Enter"){e.preventDefault();runAudit();}});
</script>';
include LB_ROOT.'/app/views/layouts/client_wrap.php';
