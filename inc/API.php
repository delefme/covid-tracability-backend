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

  private static function checkRequestMethod(string $method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method)
      self::returnError('This action requires using the '.$method.' method.');
  }

  private static function getJSONBody() {
    self::checkRequestMethod('POST');

    $rawBody = file_get_contents('php://input');
    $json = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE)
      self::returnError('The request body is malformed.');

    return $json;
  }

  private static function setCORSHeaders() {
    global $conf;
    if ((isset($conf['allowAllOrigins']) && $conf['allowAllOrigins']) ||
        (isset($conf['allowedOrigins']) &&
        isset($_SERVER['HTTP_ORIGIN']) &&
        in_array($_SERVER['HTTP_ORIGIN'], $conf['allowedOrigins']))) {
      header('Access-Control-Allow-Origin: '.($_SERVER['HTTP_ORIGIN'] ?? '*'));
      header('Access-Control-Allow-Credentials: true');
    }
  }


  public static function process($path) {
    global $conf;

    header('Content-Type: application/json');
    self::setCORSHeaders();

    $parts = explode('/', $path);
    $method = $parts[0] ?? '';

    switch ($method) {
      case 'getAuthUrl':
        self::checkRequestMethod('GET');
        $auth = new Auth();
        self::returnPayload([
          'url' => $auth->getAuthUrl()
        ]);
        break;

      case 'isSignedIn':
        self::checkRequestMethod('GET');
        $isSignedIn = \DAFME\Covid\Users::isSignedIn();
        self::returnPayload([
          'signedIn' => $isSignedIn
        ]);
        break;

      case 'getStartupData':
        self::checkRequestMethod('GET');

        $payload = [];
        $payload['user'] = [];
        $payload['user']['signedIn'] = \DAFME\Covid\Users::isSignedIn();
        $payload['user']['email'] = ($payload['user']['signedIn'] ? Users::getUserData('email') : null);

        $auth = new Auth();
        $payload['authUrl'] = $auth->getAuthUrl();
        $payload['subjects'] = \DAFME\Covid\Subjects::getStartupSubjects();

        self::returnPayload($payload);
        break;

      case 'signOut':
        self::checkRequestMethod('POST');
        \DAFME\Covid\Users::signOut();
        self::returnOk();
        break;

      case 'getAllSubjects':
        self::checkRequestMethod('GET');
        $subjects = Subjects::getAll();

        if ($subjects === false)
          self::returnError();

        self::returnPayload([
          'subjects' => $subjects
        ]);
        break;

      case 'getUserSubjects':
        self::checkRequestMethod('GET');
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

      case 'getCurrentClasses':
        self::checkRequestMethod('GET');
        $classes = Classes::getClasses();
        if ($classes === false)
          self::returnError();
        else
          self::returnPayload([
            'classes' => $classes
          ]);
        break;

      case 'getClassesInTime':
        self::checkRequestMethod('GET');
        if (!$parts[1]) self::returnError("You must provide a unix time");
        $unix_time = filter_var($parts[1], FILTER_VALIDATE_INT);
        if (!$unix_time) self::returnError("Received parameter is not an integer");

        $classes = Classes::getClasses($unix_time);
        if ($classes === false)
          self::returnError();
        else
          self::returnPayload([
            'classes' => $classes
          ]);
        break;

      case 'getClassesInRoomToday':
        self::checkRequestMethod('GET');
        if (!$parts[1]) self::returnError("You must provide a room name");

        $classes = Classes::getClassesInSpaceToday($parts[1]);
        if ($classes === false)
          self::returnError();
        else
          self::returnPayload([
            'classes' => $classes
          ]);
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
