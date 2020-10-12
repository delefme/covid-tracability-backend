<?php
namespace DAFME\Covid;

class Users {
  private static function getUserIdFromSub($sub) {
    global $con;
    $query = $con->prepare('SELECT id FROM users WHERE sub = ?');
    if (!$query->execute([$sub]))
      return false;

    if ($query->rowCount() < 1)
      return false;

    $row = $query->fetch();
    return $row['id'] ?? false;
  }

  public static function add($sub, $email): int {
    global $con;
    $query = $con->prepare('INSERT INTO users (sub, email) VALUES (?, ?)');
    if (!$query->execute([$sub, $email]))
      return false;

    return $con->lastInsertId();
  }

  public static function signIn($sub, $email): bool {
    global $_SESSION;

    $userId = self::getUserIdFromSub($sub);

    if ($userId === false)
      $userId = self::add($sub, $email);

    if ($userId === false)
      return false;

    $_SESSION['userId'] = $userId;
    return true;
  }

  public static function signOut(): void {
    global $_SESSION;
    unset($_SESSION['userId']);
  }

  public static function isSignedIn(): bool {
    global $_SESSION;
    return isset($_SESSION['userId']);
  }

  public static function getUserId(): int {
    global $_SESSION;
    return (self::isSignedIn() ? $_SESSION['userId'] : -1);
  }

  public static function getUserData(string $field) {
    global $con;
    if (!self::isSignedIn()) return false;
    $userId = self::getUserId();

    $query = $con->prepare('SELECT '.$field.' FROM users WHERE id = ?');
    if (!$query->execute([$userId])) return false;

    return $query->fetchColumn();
  }
}
