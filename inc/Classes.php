<?php
namespace DAFME\Covid;

class Classes {
  public static function getCurrentClasses() {
    global $con;

    $isSignedIn = Users::isSignedIn();

    $sentence = 'SELECT c.id, c.calendar_name, c.room, c.begins, c.ends, s.id subject_id, s.friendly_name'.($isSignedIn ? ', u_s.id user_subject_id' : '').'
        FROM classes c
        INNER JOIN subjects s
          ON c.calendar_name = s.calendar_name
        '.($isSignedIn ? 'LEFT OUTER JOIN user_subjects u_s
          ON s.id = u_s.subject_id
        ' : '').
        'WHERE
          c.begins < NOW() AND
          c.ends > NOW()'.($isSignedIn ? ' AND
          (
            u_s.user_id = :user_id OR
            u_s.subject_id IS NULL
          )': '');
    $query = $con->prepare($sentence);
    
    if (!$query->execute(($isSignedIn ? ['user_id' => Users::getUserId()] : [])))
      return false;

    $classes = $query->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($classes as &$class) {
      if (!$isSignedIn)
        $class['user_subject_id'] = null;

      $class['user_selected'] = $class['user_subject_id'] !== null;
    }

    return $classes;
  }

  public static function handleAPIGetClasses($body) {
    if (!isset($body['type']))
      return false;

    $response = [];

    switch ($body['type']) {
      case 'current':
        $classes = self::getCurrentClasses();
        if ($classes === false)
          return false;

        $response['classes'] = $classes;
        break;

      default:
        return false;
    }

    return $response;
  }
}
