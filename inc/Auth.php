<?php
namespace DAFME\Covid;

class Auth {
  private $client;

  public function __construct() {
    global $conf;
    $this->client = new \Google_Client();
    $this->client->setApplicationName = 'dafme-covid-tracability-backend';
    $this->client->setClientId($conf['goog']['clientId']);
    $this->client->setClientSecret($conf['goog']['secret']);
    $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
    $this->client->setRedirectUri($conf['fullPath'].'oauth2callback.php');
    $this->client->setAccessType('online');

    // Sometimes the server is slightly out of sync with the OAuth2 server.
    \Firebase\JWT\JWT::$leeway = 5;
  }

  public function getAuthUrl() {
    return $this->client->createAuthUrl();
  }

  public function handleCallback() {
    global $_GET, $con;
    if (isset($_GET['error']) || !isset($_GET['code'])) return 1;

    $accessToken = null;

    try {
      $accessToken = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
    } catch (\Exception $exception) {
      return 2;
    }

    $id = $this->client->verifyIdToken();
    if ($id === false)
      return 3;

    if (!isset($id['sub']) || !isset($id['email']) || !isset($id['email_verified']))
      return 4;

    if ($id['email_verified'] === false)
      return 5;

    $sub = $id['sub'];
    $email = $id['email'];

    if (preg_match('/upc.edu$/', $id['email']) !== 1)
      return 6;

    if (!Users::signIn($sub, $email))
      return 7;

    return 0;
  }

  public function setAccessToken($token) {
    $this->client->setAccessToken($token);
  }
}
