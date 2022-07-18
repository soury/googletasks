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
        if (file_exists('fetchAccessTokenWithRefreshToken.log')) {
            if($authCode) {
                unlink('fetchAccessTokenWithRefreshToken.log');
            } else {
                return 'fetchAccessTokenWithRefreshToken';
            }
        }
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            file_put_contents('token_logs.log', "AccessTokenExpired: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
            if ($client->getRefreshToken()) {
                try {
                    file_put_contents('fetchAccessTokenWithRefreshToken.log', "fetchAccessTokenWithRefreshToken: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    if (file_exists('fetchAccessTokenWithRefreshToken.log')) {
                        unlink('fetchAccessTokenWithRefreshToken.log');
                    }
                } catch (\Throwable $th) {
                    file_put_contents('token_logs.log', "fetchAccessTokenWithRefreshToken Faild: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
                    unlink($tokenPath);
                    $auth = true;
                }
            } else {
                $auth = true;
            }
            if($auth) {
                // Request authorization from the user.
                if(!$authCode) {
                    $authUrl = $client->createAuthUrl();
                    file_put_contents('token_logs.log', "!authCode: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
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
                    file_put_contents('token_logs.log', "fetchAccessTokenWithAuthCode Faild: ".(date('d-m-Y H:i'))." <br />\n" , FILE_APPEND | LOCK_EX);
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
            file_put_contents('token_logs.log', "data Faild: ".(date('d-m-Y H:i')).": ".(json_encode($data))." <br />\n" , FILE_APPEND | LOCK_EX);
            if($data) {
                file_put_contents($tokenPath, json_encode($data));
            }
        }
        return $client;
    }
}
?>