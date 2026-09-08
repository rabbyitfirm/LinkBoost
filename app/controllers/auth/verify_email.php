<?php
$token = $_GET['token'] ?? '';
if ($token) {
    try {
        $s = db()->prepare("SELECT id FROM `".pfx()."users` WHERE remember_token=? LIMIT 1");
        $s->execute([$token]);
        $u = $s->fetch();
        if ($u) {
            db()->prepare("UPDATE `".pfx()."users` SET email_verified=1,remember_token=NULL WHERE id=?")->execute([$u['id']]);
            $_SESSION['flash_success'] = 'Email verified! You can now log in.';
        }
    } catch (Exception $e) {}
}
redirect('/login');
