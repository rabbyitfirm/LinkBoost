<?php ob_start();?>
<style>
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;padding:4rem 0;align-items:start}
.info-item{display:flex;gap:.8rem;align-items:flex-start;margin-bottom:1.2rem}
.ii{width:36px;height:36px;background:rgba(184,255,60,.08);border:1px solid rgba(184,255,60,.18);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0}
@media(max-width:700px){.contact-grid{grid-template-columns:1fr}}
</style>
<div style="padding:4rem 0 1rem;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(184,255,60,.06),transparent)">
<div class="c"><h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;margin-bottom:.4rem">Get in <span style="color:var(--acid)">Touch</span></h1>
<p style="font-size:.72rem;color:var(--mist)">We reply to all inquiries within 24 hours</p></div></div>
<div class="c"><div class="contact-grid">
<div>
  <?php if($error??''):?><div style="background:rgba(255,77,109,.06);border:1px solid rgba(255,77,109,.3);padding:.8rem;color:var(--red);font-size:.7rem;border-radius:3px;margin-bottom:1rem">✗ <?=e($error)?></div><?php endif;?>
  <?php if($success??''):?><div style="background:rgba(85,239,196,.06);border:1px solid rgba(85,239,196,.3);padding:.8rem;color:var(--green);font-size:.7rem;border-radius:3px;margin-bottom:1rem">✓ <?=e($success)?></div><?php endif;?>
  <div style="background:var(--card);border:1px solid var(--border);padding:1.6rem;border-radius:4px">
    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.9rem;margin-bottom:1.2rem">Send a Message</div>
    <form method="post"><input type="hidden" name="_token" value="<?=e(csrf())?>">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem">
        <div style="display:flex;flex-direction:column;gap:.26rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">NAME</label><input type="text" name="name" style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px" value="<?=e($form['name']??'')?>" required></div>
        <div style="display:flex;flex-direction:column;gap:.26rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">EMAIL</label><input type="email" name="email" style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px" value="<?=e($form['email']??'')?>" required></div>
      </div>
      <div style="margin-top:.7rem;display:flex;flex-direction:column;gap:.26rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">SUBJECT</label><input type="text" name="subject" style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px" value="<?=e($form['subject']??'')?>" placeholder="What's this about?"></div>
      <div style="margin-top:.7rem;display:flex;flex-direction:column;gap:.26rem"><label style="font-size:.54rem;letter-spacing:.09em;color:var(--mist)">MESSAGE</label><textarea name="message" rows="5" style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--paper);padding:.68rem .88rem;font-family:DM Mono,monospace;font-size:.76rem;width:100%;outline:none;border-radius:3px;resize:vertical" required><?=e($form['message']??'')?></textarea></div>
      <button type="submit" style="margin-top:.9rem;padding:.82rem;font-family:Syne,sans-serif;font-weight:700;font-size:.8rem;border:none;cursor:pointer;width:100%;background:var(--acid);color:var(--ink);border-radius:3px;transition:all .18s">Send Message →</button>
    </form>
  </div>
</div>
<div>
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;margin-bottom:1.5rem">How Can We Help?</div>
  <?php foreach([['📋','Order Support','Questions about your link orders and delivery'],['💎','Plan Upgrades','Upgrade your plan or enterprise pricing'],['🤝','Partnerships','Become a publisher or affiliate partner'],['🔧','Technical Help','Issues with the platform or tools']] as [$ico,$t,$d]):?>
  <div class="info-item"><div class="ii"><?=$ico?></div><div><div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem;margin-bottom:.2rem"><?=e($t)?></div><div style="font-size:.65rem;color:var(--mist);line-height:1.6"><?=e($d)?></div></div></div>
  <?php endforeach;?>
  <?php $email=setting('contact_email','admin@linkparty.net');if($email):?>
  <div style="margin-top:1.5rem;padding:1rem;background:var(--card);border:1px solid var(--border);border-radius:4px;font-size:.68rem">
    <div style="color:var(--mist);margin-bottom:.3rem">Email us directly</div>
    <a href="mailto:<?=e($email)?>" style="color:var(--acid)"><?=e($email)?></a>
  </div>
  <?php endif;?>
</div>
</div></div>
<?php $pageContent=ob_get_clean();$pageTitle='Contact — '.appName();include LB_ROOT.'/app/views/public/layout.php';
