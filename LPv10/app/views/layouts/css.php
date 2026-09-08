<?php $ac=setting('accent_color','#b8ff3c');$bg=setting('bg_color','#080810'); ?>
<style>
:root{--ink:<?=e($bg)?>;--acid:<?=e($ac)?>;--paper:#f0ede6;--mist:#8888aa;--card:#0f0f1e;--card2:#141428;--border:rgba(255,255,255,.07);--red:#ff4d6d;--orange:#ff9f43;--blue:#54a0ff;--purple:#a29bfe;--green:#55efc4;--sw:245px}
*{margin:0;padding:0;box-sizing:border-box}html{font-size:14px;scroll-behavior:smooth}
body{background:var(--ink);color:var(--paper);font-family:'DM Mono',monospace;display:flex;min-height:100vh}
.sidebar{width:var(--sw);flex-shrink:0;background:var(--card);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:200;transition:transform .28s}
.sb-brand{padding:.9rem 1.2rem .7rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.5rem}
.sb-logo{font-family:'Syne',sans-serif;font-weight:800;font-size:.88rem;color:var(--paper)}
.sb-plan{font-size:.45rem;padding:.1em .38em;border-radius:3px;font-weight:700;letter-spacing:.05em}
.sb-nav{flex:1;overflow-y:auto;padding:.3rem 0;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
.sb-sec{font-size:.48rem;letter-spacing:.14em;color:var(--mist);padding:.5rem 1rem .15rem;margin-top:.2rem}
.nl{display:flex;align-items:center;gap:.5rem;padding:.45rem 1rem;color:var(--mist);font-size:.68rem;text-decoration:none;border-left:2px solid transparent;transition:all .18s}
.nl:hover{color:var(--paper);background:rgba(255,255,255,.03)}
.nl.active{color:var(--acid);background:rgba(184,255,60,.05);border-left-color:var(--acid)}
.ni{width:14px;text-align:center;font-size:.78rem;flex-shrink:0}
.nb{margin-left:auto;background:var(--red);color:#fff;font-size:.48rem;font-weight:700;padding:.1em .36em;border-radius:8px;min-width:15px;text-align:center}
.sb-foot{padding:.7rem 1rem;border-top:1px solid var(--border)}
.sb-user{display:flex;align-items:center;gap:.45rem}
.sb-av{width:24px;height:24px;border-radius:50%;background:var(--acid);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:800;font-size:.58rem;color:var(--ink);flex-shrink:0}
.sb-nm{font-size:.64rem;font-weight:600;line-height:1.2;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sb-rl{font-size:.5rem;color:var(--mist);letter-spacing:.05em}
.sb-lo{margin-left:auto;color:var(--mist);font-size:.72rem;text-decoration:none;transition:color .18s;flex-shrink:0}
.sb-lo:hover{color:var(--red)}
.main{margin-left:var(--sw);flex:1;min-width:0;display:flex;flex-direction:column}
.topbar{height:46px;display:flex;align-items:center;justify-content:space-between;padding:0 1.3rem;border-bottom:1px solid var(--border);background:rgba(8,8,16,.96);backdrop-filter:blur(12px);position:sticky;top:0;z-index:100;gap:.6rem}
.tb-l{display:flex;align-items:center;gap:.6rem}
.tb-title{font-family:'Syne',sans-serif;font-weight:700;font-size:.78rem}
.tb-r{display:flex;align-items:center;gap:.45rem}
.hbtn{display:none;background:none;border:none;color:var(--paper);cursor:pointer;font-size:.9rem;padding:.2rem}
.pg{padding:1.3rem;display:flex;flex-direction:column;gap:.9rem;flex:1}
.ph{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:.7rem}
.pt{font-family:'Syne',sans-serif;font-weight:800;font-size:1.25rem;letter-spacing:-.02em}
.ps{font-size:.63rem;color:var(--mist);margin-top:.12rem}
.pa{display:flex;gap:.38rem;align-items:center;flex-wrap:wrap}
.btn{padding:.52rem 1rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.68rem;letter-spacing:.03em;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.28rem;text-decoration:none;transition:all .18s;white-space:nowrap;border-radius:3px}
.bp{background:var(--acid);color:var(--ink)}.bp:hover{transform:translateY(-2px);box-shadow:0 5px 18px rgba(184,255,60,.28)}
.bs{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--mist)}.bs:hover{border-color:rgba(255,255,255,.14);color:var(--paper)}
.bd{background:rgba(255,77,109,.1);border:1px solid rgba(255,77,109,.2);color:var(--red)}.bd:hover{background:rgba(255,77,109,.2)}
.bi{background:rgba(84,160,255,.1);border:1px solid rgba(84,160,255,.2);color:var(--blue)}
.bsm{padding:.3rem .65rem;font-size:.6rem}
.bxs{padding:.18rem .48rem;font-size:.56rem}
.sg{display:grid;grid-template-columns:repeat(4,1fr);gap:.8rem}
.sc{background:var(--card);border:1px solid var(--border);padding:.95rem;position:relative;overflow:hidden;border-radius:4px;transition:border-color .22s}
.sc:hover{border-color:rgba(184,255,60,.18)}
.si{position:absolute;top:.75rem;right:.8rem;font-size:1.1rem;opacity:.12}
.sl{font-size:.52rem;letter-spacing:.1em;color:var(--mist);margin-bottom:.4rem}
.sv{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;line-height:1;margin-bottom:.15rem}
.ssb{font-size:.54rem;color:var(--mist)}
.sup{color:var(--green)}.sdn{color:var(--red)}
.panel{background:var(--card);border:1px solid var(--border);border-radius:4px;overflow:hidden}
.pnh{display:flex;align-items:center;justify-content:space-between;padding:.7rem .95rem;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:.45rem}
.pnt{font-family:'Syne',sans-serif;font-weight:700;font-size:.74rem}
.pna{display:flex;gap:.32rem;align-items:center;flex-wrap:wrap}
.pnb{padding:.95rem}
.tw{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:380px}
thead th{font-size:.51rem;letter-spacing:.09em;color:var(--mist);padding:.42rem .85rem;text-align:left;background:rgba(255,255,255,.02);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:.56rem .85rem;border-bottom:1px solid rgba(255,255,255,.03);font-size:.67rem;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:rgba(255,255,255,.015)}
.tdp{font-family:'Syne',sans-serif;font-weight:600;font-size:.7rem}
.tdm{font-size:.56rem;color:var(--mist);margin-top:2px}
.tdc{max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tda{display:flex;gap:.25rem;align-items:center;flex-wrap:wrap}
.bdg{display:inline-block;padding:.12em .42em;font-size:.5rem;font-weight:700;letter-spacing:.06em;border-radius:3px;white-space:nowrap}
.b-success{background:rgba(85,239,196,.12);color:#55efc4}.b-warn{background:rgba(255,159,67,.12);color:var(--orange)}.b-info{background:rgba(84,160,255,.12);color:var(--blue)}.b-purple{background:rgba(162,155,254,.12);color:var(--purple)}.b-danger{background:rgba(255,77,109,.12);color:var(--red)}.b-muted{background:rgba(136,136,170,.12);color:var(--mist)}
.ftabs{display:flex;gap:.25rem;flex-wrap:wrap}
.ftab{padding:.26rem .62rem;font-size:.6rem;font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.04em;background:none;border:1px solid var(--border);color:var(--mist);cursor:pointer;text-decoration:none;transition:all .18s;border-radius:3px}
.ftab:hover,.ftab.active{border-color:var(--acid);color:var(--acid);background:rgba(184,255,60,.05)}
.fg{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.fgr{display:flex;flex-direction:column;gap:.26rem;margin-bottom:.75rem}
.fl{font-size:.54rem;letter-spacing:.09em;color:var(--mist)}
.fc{background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.58rem .82rem;font-family:'DM Mono',monospace;font-size:.73rem;width:100%;outline:none;transition:border-color .2s;border-radius:3px;resize:vertical}
.fc:focus{border-color:rgba(184,255,60,.4)}.fc::placeholder{color:rgba(136,136,170,.35)}.fc option{background:var(--card)}
.fh{font-size:.53rem;color:var(--mist);margin-top:.1rem;line-height:1.5}
.fsec{font-size:.52rem;letter-spacing:.12em;color:var(--acid);margin:.85rem 0 .55rem;display:flex;align-items:center;gap:.38rem}
.fsec::after{content:'';flex:1;height:1px;background:rgba(184,255,60,.1)}
.al{padding:.65rem .88rem;border:1px solid;font-size:.68rem;margin-bottom:.82rem;line-height:1.6;border-radius:3px}
.al-e{border-color:rgba(255,77,109,.3);background:rgba(255,77,109,.06);color:var(--red)}
.al-s{border-color:rgba(85,239,196,.3);background:rgba(85,239,196,.06);color:var(--green)}
.al-i{border-color:rgba(84,160,255,.3);background:rgba(84,160,255,.06);color:var(--blue)}
.mo{display:none;position:fixed;inset:0;background:rgba(0,0,0,.78);z-index:500;align-items:center;justify-content:center;padding:1rem}
.mo.open{display:flex}
.mw{background:var(--card);border:1px solid var(--border);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;border-radius:4px;animation:mIn .22s ease}
.mwl{max-width:700px}
@keyframes mIn{from{transform:translateY(14px);opacity:0}to{}}
.mh{display:flex;align-items:center;justify-content:space-between;padding:.82rem 1rem;border-bottom:1px solid var(--border)}
.mt{font-family:'Syne',sans-serif;font-weight:700;font-size:.84rem}
.mc{background:none;border:none;color:var(--mist);cursor:pointer;font-size:.88rem;padding:.2rem;transition:color .18s}
.mc:hover{color:var(--paper)}
.mb{padding:1rem}
.mf{padding:.78rem 1rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:.38rem}
#sb-ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:199}
.pb{height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;margin-top:.28rem}
.pf{height:100%;border-radius:2px;transition:width .4s}
@media(max-width:1100px){.sg{grid-template-columns:repeat(2,1fr)}.fg{grid-template-columns:1fr}}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.main{margin-left:0}.hbtn{display:block}#sb-ov.open{display:block}}
@media(max-width:580px){.sg{grid-template-columns:1fr}.pg{padding:.85rem}.ph{flex-direction:column;align-items:flex-start}}
</style>
<script>
function oM(id){document.getElementById(id).classList.add('open')}
function cM(id){document.getElementById(id).classList.remove('open')}
function oSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sb-ov').classList.add('open')}
function cSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sb-ov').classList.remove('open')}
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.querySelectorAll('.mo.open').forEach(m=>m.classList.remove('open'))})
</script>
