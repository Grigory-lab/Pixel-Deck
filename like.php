<?php
    include "DB.php";

    if (!isset($_SESSION['login'])) exit();

    $id = intval($_POST['id']);

    $sql = "SELECT User_ID FROM users WHERE Login=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['login']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    $user_id = $user['User_ID'];

    $sql = "SELECT * FROM likes WHERE User_ID=? AND Card_ID=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res)) {
        mysqli_query($conn, "DELETE FROM likes WHERE User_ID=$user_id AND Card_ID=$id");
        echo "unliked";
    } else {
        mysqli_query($conn, "INSERT INTO likes (User_ID, Card_ID) VALUES ($user_id, $id)");
        echo "liked";
    }
?>