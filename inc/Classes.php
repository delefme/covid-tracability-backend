<?php
namespace DAFME\Covid;

class Classes {
  // Marge en segons per seguir retornant una assignatura abans o després que
  // acabi, quan s'obtenen les classes actuals.
  const CURRENT_MARGIN_BEGINS = 5*60;
  const CURRENT_MARGIN_ENDS = 5*60;

  const INTIME_MARGIN_BEGINS = 0*60;
  const INTIME_MARGIN_ENDS = 0*60;


  public static function getClasses(int $unix_time = null) {
    global $con;

    $isSignedIn = Users::isSignedIn();

    $sentence = 'SELECT c.id, c.calendar_name, c.room, c.begins, c.ends, c.calendar_name, c.degree, s.id subject_id, s.friendly_name
      '.($isSignedIn ? ', u_s.id user_subject_id' : '').',
          CASE
            WHEN c.begins > :unix_time OR c.ends <= :unix_time
              THEN 0
              ELSE 1
          END is_current
        FROM classes c
        LEFT OUTER JOIN subjects s
          ON c.calendar_name = s.calendar_name
        '.($isSignedIn ? 'LEFT OUTER JOIN user_subjects u_s
          ON s.id = u_s.subject_id
        ' : '').
        'WHERE
          c.begins - '.($unix_time === null ? self::CURRENT_MARGIN_BEGINS : self::INTIME_MARGIN_BEGINS).' <= :unix_time AND
          c.ends + '.($unix_time === null ? self::CURRENT_MARGIN_ENDS: self::INTIME_MARGIN_ENDS).' > :unix_time'.($isSignedIn ? ' AND
          (
            u_s.user_id = :user_id OR
            u_s.subject_id IS NULL
          )': '').'
        ORDER BY
          s.id IS NULL, -- Mostrem primer les assignatures que reconeguem
          is_current DESC, -- Mostrem primer les classes actuals
          '.($isSignedIn ? 'u_s.subject_id IS NULL, -- Mostrem primer les classes seleccionades per l\'alumne
          ' : '').'s.friendly_name ASC, -- Ordenem per ordre alfabètic el nom de l\'assignatura
          c.calendar_name ASC, -- Ordenem per ordre alfabètic el nom de l\'assignatura del calendari
          c.room ASC -- Ordenem per ordre alfabètic l\'aula';
    $query = $con->prepare($sentence);

    if ($unix_time === null) $unix_time = time();

    $query_params = ['unix_time' => $unix_time];
    if ($isSignedIn) $query_params['user_id'] = Users::getUserId();

    if (!$query->execute($query_params)) return false;
    $classes = $query->fetchAll(\PDO::FETCH_ASSOC);

    Subjects::addIsUserSelected($classes);
    return $classes;
  }
}
