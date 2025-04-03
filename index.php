<?php

$page = $_GET['page'] ?? 'home';

if ($page === 'user_logins') {
    require 'src/views/user_logins.php';
} else {
    require 'src/views/home.php';
}
