<?php
    sessiont_start();
    if (session_destory()) {
        header("Location: login.php");
        exit();
    }
?>