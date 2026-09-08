<?php
try{$plans=db()->query("SELECT * FROM `".pfx()."plans` WHERE status=1 ORDER BY sort_order")->fetchAll();}
catch(Exception $e){$plans=[];}
require LB_ROOT.'/app/views/public/pricing.php';
