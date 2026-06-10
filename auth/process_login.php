<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
session_start();
require "../config/database.php";

$sql = "SELECT * FROM users WHERE email = ?";
$qmanager_skua = $qmanager->prepare($sql);
$qmanager_skua->bind_param("s", $_POST['email']);
$qmanager_skua->execute();
$result = $qmanager_skua->get_result();

$user = $result->fetch_assoc();

if ($user && password_verify($_POST['password'], $user['password'])) {
    $_SESSION['user'] = $user;
   

    $qmanager->query("
REPLACE INTO user_sessions (session_id, user_id, last_activity)
VALUES ('".session_id()."', {$user['user_id']}, NOW())
");

    header("Location: ../dashboard/index.php");
} else {

    include 'alert.php'; 
    echo "<script>
    alerta('Login inválido!', 'error');
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 1500);
</script>";
   
}
