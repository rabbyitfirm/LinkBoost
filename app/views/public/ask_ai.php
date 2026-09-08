<?php ob_start();?>
<style>
.chat-area{max-width:760px;margin:0 auto;padding:2rem 1.5rem}
.msgs{display:flex;flex-direction:column;gap:.75rem;min-height:200px;margin-bottom:1rem}
.msg{display:flex;gap:.7rem;align-items:flex-start}
.msg.user{flex-direction:row-reverse}
.av2{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;flex-shrink:0}
.av2.ai{background:var(--acid);color:var(--ink)}
.av2.us{background:rgba(255,255,255,.08)}
.bubble{background:var(--card);border:1px solid var(--border);padding:.75rem .95rem;font-size:.73rem;line-height:1.75;max-width:88%;border-radius:4px;white-space:pre-wrap;word-wrap:break-word}
.bubble.user{background:rgba(184,255,60,.06);border-color:rgba(184,255,60,.18)}
.inp-row{display:flex;gap:.5rem}
.inp-row textarea{flex:1;background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.7rem .9rem;font-family:DM Mono,monospace;font-size:.76rem;outline:none;resize:none;border-radius:3px;transition:border-color .2s}
.inp-row textarea:focus{border-color:rgba(184,255,60,.4)}
.spin2{animation:spin3 .9s linear infinite;display:inline-block}
@keyframes spin3{to{transform:rotate(360deg)}}
</style>
<div style="padding:3rem 0 1rem;text-align:center;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)">
<div class="c"><h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.5rem,3.5vw,2.2rem);margin-bottom:.4rem">Ask <span style="color:var(--acid)">AI</span></h1>
<p style="font-size:.72rem;color:var(--mist)">Get instant SEO advice, content ideas, and strategy from Claude AI</p></div></div>
<div class="c chat-area">
<div class="panel" style="margin-bottom:1rem">
  <div class="msgs" id="msgs">
    <div class="msg"><div class="av2 ai">✨</div><div class="bubble">Hi! I'm your SEO AI assistant. Ask me anything about SEO, link building, keyword research, content strategy, or any digital marketing topic. I'm here to help! 🚀</div></div>
  </div>
  <div style="padding:.8rem;border-top:1px solid var(--border)">
    <div class="inp-row">
      <textarea id="qi" rows="2" placeholder="Ask anything about SEO... (Enter to send, Shift+Enter for newline)"></textarea>
      <button onclick="sendQ()" id="sb3" class="btn bp" style="flex-shrink:0;padding:.7rem 1.2rem;align-self:flex-end">Send →</button>
    </div>
  </div>
</div>
<div style="display:flex;flex-wrap:wrap;gap:.38rem;margin-bottom:1.5rem">
  <?php foreach(['Best anchor text ratios for 2025','How to recover from a Google penalty','What makes a good guest post site?','Explain Domain Authority vs Domain Rating','How many backlinks do I need to rank #1?','What is link velocity and why does it matter?'] as $q):?>
  <button onclick="document.getElementById(\'qi\').value=<?=json_encode($q)?>;sendQ()" class="btn bs bsm"><?=e($q)?></button>
  <?php endforeach;?>
</div>
</div>
<script>
var hist=[{role:'assistant',content:'Hi! I\'m your SEO AI assistant. Ask me anything about SEO, link building, keyword research, content strategy, or any digital marketing topic!'}];
async function sendQ(){
  var q=document.getElementById('qi').value.trim();if(!q)return;
  addMsg('user',q);hist.push({role:'user',content:q});document.getElementById('qi').value='';
  var btn=document.getElementById('sb3');btn.disabled=true;btn.textContent='...';
  var thinkId='t'+Date.now();addMsg('ai','<span class="spin2">⚙</span> Thinking...',thinkId);
  try{
    var r=await fetch('https://api.anthropic.com/v1/messages',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:600,system:'You are an expert SEO consultant specializing in link building, technical SEO, and content strategy. Give concise, actionable advice.',messages:hist})});
    var d=await r.json();var ans=d.content?.map(b=>b.text||'').join('')||'Sorry, I could not respond.';
    var el=document.getElementById(thinkId);if(el){el.querySelector('.bubble').textContent=ans;}
    hist.push({role:'assistant',content:ans});
  }catch(e){var el=document.getElementById(thinkId);if(el)el.querySelector('.bubble').textContent='Error: '+e.message;}
  btn.disabled=false;btn.textContent='Send →';
}
function addMsg(who,txt,id){
  var d=document.getElementById('msgs');
  var m=document.createElement('div');m.className='msg'+(who==='user'?' user':'');
  if(id)m.id=id;
  m.innerHTML='<div class="av2 '+(who==='user'?'us':'ai')+'">'+(who==='user'?'👤':'✨')+'</div><div class="bubble'+(who==='user'?' user':'')+'">'+(who==='user'?txt.replace(/</g,'&lt;'):txt)+'</div>';
  d.appendChild(m);d.scrollTop=d.scrollHeight;
}
document.getElementById('qi').addEventListener('keydown',function(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendQ();}});
</script>
<?php $pageContent=ob_get_clean();$pageTitle='Ask AI — '.appName();include LB_ROOT.'/app/views/public/layout.php';
