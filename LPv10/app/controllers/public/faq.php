<?php
$faqs=[
  'General'=>[['What is LinkParty?','LinkParty is a premium SEO and backlink marketplace platform. We connect businesses with real publisher websites for guest posts, niche edits, and homepage links — combined with powerful AI SEO tools.'],['How is this different from a PBN?','Every link we place is on a real, active website with organic traffic. No PBNs, no link farms. We do manual outreach to real publishers in our network of 12,000+ sites.'],['Do you offer refunds?','Yes — if we cannot place your link on a site matching the agreed specifications, we will offer a full refund or a replacement at no extra cost.']],
  'Orders'=>[['How long does delivery take?','Most guest posts are delivered within 5–10 business days. Niche edits typically take 3–5 days. Turnaround times are shown on each service.'],['Can I specify which websites to use?','You can request specific niches and DA/DR minimums. We will match you with the best available sites in our network.'],['Will I get a live URL report?','Yes. Every completed order includes the live URL of the placed link along with a delivery report.'],['Can I cancel an order?','You can cancel before the link has been placed. Contact support immediately after ordering if you need to cancel.']],
  'SEO Tools'=>[['Are the SEO tools really free?','Yes — all 22 SEO tools are free for registered users. AI-powered tools use Claude AI and are included on all plans including Free.'],['How accurate is the rank tracker?','Rankings are checked and updated weekly. For real-time data, we recommend connecting a SERP API in the admin settings.'],['What does the AI Autopilot do?','The AI Autopilot analyzes your entire domain in one click — SEO score, keyword opportunities, competitor gaps, content strategy, and a full 30-day action plan.']],
  'Billing'=>[['What payment methods do you accept?','We accept credit/debit cards via Stripe, PayPal, and bank transfer (manual). Contact support to arrange payment.'],['Can I upgrade or downgrade my plan?','Yes — upgrades take effect immediately. Contact support to downgrade.'],['Is there a free plan?','Yes — the Free plan includes 1 project, 10 keywords, 1 audit, 3 orders/month, and all SEO tools.']],
];
ob_start();?>
<style>
.faq-section{margin-bottom:2.5rem}
.faq-item{border-bottom:1px solid var(--border)}
.faq-q{width:100%;background:none;border:none;color:var(--paper);text-align:left;padding:.9rem 0;font-size:.78rem;font-family:'Syne',sans-serif;font-weight:700;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:.5rem;transition:color .18s}
.faq-q:hover{color:var(--acid)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease;font-size:.72rem;color:var(--mist);line-height:1.85}
.faq-a.open{max-height:200px}
.faq-a-inner{padding-bottom:.9rem}
.faq-ico{font-size:.8rem;transition:transform .25s;flex-shrink:0}
.faq-ico.open{transform:rotate(45deg)}
</style>
<div style="padding:4rem 0 2rem;text-align:center;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.07),transparent)">
<div class="c"><h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Frequently Asked <span style="color:var(--acid)">Questions</span></h1>
<p style="font-size:.72rem;color:var(--mist)">Everything you need to know about LinkParty</p></div></div>
<div class="c" style="max-width:760px;padding:3rem 1.5rem">
<?php foreach($faqs as $sec=>$items):?>
<div class="faq-section">
  <div style="font-size:.55rem;letter-spacing:.12em;font-weight:700;color:var(--acid);text-transform:uppercase;margin-bottom:.8rem;display:flex;align-items:center;gap:.5rem"><?=e($sec)?><div style="flex:1;height:1px;background:rgba(184,255,60,.15)"></div></div>
  <?php foreach($items as $i=>[$q,$a]):$id='faq'.preg_replace('/\W/','',strtolower($sec)).$i;?>
  <div class="faq-item">
    <button class="faq-q" onclick="tog('<?=$id?>')">
      <?=e($q)?>
      <span class="faq-ico" id="ico<?=$id?>">+</span>
    </button>
    <div class="faq-a" id="<?=$id?>"><div class="faq-a-inner"><?=e($a)?></div></div>
  </div>
  <?php endforeach;?>
</div>
<?php endforeach;?>
<div style="text-align:center;margin-top:2.5rem;padding:2rem;background:var(--card);border:1px solid var(--border);border-radius:4px">
  <div style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.4rem">Still have questions?</div>
  <div style="font-size:.68rem;color:var(--mist);margin-bottom:1rem">Our team replies within 24 hours</div>
  <a href="<?=e(appUrl())?>/contact" class="btn bp">Contact Us →</a>
</div>
</div>
<script>function tog(id){var a=document.getElementById(id),i=document.getElementById('ico'+id);var open=a.classList.toggle('open');i.textContent=open?'×':'+';i.classList.toggle('open',open);}</script>
<?php $pageContent=ob_get_clean();$pageTitle='FAQ — '.appName();include LB_ROOT.'/app/views/public/layout.php';
