<?php // Called inside ob_start from seo_tools controller ?>
<style>
.tool-tabs{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.38rem;margin-bottom:.9rem}
.tt{padding:.5rem .7rem;font-size:.62rem;font-family:'Syne',sans-serif;font-weight:600;background:var(--card);border:1px solid var(--border);color:var(--mist);cursor:pointer;text-align:left;transition:all .18s;border-radius:3px;text-decoration:none;display:block}
.tt:hover,.tt.active{border-color:var(--acid);color:var(--acid);background:rgba(184,255,60,.04)}
.tt span{display:block;font-size:.85rem;margin-bottom:.15rem}
.tool-area{background:var(--card);border:1px solid var(--border);border-radius:4px;padding:1rem}
.tool-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:.2rem}
.tool-desc{font-size:.65rem;color:var(--mist);margin-bottom:.9rem}
.out{background:rgba(255,255,255,.03);border:1px solid var(--border);padding:.9rem;font-size:.72rem;line-height:1.8;min-height:100px;white-space:pre-wrap;word-wrap:break-word;border-radius:3px;margin-top:.85rem}
.spin{display:inline-block;animation:spin2 .9s linear infinite}
@keyframes spin2{to{transform:rotate(360deg)}}
</style>

<div class="ph"><div><div class="pt">SEO Tools</div><div class="ps">22 AI-powered SEO tools</div></div></div>

<?php
$tools=[
  ['keyword','🔍','Keyword Research','Find keywords & difficulty'],
  ['density','📊','Keyword Density','Analyze keyword usage'],
  ['lsi','🧩','LSI Keywords','Get semantically related terms'],
  ['longtail','🎯','Long-Tail Keywords','Discover long-tail opportunities'],
  ['meta','🏷','Meta Generator','Title + description with SERP preview'],
  ['title_gen','✍️','Title Generator','SEO-optimized title ideas'],
  ['schema','🏗','Schema Generator','JSON-LD structured data'],
  ['readability','📖','Readability Score','Check content readability'],
  ['paraphrase','♻️','AI Paraphraser','Rewrite content uniquely'],
  ['seo_brief','📋','SEO Brief','Full content brief generation'],
  ['outline','📝','Article Outline','Structured article outline'],
  ['anchor','🔗','Anchor Text Advisor','Best anchor text strategy'],
  ['link_value','💎','Link Value Estimator','Estimate backlink value'],
  ['outreach','📧','Outreach Email','Write link outreach emails'],
  ['url_slug','🔤','URL Slug Generator','SEO-friendly URL slugs'],
  ['robots','🤖','Robots.txt Builder','Generate robots.txt'],
  ['sitemap','🗺','Sitemap Generator','XML sitemap template'],
  ['utm','🔗','UTM Builder','Campaign tracking URLs'],
  ['word_count','📏','Word Counter','Count words and characters'],
  ['case','Aa','Case Converter','Convert text case'],
  ['url_encode','🔒','URL Encoder','Encode/decode URLs'],
  ['redirects','↪️','Redirect Checker','Check redirect chains'],
];
?>

<div class="tool-tabs">
<?php foreach($tools as [$key,$ico,$name,$desc]):?>
<a href="?tool=<?=e($key)?>" class="tt <?=$activeTool===$key?'active':''?>"><span><?=$ico?></span><?=e($name)?></a>
<?php endforeach;?>
</div>

