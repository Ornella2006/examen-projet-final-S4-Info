<?php
require_once __DIR__ . '/../controllers/LoginController.php';

Flight::route('POST /login', ['LoginController', 'login']);
?>