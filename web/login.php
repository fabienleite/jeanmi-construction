<?php
use \Firebase\JWT\JWT;

ob_start();

include('vendor/autoload.php');
include('head.php');
include('login_form.php');
include('pdo_connection.php');

if(isset($_POST['login']) && isset($_POST['password'])){
    $userlogin = $_POST['login'];
    $userPasswd = hash('sha256', $_POST['password']);

    if($pdo->query("SELECT COUNT(*) FROM Users WHERE username = '" . $userlogin . "' AND passwd = '" . $userPasswd . "'")->fetch()[0] == 1) {
        $token = [
            'iat' => time(),
            'jti' => uniqid("", TRUE),
            'nbf' => time(),
            'exp' => time() + 5 * 3600,
            'data' => [
                'username' => $userlogin,
                'is_jeanmi_the_god' => 'false'
            ]
        ];
        $jwt = JWT::encode($token, 'jeanmiconstruction', 'HS256');
        ?>
            <script> window.localStorage.setItem('token', <?php echo "'" . trim($jwt) . "'" ?>); </script>
            <form action="admin.php" name="redirect" method="post" style="display:none;">
                <input type="text" name="token" id="token-redirect-value" value="" />
            </form>
            <script>
            document.getElementById('token-redirect-value').setAttribute('value', window.localStorage.getItem('token'));

            document.forms['redirect'].submit();
            </script>
        <?php
        exit(0);
    } else {
        echo '<section><div class="container text-uppercase mb-4" style="text-align: center"> Incorrect login / password</div></section>';
    }
}


include('foot.php');

// die(0);