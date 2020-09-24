<?php
// App configuration

$conf = [];

// Nom de l'aplicatiu
$conf['appName'] = 'App de traçabilitat DAFME';

// Configuració de la base de dades
$conf['db'] = [];
$conf['db']['host'] = '';
$conf['db']['database'] = '';
$conf['db']['user'] = '';
$conf['db']['password'] = '';

// Enllaç al formulari de Google de l'FME
$conf['formUrl'] = 'https://docs.google.com/forms/d/e/1FAIpQLSfT9o287VqLyhwR8LPdloAQWhuqCgA3NfdhgP5vb9_sVQHL-g/viewform';

// Credencials de les APIs de Google (seguir pasos de
// https://developers.google.com/identity/protocols/oauth2/web-server#creatingcred)
$conf['goog'] = [];
$conf['goog']['clientId'] = '';
$conf['goog']['secret'] = '';

// Lloc on s'allotja l'API (backend, és a dir, aquest programari)
//   Exemple: https://covid-backend.fme.upc.edu/
$conf['fullPath'] = '';

// Lloc on s'allotja el web (frontend)
//   Exemple: https://covid.fme.upc.edu/
$conf['frontendUrl'] = '';

// Valor del header Access-Control-Allow-Origin per les peticions a l'API.
// Per defecte és el valor de la variable $conf['homeUrl']
$conf['allowedOrigin'] = $conf['homeUrl'];