<div class="tool-area">
<?php
$toolDefs=[
  'keyword'=>['Keyword Research','Enter a seed keyword to get related keywords with difficulty estimates, search volume ranges, CPC data, and strategic recommendations.',
    '<div class="fg"><label class="fl">SEED KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. buy backlinks" required></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">COUNTRY</label><select id="t2" class="fc"><option>US</option><option>GB</option><option>AU</option><option>CA</option><option>IN</option></select></div><div class="fg"><label class="fl">INTENT</label><select id="t3" class="fc"><option>All</option><option>Informational</option><option>Commercial</option><option>Transactional</option></select></div></div>',
    'Analyze the keyword "{v}" for country "{v2}" with intent "{v3}". Provide: 1) Difficulty score (0-100) with explanation 2) Estimated monthly search volume range 3) CPC estimate 4) 10 related keyword suggestions with difficulty/volume 5) Top 3 content angles. Format clearly with sections.'],
  'density'=>['Keyword Density Analyzer','Paste your content and target keyword to analyze density and get optimization recommendations.',
    '<div class="fg"><label class="fl">CONTENT</label><textarea id="tin" class="fc" rows="5" placeholder="Paste your article content here..." required></textarea></div><div class="fg"><label class="fl">TARGET KEYWORD</label><input type="text" id="t2" class="fc" placeholder="target keyword" required></div>',
    'Analyze keyword density for keyword "{v2}" in this content:\n\n{v}\n\nProvide: 1) Overall density % 2) Count of exact matches 3) Density rating (over/under/optimal) 4) LSI keywords used 5) Specific optimization suggestions.'],
  'lsi'=>['LSI Keywords','Get semantically related keywords to enhance your content\'s topical authority.',
    '<div class="fg"><label class="fl">MAIN KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. link building strategies" required></div>',
    'Generate 25 LSI (Latent Semantic Indexing) keywords for "{v}". Group them into: 1) Core synonyms 2) Related concepts 3) Question-based terms 4) Action-oriented terms. For each, note relevance level and where to naturally include it.'],
  'longtail'=>['Long-Tail Keyword Generator','Discover low-competition long-tail keywords with high buying intent.',
    '<div class="fg"><label class="fl">SEED KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. backlink building" required></div><div class="fg"><label class="fl">NICHE/INDUSTRY</label><input type="text" id="t2" class="fc" placeholder="e.g. SEO / Digital Marketing"></div>',
    'Generate 20 long-tail keyword variations for "{v}" in the {v2} niche. Focus on: 1) Question keywords (who/what/how/why/best) 2) Location-based variations 3) Comparison keywords 4) Buying-intent keywords. Include estimated competition level (Low/Medium/High) for each.'],
  'meta'=>['Meta Tag Generator','Generate optimized meta title and description with real-time SERP preview.',
    '<div class="fg"><label class="fl">PAGE TOPIC / KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. buy guest posts for SEO" required></div><div class="fg"><label class="fl">BRAND NAME</label><input type="text" id="t2" class="fc" placeholder="LinkParty"></div>',
    'Create 3 variations of SEO meta tags for "{v}" for brand "{v2}". For each provide: Title (50-60 chars), Meta description (145-155 chars), Character counts, and a brief explanation of the strategy used. Mark the best option.'],
  'title_gen'=>['SEO Title Generator','Generate click-worthy, SEO-optimized title options for your content.',
    '<div class="fg"><label class="fl">TOPIC / KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. link building for beginners" required></div><div class="fg"><label class="fl">CONTENT TYPE</label><select id="t2" class="fc"><option>Blog Post</option><option>Guide</option><option>List Article</option><option>Case Study</option><option>How-To</option><option>Review</option></select></div>',
    'Generate 10 SEO-optimized titles for a {v2} about "{v}". Mix these formulas: numbers, how-to, questions, power words, curiosity gaps. Include character count for each. Star (★) your top 3 picks.'],
  'schema'=>['Schema Markup Generator','Generate JSON-LD structured data markup for better rich snippets.',
    '<div class="fg"><label class="fl">PAGE URL</label><input type="url" id="tin" class="fc" placeholder="https://example.com/page" required></div><div class="fg"><label class="fl">SCHEMA TYPE</label><select id="t2" class="fc"><option>Article</option><option>LocalBusiness</option><option>Product</option><option>FAQPage</option><option>HowTo</option><option>Review</option><option>Service</option><option>Organization</option></select></div><div class="fg"><label class="fl">DETAILS</label><textarea id="t3" class="fc" rows="2" placeholder="Brief description of the page/business/product..."></textarea></div>',
    'Generate complete {v2} JSON-LD schema markup for URL: {v}\n\nDetails: {v3}\n\nProvide valid, complete JSON-LD schema with all recommended properties. Then explain what rich snippets this will enable.'],
  'readability'=>['Readability Score Checker','Analyze your content readability and get improvement suggestions.',
    '<div class="fg"><label class="fl">CONTENT TO ANALYZE</label><textarea id="tin" class="fc" rows="7" placeholder="Paste your article content here..." required></textarea></div>',
    'Analyze the readability of this content:\n\n{v}\n\nProvide: 1) Flesch Reading Ease score estimate (0-100) 2) Flesch-Kincaid Grade Level 3) Average sentence length 4) Average word length 5) Passive voice usage 6) Complex word percentage 7) Specific improvement suggestions for SEO readability.'],
  'paraphrase'=>['AI Content Paraphraser','Rewrite content uniquely while preserving meaning and SEO value.',
    '<div class="fg"><label class="fl">CONTENT TO PARAPHRASE</label><textarea id="tin" class="fc" rows="5" placeholder="Paste text to paraphrase..." required></textarea></div><div class="fg"><label class="fl">TONE</label><select id="t2" class="fc"><option>Professional</option><option>Casual/Friendly</option><option>Academic</option><option>Simple/Clear</option><option>Persuasive</option></select></div>',
    'Paraphrase this content in a {v2} tone, making it unique while preserving all key information and SEO keywords:\n\n{v}\n\nProvide the rewritten version followed by a brief note on changes made.'],
  'seo_brief'=>['SEO Content Brief Generator','Generate a comprehensive content brief for any target keyword.',
    '<div class="fg"><label class="fl">TARGET KEYWORD</label><input type="text" id="tin" class="fc" placeholder="e.g. best link building services 2025" required></div><div class="fg"><label class="fl">INDUSTRY/NICHE</label><input type="text" id="t2" class="fc" placeholder="SEO / Digital Marketing"></div>',
    'Create a comprehensive SEO content brief for the keyword "{v}" in the {v2} industry. Include: 1) Target audience 2) Search intent 3) Recommended word count 4) Suggested H1 title 5) H2/H3 structure with sub-topics 6) Must-include LSI keywords 7) Key points to cover 8) Call-to-action suggestions 9) Internal/external link suggestions 10) Competitors to outrank.'],
  'outline'=>['Article Outline Generator','Generate a detailed, SEO-optimized article outline.',
    '<div class="fg"><label class="fl">ARTICLE TOPIC</label><input type="text" id="tin" class="fc" placeholder="e.g. Complete Guide to Link Building" required></div><div class="fg"><label class="fl">WORD COUNT TARGET</label><select id="t2" class="fc"><option>1000-1500</option><option>1500-2500</option><option>2500-4000</option><option>4000+</option></select></div>',
    'Create a detailed article outline for "{v}" targeting {v2} words. Include: H1, H2, H3 headings with brief notes on what each section should cover, suggested word count per section, and keyword placement suggestions.'],
  'anchor'=>['Anchor Text Advisor','Get the best anchor text strategy for your link building campaign.',
    '<div class="fg"><label class="fl">TARGET URL</label><input type="url" id="tin" class="fc" placeholder="https://yoursite.com/page" required></div><div class="fg"><label class="fl">TARGET KEYWORD</label><input type="text" id="t2" class="fc" placeholder="Main keyword you want to rank for" required></div>',
    'Provide an anchor text strategy for URL: {v}\nTarget keyword: {v2}\n\nInclude: 1) Recommended anchor text distribution (% exact, partial, branded, naked, generic) 2) 10 specific anchor text examples with types 3) Over-optimization risks to avoid 4) Natural variation suggestions.'],
  'link_value'=>['Link Value Estimator','Estimate the SEO value of a potential backlink opportunity.',
    '<div class="fg"><label class="fl">LINKING DOMAIN</label><input type="text" id="tin" class="fc" placeholder="e.g. techcrunch.com" required></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">DOMAIN AUTHORITY (DA)</label><input type="number" id="t2" class="fc" min="0" max="100" placeholder="0-100"></div><div class="fg"><label class="fl">NICHE RELEVANCE</label><select id="t3" class="fc"><option>Same niche</option><option>Related niche</option><option>General/any</option><option>Unrelated</option></select></div></div>',
    'Estimate the SEO value of a backlink from {v} (DA: {v2}, relevance: {v3}). Provide: 1) Overall value score (1-10) 2) Trust factors 3) Link equity estimate 4) Relevance score 5) Recommended anchor types 6) Whether to pursue this link 7) What type of content would earn a natural link.'],
  'outreach'=>['Link Outreach Email Writer','Write personalized link building outreach emails that get responses.',
    '<div class="fg"><label class="fl">TARGET WEBSITE</label><input type="text" id="tin" class="fc" placeholder="techblog.com" required></div><div class="fg"><label class="fl">YOUR WEBSITE/BRAND</label><input type="text" id="t2" class="fc" placeholder="yoursite.com" required></div><div class="fg"><label class="fl">OUTREACH TYPE</label><select id="t3" class="fc"><option>Guest Post Pitch</option><option>Broken Link Building</option><option>Resource Page Link</option><option>Link Reclamation</option><option>Skyscraper Method</option></select></div>',
    'Write a {v3} outreach email for getting a backlink from {v} for the website {v2}. Include: Subject line (3 variations), Email body (personalized, under 150 words), Clear value proposition, Professional sign-off. Make it genuine, not spammy.'],
  'url_slug'=>['URL Slug Generator','Convert any title into SEO-friendly URL slugs.',
    '<div class="fg"><label class="fl">PAGE TITLE</label><input type="text" id="tin" class="fc" placeholder="e.g. 10 Best Link Building Strategies for 2025" required></div>',
    'Convert this title into 5 SEO-friendly URL slug variations: "{v}"\n\nFor each slug: 1) The clean slug (lowercase, hyphens, no stop words) 2) Character count 3) Brief note on why it\'s good. Also note any stop words removed and why.'],
  'robots'=>['Robots.txt Builder','Generate a proper robots.txt file for your website.',
    '<div class="fg"><label class="fl">DOMAIN</label><input type="url" id="tin" class="fc" placeholder="https://example.com" required></div><div class="fg"><label class="fl">SITE TYPE</label><select id="t2" class="fc"><option>Business/Corporate</option><option>E-commerce</option><option>Blog/Content</option><option>SaaS/App</option><option>Agency</option></select></div><div class="fg"><label class="fl">PAGES TO BLOCK</label><input type="text" id="t3" class="fc" placeholder="e.g. /admin, /cart, /login (comma separated)"></div>',
    'Generate a complete robots.txt file for {v} ({v2} website). Block these paths: {v3}. Include: All major search engine bots, crawl delay settings, disallow rules for sensitive paths, sitemap declaration, and explanatory comments.'],
  'sitemap'=>['XML Sitemap Generator','Create an XML sitemap template for your website.',
    '<div class="fg"><label class="fl">WEBSITE URL</label><input type="url" id="tin" class="fc" placeholder="https://example.com" required></div><div class="fg"><label class="fl">SITE PAGES (one per line)</label><textarea id="t2" class="fc" rows="4" placeholder="/\n/about\n/services\n/blog\n/contact"></textarea></div>',
    'Generate a complete XML sitemap for {v} with these pages:\n{v2}\n\nInclude proper priority values (homepage=1.0, main pages=0.8, secondary=0.6), changefreq values, and lastmod. Also provide: submission instructions for Google Search Console and Bing Webmaster Tools.'],
  'utm'=>['UTM Parameter Builder','Build UTM tracking URLs for Google Analytics campaigns.',
    '<div class="fg"><label class="fl">BASE URL</label><input type="url" id="tin" class="fc" placeholder="https://example.com/landing-page" required></div><div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem"><div class="fg"><label class="fl">CAMPAIGN SOURCE</label><input type="text" id="t2" class="fc" placeholder="e.g. google, newsletter, twitter"></div><div class="fg"><label class="fl">CAMPAIGN MEDIUM</label><input type="text" id="t3" class="fc" placeholder="e.g. cpc, email, social"></div></div><div class="fg"><label class="fl">CAMPAIGN NAME</label><input type="text" id="t4" class="fc" placeholder="e.g. spring_sale_2025"></div>',
    'Build UTM-tracked URLs for: {v}\nSource: {v2} | Medium: {v3} | Campaign: {v4}\n\nProvide: 1) Complete UTM URL 2) Shortened URL suggestion 3) QR code suggestion note 4) 3 additional UTM variations for A/B testing 5) Google Analytics setup tips for this campaign.'],
  'word_count'=>['Word & Character Counter','Count words, characters, sentences, and get readability metrics.',
    '<div class="fg"><label class="fl">TEXT TO ANALYZE</label><textarea id="tin" class="fc" rows="6" placeholder="Paste your content here..." required></textarea></div>',
    'Analyze this text and provide detailed statistics:\n{v}\n\nInclude: 1) Word count 2) Character count (with/without spaces) 3) Sentence count 4) Paragraph count 5) Average words per sentence 6) Longest word 7) Most frequent words (top 10) 8) Estimated reading time 9) Estimated speaking time.'],
  'case'=>['Text Case Converter','Convert text between different cases for SEO and copywriting.',
    '<div class="fg"><label class="fl">TEXT</label><textarea id="tin" class="fc" rows="3" placeholder="Enter text to convert..." required></textarea></div>',
    'Convert this text to all common cases:\n{v}\n\nProvide: 1) UPPERCASE 2) lowercase 3) Title Case 4) Sentence case 5) camelCase 6) PascalCase 7) kebab-case 8) snake_case 9) SCREAMING_SNAKE_CASE 10) Alternating cAsE (for fun). Format clearly labeled.'],
  'url_encode'=>['URL Encoder / Decoder','Encode or decode URLs for use in links and parameters.',
    '<div class="fg"><label class="fl">TEXT OR URL</label><textarea id="tin" class="fc" rows="3" placeholder="Enter URL or text to encode/decode..." required></textarea></div>',
    'Process this URL/text: {v}\n\nProvide: 1) URL Encoded version 2) URL Decoded version 3) Base64 Encoded 4) Base64 Decoded 5) HTML entity encoded 6) JavaScript escaped version. Explain any special characters found and their URL-safe equivalents.'],
  'redirects'=>['Redirect Chain Checker','Get redirect chain analysis and recommendations.',
    '<div class="fg"><label class="fl">URL TO CHECK</label><input type="url" id="tin" class="fc" placeholder="https://example.com/old-page" required></div>',
    'Analyze the redirect scenario for: {v}\n\nProvide: 1) Likely redirect chain analysis 2) SEO impact of redirect types (301, 302, meta refresh) 3) Link equity passing percentages 4) Redirect chain length recommendations 5) How to check redirects using tools 6) Common redirect issues to fix 7) .htaccess code examples for proper redirects.'],
];

