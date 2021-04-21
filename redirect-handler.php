<?php
require_once './vendor/autoload.php';
if(!session_id()) {
    session_start();
}

use soury\googletasks\helpers\GoogleHelper;


if (!isset($_GET['code'])) {
    die('No code URL paramete present.');
}

$code = $_GET['code'];
$client = GoogleHelper::getClient($code);
echo 'Refresh token is: '.$code.' - Please add this to Enter verification code.';
?>
<script type="text/javascript">location.href = 'index.php';</script>