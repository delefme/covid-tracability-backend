<?php
// Oauth2 callback, which is meant to complete the sign in process

require_once('core.php');
header('Content-Type: text/plain');

$auth = new DAFME\Covid\Auth();
$returnCode = $auth->handleCallback();

switch ($returnCode) {
  case 0:
    header('Location: '.$conf['frontendUrl']);
    break;

  case 1:
    echo 'Hi ha hagut un problema iniciant sessió. Probablement has denegat l\'inici de sessió.';
    break;

  case 2:
  case 3:
  case 4:
  case 7:
    echo 'Hi ha hagut un problema iniciant sessió. Sisplau prova de fer-ho de nou.';
    break;

  case 5:
    echo 'El correu electrònic del teu compte no està verificat. Verifica\'l i torna a iniciar sessió';
    break;

  case 6:
    echo 'El teu compte no pertany a la UPC. Sisplau, inicia sessió amb el teu compte de Google de la UPC.';
    break;
}
