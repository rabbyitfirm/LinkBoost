<?php
requireLogin();$user=currentUser();$activePage='content_writer';$pageTitle='AI Content Writer';
ob_start();?>
<style>
.cw-output{background:rgba(255,255,255,.03);border:1px solid var(--border);padding:1.2rem;font-size:.74rem;line-height:1.95;min-height:200px;border-radius:3px;margin-top:.9rem;white-space:pre-wrap;word-wrap:break-word;max-height:600px;overflow-y:auto}
.cw-types{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.4rem;margin-bottom:.9rem}
.cwt{padding:.5rem .7rem;font-size:.62rem;font-family:'Syne',sans-serif;font-weight:600;background:var(--card);border:1px solid var(--border);color:var(--mist);cursor:pointer;border-radius:3px;transition:all .2s;text-align:center}
.cwt:hover,.cwt.active{border-color:var(--acid);color:var(--acid);background:rgba(184,255,60,.04)}
.word-est{font-size:.58rem;color:var(--mist);display:block;margin-top:.15rem}
</style>

<div class="ph"><div><div class="pt">✍️ AI Content Writer</div><div class="ps">Write full SEO-optimized articles, meta tags, and content — in seconds</div></div></div>

<div class="cw-types">
  <?php foreach([['full_post','📄','Full Blog Post','1500-3000 words'],['outline','📋','Article Outline','Structure first'],['intro','🎯','Introduction','Hook + context'],['meta','🏷','Meta Tags','Title + description'],['faq','❓','FAQ Section','10 Q&As'],['conclusion','🏁','Conclusion','CTA + summary'],['product_desc','🛒','Product Page','Convert visitors'],['landing','🚀','Landing Page','Lead generation']] as [$val,$ico,$lbl,$est]):?>
  <div class="cwt" onclick="setCType('<?=e($val)?>')" id="cwt_<?=e($val)?>" data-val="<?=e($val)?>">
    <?=$ico?> <?=e($lbl)?><span class="word-est"><?=e($est)?></span>
  </div>
  <?php endforeach;?>
</div>
<input type="hidden" id="cwType" value="full_post">

<div class="panel"><div class="pnh"><div class="pnt">Content Details</div></div><div class="pnb">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
    <div class="fg"><label class="fl">TARGET KEYWORD</label><input type="text" id="cwKw" class="fc" placeholder="e.g. best link building services 2025" required></div>
    <div class="fg"><label class="fl">WEBSITE/BRAND</label><input type="text" id="cwBrand" class="fc" placeholder="e.g. LinkParty"></div>
    <div class="fg"><label class="fl">TONE</label>
      <select id="cwTone" class="fc"><option value="Professional & authoritative">Professional</option><option value="Friendly & conversational">Friendly</option><option value="Casual & fun">Casual</option><option value="Academic & formal">Academic</option><option value="Persuasive & sales-focused">Sales-focused</option></select>
    </div>
    <div class="fg"><label class="fl">AUDIENCE</label><input type="text" id="cwAud" class="fc" placeholder="e.g. SEO professionals, small business owners"></div>
  </div>
  <div class="fg"><label class="fl">EXTRA CONTEXT (optional)</label><textarea id="cwCtx" class="fc" rows="2" placeholder="Any specific points to include, competitors to mention, or special instructions..."></textarea></div>
  <button class="btn bp" onclick="writeContent()" id="cwBtn">✍️ Write Content →</button>
  <div class="fh">AI writes complete, SEO-optimized content ready to publish</div>
</div></div>

<div id="cwLoading" style="display:none;text-align:center;padding:3rem;color:var(--mist)">
  <div style="font-size:2rem;animation:sp3 .9s linear infinite;display:inline-block;margin-bottom:.7rem">✍️</div>
  <div style="font-size:.72rem" id="cwMsg">Writing your content...</div>
  <div style="font-size:.62rem;margin-top:.4rem;color:rgba(136,136,170,.5)">This may take 15-30 seconds</div>
</div>

<div id="cwResultPanel" style="display:none" class="panel">
  <div class="pnh">
    <div class="pnt" id="cwResultTitle">Generated Content</div>
    <div class="pna">
      <button class="btn bs bsm" onclick="copyContent()">📋 Copy</button>
      <button class="btn bs bsm" onclick="document.getElementById('cwResultPanel').style.display='none'">✕</button>
    </div>
  </div>
  <div class="pnb">
    <div id="cwWordCount" style="font-size:.6rem;color:var(--mist);margin-bottom:.5rem"></div>
    <div class="cw-output" id="cwOutput"></div>
  </div>
</div>

<script>
const API='https://api.anthropic.com/v1/messages';
var cwActiveType='full_post';

function setCType(val){
  document.querySelectorAll('.cwt').forEach(e=>e.classList.remove('active'));
  document.getElementById('cwt_'+val)?.classList.add('active');
  document.getElementById('cwType').value=val;
  cwActiveType=val;
}
setCType('full_post');

