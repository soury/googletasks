<?php
require_once './vendor/autoload.php';
if(!session_id()) {
    session_start();
}

use soury\googletasks\helpers\GoogleHelper;

$headers = apache_request_headers();
file_put_contents('login_logs.log', "data: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
foreach ($headers as $header => $value) {
    file_put_contents('login_logs.log', "$header: $value <br />\n" , FILE_APPEND | LOCK_EX);
}

if (!isset($_GET['code'])) {
    die('No code URL paramete present.');
}

$code = $_GET['code'];
file_put_contents('login_logs.log', "code: $code <br />\n <br />\n" , FILE_APPEND | LOCK_EX);

$client = GoogleHelper::getClient($code);
echo 'Refresh token is: '.$code.' - Please add this to Enter verification code.';
?>
<script type="text/javascript">location.href = 'index.php';</script>