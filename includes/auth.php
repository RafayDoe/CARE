<?php
session_start();

function require_role($allowed_roles = []) {
  if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: /CARE/auth/unauthorized.php");
    exit();
  }
}
