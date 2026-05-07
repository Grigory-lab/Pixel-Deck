<?php
    include "DB.php";

    session_unset();
    session_destroy();

    header("Location: account.php");
    exit;