$td=$toolDefs[$activeTool]??$toolDefs['keyword'];
echo '<div class="tool-title">'.e($td[0]).'</div>';
echo '<div class="tool-desc">'.e($td[1]).'</div>';
echo $td[2];
$promptTemplate=htmlspecialchars($td[3],ENT_QUOTES);
?>
<button class="btn bp" onclick="runTool()" id="toolBtn" style="margin-top:.7rem">
  <span class="spin">⚙</span> &nbsp;Run AI Analysis →
</button>
<div class="out" id="toolOut" style="display:none"></div>

<script>
var promptTpl=<?=json_encode($td[3])?>;
async function runTool(){
  var v=document.getElementById("tin")?.value?.trim()||"";
  var v2=document.getElementById("t2")?.value?.trim()||"";
  var v3=document.getElementById("t3")?.value?.trim()||"";
  var v4=document.getElementById("t4")?.value?.trim()||"";
  if(!v)return alert("Please fill in the required field");
  var prompt=promptTpl.replace(/{v}/g,v).replace(/{v2}/g,v2).replace(/{v3}/g,v3).replace(/{v4}/g,v4);
  var btn=document.getElementById("toolBtn");
  var out=document.getElementById("toolOut");
  btn.disabled=true;btn.innerHTML='<span class="spin">⚙</span> &nbsp;Analyzing...';
  out.style.display="block";out.textContent="Analyzing with AI...";
  try{
    var res=await fetch("https://api.anthropic.com/v1/messages",{
      method:"POST",headers:{"Content-Type":"application/json"},
      body:JSON.stringify({model:"claude-sonnet-4-20250514",max_tokens:1000,messages:[{role:"user",content:prompt}]})
    });
    var data=await res.json();
    out.textContent=data.content?.map(b=>b.text||"").join("")||"No response";
    // Log usage
    fetch("<?=e(appUrl())?>/api/audit",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({tool:"<?=e($activeTool)?>",_log:1})}).catch(()=>{});
  }catch(e){out.textContent="Error: "+e.message;}
  btn.disabled=false;btn.innerHTML='<span>⚙</span> &nbsp;Run Again';
}
</script>
</div>
