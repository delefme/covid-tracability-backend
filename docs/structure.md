# Estructura del backend
Aquest document resumeix com funciona el backend describint la seva estructura.

Per començar, l'arxiu `api.php` és l'arxiu que escolta les peticions a `/api/v1/*`, gràcies a la redirecció que fa la configuració de `.htaccess`.

L'arxiu `api.php` crida a la funció `\DAFME\Covid\API::process(...);`, que processa la petició.

Abans de continuar, però, observem 2 coses:

1. Les funcions del nostre backend es troben a classes dins del namespace `DAFME\Covid`. Cada classe representa un tipus diferent d'element o de funcionalitat, i la majoria de funcions a aquestes classes són estàtiques, tot i que es pot veure que algunes classes (com `Auth`) s'han de construir primer (`$auth = new Auth(...);`).
   - Usem un Namespace per diferenciar les nostres classes/funcions de les que implementen altres llibreries dw tercers que incloem amb Composer.
2. L'arxiu `api.php` inclou l'arxiu `core.php`. Si us fixeu, aquest arxiu el que fa és realitzar coses bàsiques com establir l'autoload de les classes, inicializar la sessió, carregar la configuració del fitxer `config.php`, inicialitzar la base de dades, etc. Molts dels scripts de PHP inclouen aquest fitxer `core.php`.

Aleshores, a l'arxiu `inc/API.php` (observem que la carpeta `inc` inclou els fitxers amb les classes, que tenen el mateix nom que la classe corresponent), la funció `process` gestiona les respostes que dona l'API.

Per fer això, es crida a diversos mètodes d'altres classes. Es recomana que el codi a `inc/API.php` sigui el més reduït possible i que la lògica estigui a altres funcions de les corresponents classes, ja que d'aquesta manera el codi és més modular i es poden reutilitzar les funcions més tard per realitzar altres tasques.

Per retornar la resposta, s'usen les funcions `returnError(...)`, `returnPayload(...)` i `returnOk()` (llegir la funció `process(...)` de la classe per veure com funciona.

# Base de dades
Per interactuar amb la base de dades, utilitzem la llibreria PDO de PHP. És bastant senzilla d'utilitzar! Us recomano llegir el codi de les diverses classes per veure com funciona, i llegir la documentació de PHP.

Si esteu contribuint i no teniu molta idea de com utilitzar PDO o no sabeu si ho esteu fent bé, tranquils! Programeu sense por i si us atasqueu sempre podeu demanar ajuda a altres colaboradors. I no tingueu por de fer un pull request, d'aquesta manera altres colaboradors poden revisar el codi i veure si la sentència SQL/la manera d'usar PDO és correcta :)

L'estructura de la base de dades està definida a `utils/db_structure.sql`, feu-li una ullada!

El table-parser programat en Python desa la informació a la base de dades, així que aquesta és la manera com es comuniquen les dues components.

