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
}
