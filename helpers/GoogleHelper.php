<?php
namespace soury\googletasks\helpers;

abstract class GoogleHelper
{
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public static function getClient($authCode = null)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Tasks API PHP');
        $client->setScopes(\Google_Service_Tasks::TASKS);
        $client->setAuthConfig('./.config.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'token.json';
        $auth = true;
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            if($accessToken) {
                $client->setAccessToken($accessToken);
                $auth = false;
            }
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                try {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } catch (\Throwable $th) {
                    unlink($tokenPath);
                    $auth = true;
                }
            }  else {
                $auth = true;
            }
            if($auth) {
                // Request authorization from the user.
                if(!$authCode) {
                    $to      = 'info@ma-ced.it';
                    $subject = 'Google task API - Token expired';
                    $message = '
                        <html>
                            <head>
                                <title>Google task API - Token expired</title>
                            </head>
                            <body>
                                <p>You can Authenticate <a href="'.$authUrl.'">here</a></p>
                            </body>
                        </html>
                    ';
                    $headers = 'MIME-Version: 1.0'       . "\r\n" .
                                'Content-type: text/html; charset=iso-8859-1'       . "\r\n" .
                                'From: taskAPI@ma-ced.it'       . "\r\n" .
                                'Reply-To: taskAPI@ma-ced.it' . "\r\n" .
                                'X-Mailer: PHP/' . phpversion();
                    mail($to, $subject, $message, $headers);
                    $authUrl = $client->createAuthUrl();
                    printf("Open the following link in your browser:\n%s\n", $authUrl);
                    echo "<br>";
                    print 'Enter verification code: ';
                    header("Location: $authUrl");
                }
                else {
                    // Exchange authorization code for an access token.
                    echo $authCode;
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            $data = $client->getAccessToken();
            if($data) {
                file_put_contents($tokenPath, json_encode($data));
            }
        }
        return $client;
    }
}
?>