var typePrompts={
  full_post:(kw,brand,tone,aud,ctx)=>`Write a complete, SEO-optimized blog post for keyword: "${kw}"
Brand: ${brand||'the website'} | Tone: ${tone} | Audience: ${aud||'general readers'}
${ctx?'Extra context: '+ctx:''}

Include:
- Compelling H1 title (with keyword)
- Meta description (155 chars)
- Introduction with hook (2-3 paragraphs)
- 5-7 H2 sections with content (2-3 paragraphs each)
- H3 sub-sections where relevant
- FAQ section (5 questions)
- Conclusion with CTA
- Use natural keyword variations throughout
- Write 1800-2500 words
- Ready to publish immediately`,

  outline:(kw,brand,tone,aud,ctx)=>`Create a detailed SEO content outline for: "${kw}"
Target audience: ${aud||'general'}
Include: H1 title, all H2/H3 headings with notes on what each covers, word count per section, keyword placement guide, internal linking suggestions, and meta tags.`,

  intro:(kw,brand,tone,aud,ctx)=>`Write an engaging introduction for a blog post about: "${kw}"
Tone: ${tone} | Audience: ${aud||'general'}
Include: hook sentence, context, problem statement, what the article covers, and transition. About 200-250 words.`,

  meta:(kw,brand,tone,aud,ctx)=>`Generate 5 meta tag sets for: "${kw}"
Brand: ${brand||''}
Each set: Title (50-60 chars), Meta description (145-155 chars), H1 suggestion. Mark the best option. Include character counts.`,

  faq:(kw,brand,tone,aud,ctx)=>`Write 10 FAQ items for a page about: "${kw}"
Format each as Q: [question] A: [detailed answer 2-4 sentences]. Include the FAQ schema markup at the end.`,

  conclusion:(kw,brand,tone,aud,ctx)=>`Write a powerful conclusion for a blog post about: "${kw}"
Brand: ${brand||''} | Tone: ${tone}
Include: summary of key points, final insight, strong CTA. About 150-200 words.`,

  product_desc:(kw,brand,tone,aud,ctx)=>`Write SEO-optimized product/service page copy for: "${kw}"
Brand: ${brand||''} | Audience: ${aud||'buyers'}
Include: headline, subheadline, 3 benefit sections, features list, social proof section, FAQ (3 items), and multiple CTAs. About 600-800 words.`,

  landing:(kw,brand,tone,aud,ctx)=>`Write a high-converting landing page for: "${kw}"
Brand: ${brand||''} | Audience: ${aud||''}
Include: hero section, value proposition, 3 benefits, how it works (3 steps), social proof, FAQ, and CTA. Conversion-focused copywriting.`
};

async function writeContent(){
  var kw=document.getElementById('cwKw').value.trim();
  if(!kw)return alert('Enter a target keyword');
  var brand=document.getElementById('cwBrand').value;
  var tone=document.getElementById('cwTone').value;
  var aud=document.getElementById('cwAud').value;
  var ctx=document.getElementById('cwCtx').value;
  var type=document.getElementById('cwType').value;

  document.getElementById('cwBtn').disabled=true;
  document.getElementById('cwLoading').style.display='block';
  document.getElementById('cwResultPanel').style.display='none';

  var msgs={full_post:'Writing full blog post...',outline:'Building content outline...',intro:'Writing introduction...',meta:'Generating meta tags...',faq:'Creating FAQ section...',conclusion:'Writing conclusion...',product_desc:'Writing product page...',landing:'Writing landing page...'};
  document.getElementById('cwMsg').textContent=msgs[type]||'Writing...';

  var prompt=(typePrompts[type]||typePrompts['full_post'])(kw,brand,tone,aud,ctx);

  try{
    var r=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:2000,
        system:'You are an expert SEO content writer. Write high-quality, engaging, publication-ready content. Do not add commentary — just write the content directly.',
        messages:[{role:'user',content:prompt}]})});
    var d=await r.json();
    var text=d.content?.map(b=>b.text||'').join('')||'';
    var wc=text.split(/\s+/).filter(Boolean).length;
    document.getElementById('cwOutput').textContent=text;
    document.getElementById('cwWordCount').textContent='📏 '+wc+' words · ⏱ ~'+Math.ceil(wc/250)+' min read';
    document.getElementById('cwResultTitle').textContent='Generated: '+kw;
    document.getElementById('cwResultPanel').style.display='block';
    document.getElementById('cwResultPanel').scrollIntoView({behavior:'smooth'});
  }catch(e){alert('Error: '+e.message);}
  document.getElementById('cwBtn').disabled=false;
  document.getElementById('cwLoading').style.display='none';
}

function copyContent(){
  var text=document.getElementById('cwOutput').textContent;
  navigator.clipboard.writeText(text).then(()=>{
    var btn=event.target;btn.textContent='✓ Copied!';setTimeout(()=>btn.textContent='📋 Copy',2000);
  });
}
</script>
<?php $pageContent=ob_get_clean();include LB_ROOT.'/app/views/layouts/client_wrap.php';
