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

// Llistat d'orígens permesos per interactuar amb l'API.
//
// NOTA: És important que els valors de l'array no acabin amb un "/" final.
//   Exemple: ['https://covid.fme.upc.edu']
$conf['allowedOrigins'] = [];

// Ignorar la llista d'orígens permesos i permetre a qualsevol pàgina web
// interactuar amb l'API
$conf['allowAllOrigins'] = false;

// URL del repositori de GitHub.
$conf['gitHubRepo'] = 'https://github.com/delefme/covid-tracability-backend';

// Configurar si estem a producció ("false" vol dir que estem en un servidor
// de desenvolupament local)
$conf['isProduction'] = true;
