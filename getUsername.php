<?php
// session_save_path("/tmp");
session_start();
if(isset($_SESSION['username']))
    echo $_SESSION['username'];
?>