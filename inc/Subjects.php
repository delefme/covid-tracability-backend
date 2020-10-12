<?php
namespace DAFME\Covid;

class Subjects {
  public static function getAll() {
    global $con;
    $query = $con->prepare('SELECT * FROM subjects');

    if (!$query->execute())
      return false;

    return $query->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function addIsUserSelected(&$entries, $doIfSignedOut = true) {
    $isSignedIn = Users::isSignedIn();

    if (!$doIfSignedOut) return;

    foreach ($entries as &$entry) {
      if (!$isSignedIn)
        $entry['user_subject_id'] = null;

      $entry['user_selected'] = $entry['user_subject_id'] !== null;
    }
  }

  public static function getStartupSubjects() {
    global $con;
    $isSignedIn = Users::isSignedIn();

    $query = $con->prepare('SELECT s.id, s.friendly_name'.($isSignedIn ? ', us.id user_subject_id' : '').'
      FROM subjects s'.($isSignedIn ? '
      LEFT JOIN user_subjects us
        ON s.id = us.subject_id
      WHERE
        us.user_id = :user_id OR
        us.user_id IS NULL' : '').'
      ORDER BY s.friendly_name ASC');

    $query_params = [
      "user_id" => Users::getUserId()
    ];

    if (!$query->execute($query_params)) return false;
    $subjects = $query->fetchAll(\PDO::FETCH_ASSOC);

    self::addIsUserSelected($subjects, false);
    return $subjects;
  }

  public static function exists(int $subject): bool {
    global $con;
    $query = $con->prepare('SELECT id FROM subjects WHERE id = ?');
    if (!$query->execute([$subject]))
      return false;

    return $query->rowCount() > 0;
  }

  public static function isNewUserSubject(int $subjectId): bool {
    global $con;
    $query = $con->prepare('SELECT id FROM user_subjects WHERE user_id = :user_id AND subject_id = :subject_id');

    $userId = Users::getUserId();
    if ($userId == -1 || !$query->execute([
      'user_id' => $userId,
      'subject_id' => $subjectId
    ]))
      return false;

    return $query->rowCount() == 0;
  }

  public static function getUserSubjects() {
    global $con;
    $query = $con->prepare('SELECT
        us.id, us.subject_id, s.friendly_name, s.calendar_name
        FROM user_subjects us
        INNER JOIN subjects s
          ON us.subject_id = s.id
        WHERE us.user_id = ?');

    $userId = Users::getUserId();
    if ($userId == -1 || !$query->execute([$userId]))
      return false;

    return $query->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function addUserSubject(int $subjectId): bool {
    global $con;

    if (!self::exists($subjectId) || !self::isNewUserSubject($subjectId))
      return false;

    $query = $con->prepare('INSERT INTO user_subjects (user_id, subject_id) VALUES (:user_id, :subject_id)');

    $userId = Users::getUserId();

    return $userId != -1 && $query->execute([
      'user_id' => $userId,
      'subject_id' => $subjectId
    ]);
  }

  public static function removeUserSubject(int $subjectId): bool {
    global $con;

    $query = $con->prepare('DELETE FROM user_subjects WHERE user_id = :user_id AND subject_id = :subject_id LIMIT 1');

    $userId = Users::getUserId();
    return $userId != -1 && $query->execute([
      'user_id' => $userId,
      'subject_id' => $subjectId
    ]);
  }
}
