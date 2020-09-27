<?php
namespace DAFME\Covid;

class API {
  private static function returnJSON($array) {
    echo json_encode($array);
    exit();
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
    }    
  }

  private static function getJSONBody() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
      self::returnError('This action requires using the POST method.');

    $rawBody = file_get_contents('php://input');
    $json = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE)
      self::returnError('The request body is malformed.');

    return $json;
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
        $subjects = Subjects::getUserSubjects();

        if ($subjects === false)
          self::returnError();

        self::returnPayload([
          'subjects' => $subjects
        ]);
        break;

      case 'addUserSubject':
        self::checkSignInStatus();
        $body = self::getJSONBody();
        if (!isset($body['subject']))
          self::returnError();

        if (Subjects::addUserSubject((int)$body['subject']))
          self::returnOk();
        else
          self::returnError();
        break;

      case 'removeUserSubject':
        self::checkSignInStatus();
        $body = self::getJSONBody();
        if (!isset($body['subject']))
          self::returnError();

        if (Subjects::removeUserSubject((int)$body['subject']))
          self::returnOk();
        else
          self::returnError();
        break;

      case 'removeUserSubject':
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
