<?php

require_once __DIR__.'/../helpers.php';

session_start();
session_destroy();

header('Location: /index.php');
exit;
