<?php
require_once 'includes/config.php';
redirect(!empty($_SESSION['user']) ? 'dashboard.php' : 'login.php');
