<?php
session_start();

include('../config.php');

if (isset($_POST['user'], $_POST['pass'])) {
    $username = $_POST['user'];
    $entered_password = $_POST['pass'];

    $stmt = $conn->prepare("SELECT id_user, user, pass, perm FROM user WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_user, $username, $hashed_password, $perm);
        $stmt->fetch();

        if (password_verify($entered_password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['perm'] = $perm;
            $_SESSION['id_user'] = $id_user;


            if ($perm == 0) {
                header("Location: ../index.php");
                exit();
            } else if ($perm == 3) {
                header("Location: ../form.php");
                exit();
            } else if ($perm == 2) {
                header("Location: ../payment.php?id_user=" . $id_user . "");
                exit();
            } else if ($perm == 1) {
                header("Location: ../manage/shop_list_view.php?id_user=" . $id_user . "");
                exit();
            } else {
                header("Location: ../signin.php?error=invalid_perm");
                exit();
            }
        } else {
            header("Location: ../signin.php?error=invalid_pass");
            exit();
        }
    } else {
        header("Location: ../signin.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: ../signin.php?error=missing_data");
    exit();
}

?>