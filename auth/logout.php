<?php
session_start();           
session_unset();           
session_destroy();          

header("Location: /CARE/auth/login.php");
exit();
