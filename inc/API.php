<?php
namespace DAFME\Covid;

class API {
  private static function returnJSON($array) {
    echo json_encode($array);
  }

  public static function returnError($errorMessage = 'Unexpected error') {
    http_response_code(400);
    self::returnJson([
      'status' => 'error',
      'errorMessage' =>  $errorMessage
    ]);
  }

  public static function returnPayload($payload) {
    self::returnJson([
      'status' => 'ok',
      'payload' => $payload
    ]);
  }

  public static function returnOk() {
    self::returnJson([
      'status' => 'ok'
    ]);
  }

  private static function checkSignInStatus() {
    if (!Users::isSignedIn()) {
      self::returnError('The user hasn\'t signed in.');
      exit();
    }    
  }

  public static function process($path) {
    global $conf;

    header('Content-Type: application/json');

    if (isset($conf['allowedOrigin']) && !empty($conf['allowedOrigin']))
      header('Access-Control-Allow-Origin: '.$conf['allowedOrigin']);

    $parts = explode('/', $path);
    $method = $parts[0] ?? '';

    switch ($method) {
      case 'getAuthUrl':
        $auth = new Auth();
        self::returnPayload([
          'url' => $auth->getAuthUrl()
        ]);
        break;

      case 'isSignedIn':
        $isSignedIn = \DAFME\Covid\Users::isSignedIn();
        self::returnPayload([
          'signedIn' => $isSignedIn
        ]);
        break;

      case 'signOut':
        \DAFME\Covid\Users::signOut();
        self::returnOk();
        break;

      case 'getAllSubjects':
        $subjects = Subjects::getAll();

        if ($subjects === false)
          self::returnError();

        self::returnPayload([
          'subjects' => $subjects
        ]);
        break;

      case 'getUserSubjects':
        self::checkSignInStatus();
        // @TODO: Implement this method
        break;

      case 'setUserSubjects':
        self::checkSignInStatus();
        // @TODO: Implement this method
        break;

      case 'getClasses':
        self::checkSignInStatus();
        // @TODO: Implement this method
        break;

      case 'setClassState':
        self::checkSignInStatus();
        // @TODO: Handle this method
        break;

      default:
        self::returnError('The method requested doesn\'t exist.');
        break;
    }
  }
}
