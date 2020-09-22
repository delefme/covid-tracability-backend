# DeleFME/covid-tracability-backend
Per veure el repositori principal, aneu a [DeleFME/covid-tracability](https://github.com/DeleFME/covid-tracability).

## Requisits
- Servidor web Apache
- MariaDB o MySQL
- Composer

## Instal·lació
Per instal·lar el backend, seguiu els següents passos:

1. Cloneu aquest repositori al directori arrel del vostre servidor web.
2. Feu un duplicat de l'arxiu `config.default.php` amb el nom `config.php`, i ompliu el fitxer amb la configuració desitjada.
3. [Instal·leu Composer](https://getcomposer.org/doc/00-intro.md) a la vostra màquina (si no el teniu ja instal·lat) i executeu la comanda `composer install` al directori arrel.
4. Instal·leu la base de dades seguint els següents pasos:
    a. A MariaDB/MySQL, executeu la següent comanda per inicialitzar la base de dades: `CREATE DATABASE covid_tracability CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;` (es pot substituir `covid_tracability` per un altre nom de la base de dades).
    b. Executeu la comanda `mysql -u usuari -p covid_tracability < utils/db_structure.sql`, on `usuari` és l'usuari de MariaDB/MySQL.
5. Configureu Apache perquè faci cas a l'arxiu `.htaccess` del directori arrel. \[[+ info sobre com fer-ho](https://askubuntu.com/questions/429869/is-this-a-correct-way-to-enable-htaccess-in-apache-2-4-7)\]
6. Ja teniu el servidor disponible a `http://localhost/`!

Nota: per tal d'utilitzar Apache amb diversos projectes, el que es pot fer és posar el repositori a un altre directori, i configurar un `virtualhost` que respongui les peticions d'un host com `covid-tracability-backend.test` servint els documents de l'altre directori. També s'hauria de configurar a l'arxiu [hosts](https://ca.wikipedia.org/wiki/Fitxer_de_hosts) la resolució d'aquest domini (`covid-tracability-backend.test`) a l'IP `172.0.0.1`.
