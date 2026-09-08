<?php
try{$services=db()->query("SELECT * FROM `".pfx()."services` WHERE status=1 ORDER BY sort_order,price")->fetchAll();}
catch(Exception $e){$services=[];}
require LB_ROOT.'/app/views/public/services.php';
