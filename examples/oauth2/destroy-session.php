

<?php
use Illuminate\Support\Facades\Session;
require_once __DIR__.'/../helpers.php';

Session::flush();

header('Location: /index.php');
exit;
