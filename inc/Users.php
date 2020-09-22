<?php
namespace DAFME\Covid;

class Users {
  private static function getUserId($sub) {
    global $con;
    $query = $con->prepare('SELECT id FROM users WHERE sub = ?');
    if (!$query->execute([$sub]))
      return false;

    if ($query->rowCount() < 1)
      return false;

    $row = $query->fetch();
    return $row['id'] ?? false;
  }

  public static function add($sub, $email) {
    global $con;
    $query = $con->prepare('INSERT INTO users (sub, email) VALUES (?, ?)');
    if (!$query->execute([$sub, $email]))
      return false;

    return $con->lastInsertId();
  }

  public static function signIn($sub, $email) {
    global $_SESSION;

    $userId = self::getUserId($sub);

    if ($userId === false)
      $userId = self::add($sub, $email);

    if ($userId === false)
      return false;

    $_SESSION['userId'] = $userId;
    return true;
  }

  public static function signOut() {
    global $_SESSION;
    unset($_SESSION['userId']);
  }

  public static function isSignedIn() {
    global $_SESSION;
    return isset($_SESSION['userId']);
  }
}
