<?php
// add_subjects.php - A PHP script used to add subjects to the subjects list
// saved in the DB.

require_once(__DIR__.'/../core.php');

if (php_sapi_name() != 'cli')
  exit();

$subjects = [];

if ($argc == 2 && $argv[1] == '--stdin') {
  $stdin = file_get_contents('php://stdin');
  $subjects = json_decode($stdin, true);
} elseif ($argc == 3) {
  $subjects[] = [
    'friendly_name' => $argv[1],
    'calendar_name' => $argv[2]
  ];
} else {
  echo "Usage: php add_subjects.php friendly_name calendar_name\n";
  echo "php add_subjects.json --stdin < subjects.json\n";
  exit();
}

$con->beginTransaction();

$query = $con->prepare('INSERT INTO subjects (friendly_name, calendar_name) VALUES (:friendly_name, :calendar_name)');
foreach ($subjects as $subject) {
  if (!isset($subject['friendly_name']) || !isset($subject['calendar_name']) || empty($subject['friendly_name']) || empty($subject['calendar_name'])) {
    $con->rollback();
    echo "The JSON file passed is malformed. It should be an array consisting of objects which have non-empty 'friendly_name' and 'calendar_name' properties.\n";
    exit();
  }

  if (!$query->execute($subject)) {
    echo "An error occurred while adding the subject '".$subject['friendly_name']."' to the database. This doesn't affect the other subjects.\n";
  }
}

$con->commit();

echo "Success.\n";
