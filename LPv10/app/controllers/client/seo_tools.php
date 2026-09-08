<?php
requireLogin();$user=currentUser();$activePage='seo_tools';$pageTitle='SEO Tools';
$activeTool=$_GET['tool']??'keyword';
ob_start();
include LB_ROOT.'/app/views/client/seo_tools.php';
$pageContent=ob_get_clean();
include LB_ROOT.'/app/views/layouts/client_wrap.php';
