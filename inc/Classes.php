<?php
namespace DAFME\Covid;

class Classes {
  // Marge en segons per seguir retornant una assignatura abans o després que
  // acabi, quan s'obtenen les classes actuals.
  const MARGIN_BEGINS = 0*60;
  const MARGIN_ENDS = 0*60;

  public static function getCurrentClasses() {
    global $con;

    $isSignedIn = Users::isSignedIn();

    $sentence = 'SELECT c.id, c.calendar_name, c.room, c.begins, c.ends, c.calendar_name, s.id subject_id, s.friendly_name'.($isSignedIn ? ', u_s.id user_subject_id' : '').'
        FROM classes c
        LEFT OUTER JOIN subjects s
          ON c.calendar_name = s.calendar_name
        '.($isSignedIn ? 'LEFT OUTER JOIN user_subjects u_s
          ON s.id = u_s.subject_id
        ' : '').
        'WHERE
          c.begins - '.self::MARGIN_BEGINS.' < UNIX_TIMESTAMP() AND
          c.ends + '.self::MARGIN_ENDS.' > UNIX_TIMESTAMP()'.($isSignedIn ? ' AND
          (
            u_s.user_id = :user_id OR
            u_s.subject_id IS NULL
          )': '').'
        ORDER BY s.id IS NULL, '.($isSignedIn ? 'u_s.subject_id IS NULL, ' : '').'s.friendly_name ASC';
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
}
