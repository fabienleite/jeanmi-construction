<?php

include('head.php');
include('login_form.php');

try{
    $conn = 'sqlite:jeanmi-construction.db';
    $pdo = new PDO($conn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    $msg = 'Error while accessing database. Please warn challenge organisation.';
    die($msg);
    exit();
}

if(isset($_POST['login']) && isset($_POST['password'])){
    $userlogin = $_POST['login'];
    $userPasswd = hash('sha256', $_POST['password']);

    if($pdo->query("SELECT COUNT(*) FROM Users WHERE username = '" . $userlogin . "' AND passwd = '" . $userPasswd . "'")->fetch()[0] == 1) {
        // set JWT for user
        // redirect him to admin page with flag
        // exit after redirect
        echo '<section><div class="container text-uppercase mb-4"> Correct </div></section>';
    } else {
        echo '<section><div class="container text-uppercase mb-4"> Incorrect login / password</div></section>';
    }
}

include('foot.php');