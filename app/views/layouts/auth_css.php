<?php $ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810');?>
<style>
:root{--ink:<?=e($bg)?>;--acid:<?=e($ac)?>;--paper:#f0ede6;--mist:#8888aa;--card:#0f0f1e;--border:rgba(255,255,255,.07);--red:#ff4d6d;--green:#55efc4}
*{margin:0;padding:0;box-sizing:border-box}html{font-size:15px}
body{background:var(--ink);color:var(--paper);font-family:'DM Mono',monospace;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;position:relative}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 80% 50% at 50% 0%,rgba(184,255,60,.07),transparent 65%);pointer-events:none}
.aw{width:100%;max-width:430px;position:relative;z-index:1}
.ab{display:flex;align-items:center;gap:.5rem;margin-bottom:1.8rem;text-decoration:none}
.al2{font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:var(--paper)}
.al2 em{background:var(--acid);color:var(--ink);padding:.1em .38em;font-size:.5rem;border-radius:3px;font-style:normal;font-weight:700;letter-spacing:.05em}
.ac{background:var(--card);border:1px solid var(--border);padding:2rem;border-radius:4px}
.at{font-family:'Syne',sans-serif;font-weight:800;font-size:1.25rem;margin-bottom:.26rem}
.as{font-size:.72rem;color:var(--mist);line-height:1.7;margin-bottom:1.4rem}
.fg{display:flex;flex-direction:column;gap:.26rem;margin-bottom:.82rem}
.fl{font-size:.56rem;letter-spacing:.09em;color:var(--mist)}
.fc{background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.7rem .88rem;font-family:'DM Mono',monospace;font-size:.8rem;width:100%;outline:none;transition:border-color .2s;border-radius:3px}
.fc:focus{border-color:rgba(184,255,60,.45)}.fc::placeholder{color:rgba(136,136,170,.35)}
.sb2{padding:.82rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem;letter-spacing:.04em;border:none;cursor:pointer;width:100%;background:var(--acid);color:var(--ink);transition:all .18s;margin-top:.38rem;border-radius:3px}
.sb2:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(184,255,60,.3)}
.alinks{display:flex;justify-content:space-between;margin-top:1.1rem;font-size:.64rem;color:var(--mist)}
.alinks a{color:var(--mist);text-decoration:none;transition:color .2s}.alinks a:hover{color:var(--acid)}
.al{padding:.7rem .88rem;border:1px solid;font-size:.7rem;margin-bottom:.95rem;line-height:1.6;border-radius:3px}
.al-e{border-color:rgba(255,77,109,.3);background:rgba(255,77,109,.06);color:var(--red)}
.al-s{border-color:rgba(85,239,196,.3);background:rgba(85,239,196,.06);color:var(--green)}
.gbtn{display:flex;align-items:center;justify-content:center;gap:.62rem;width:100%;padding:.8rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:var(--paper);font-family:'Syne',sans-serif;font-weight:600;font-size:.76rem;text-decoration:none;transition:all .2s;border-radius:3px;cursor:pointer;margin-bottom:.6rem}
.gbtn:hover{background:rgba(255,255,255,.09);border-color:rgba(255,255,255,.22);transform:translateY(-1px)}
.div{display:flex;align-items:center;gap:.7rem;margin:.85rem 0;font-size:.6rem;color:var(--mist)}
.div::before,.div::after{content:'';flex:1;height:1px;background:var(--border)}
.pwb{height:3px;background:var(--border);border-radius:2px;margin-top:.32rem;overflow:hidden}
.pwf{height:100%;transition:width .3s,background .3s;border-radius:2px;width:0}
</style>
<script>
function checkPsw(v){var f=document.getElementById('pf');if(!f)return;var s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;var w=['0%','25%','55%','78%','100%'][s],c=[null,'#ff4d6d','#ff9f43','#55efc4','#b8ff3c'][s];f.style.width=w;f.style.background=c;}
</script>