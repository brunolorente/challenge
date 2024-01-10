# Challenge seQura

## Requisitos:
- Docker
- Docker Compose
- No editar .env ni .env.test

## Uso:
- En una CLI, ubicarnos dentro del directorio `challenge` y ejecutar el comando `make build`.

##### El proceso ejecutará los contenedores de Docker necesarios para el proyecto, importará los datos, ejecutará los procesos del challenge y mostrará una tabla con los resultados.

## Código:
El código utilizado para la prueba (sin contar tests, migraciones, e infraestructura):

````
app
|-- Console
|   |-- Commands
|      |-- GenerateDisbursements.php
|      |-- ImportMerchants.php
|      |-- ImportOrders.php
|      `-- Summary.php
|   
|-- Contracts
|   |-- AdditionalFeeInterface.php
|   |-- DisbursementRepositoryInterface.php
|   |-- FileDownloaderInterface.php
|   |-- MerchantImporterInterface.php
|   |-- MerchantRepositoryInterface.php
|   |-- OrderImporterInterface.php
|   `-- OrderRepositoryInterface.php
|-- DTOs
|   |-- AdditionalFeeData.php
|   |-- DisbursementData.php
|   |-- MerchantData.php
|   `-- OrderData.php
|-- Models
|   |-- AdditionalFee.php
|   |-- Disbursement.php
|   |-- Merchant.php
|   `-- Order.php
|-- Providers
|   `-- AppServiceProvider.php
|-- Repositories
|   |-- EloquentAdditionalFeeRepository.php
|   |-- EloquentDisbursementRepository.php
|   |-- EloquentMerchantRepository.php
|   `-- EloquentOrderRepository.php
`-- Services
    |-- CsvReader.php
    |-- CurlFileDownloader.php
    |-- DisbursementService.php
    |-- MerchantDataTransformer.php
    |-- MerchantImporterFromCsv.php
    |-- OrderDataTransformer.php
    `-- OrderImporterFromCsv.php
````
El resultado del análisis de sonarqube

# TODO: Captura de sonarqube

## Seguridad y Trazabilidad:
- Creación de cuatro tablas en la base de datos: disbursements, merchants, additional_fees y orders.
- Añadidos campos técnicos en tablas orders y merchants para trazabilidad (ingest_date, source).
- Reemplazo de id del CSV por external_id en tablas orders y merchants, con id local como primary key.

## Rendimiento y Manejo de Datos:
- Optimización del rendimiento mediante la lectura del CSV de merchants a través de la red.
- Descarga por partes del CSV de pedidos para evitar timeouts.
- Importación de un millón de pedidos en ~6-7 minutos en MacBook M1 Pro 2021 (16GB RAM, 1GB de memoria para PHP).
- Registro de pedidos con datos erróneos en el log de Laravel.
- No se verifica la existencia del merchant en cada pedido importado para mejorar el rendimiento.

## Decisiones de Diseño y Arquitectura:
- Elección de Laravel por similitud con Ruby on Rails y enfoque en código limpio y bien testeado siguiendo principios SOLID.
- Uso de modelos anémicos y traslado de la lógica a los servicios, evitando romper la filosofía del framework.

## Manejo de Repositorios y Tablas:
- Diseño de repositorios y tablas para prevenir la inserción de registros duplicados.

## Errores y Excepciones:
- Identificación de 8 pedidos con errores durante el proceso de importación.

## El proceso para cada comerciante es el siguiente:
* Verificamos si es elegible para desembolso en función del día en el que se está ejecutando el comando, en caso afirmativo:
    * Recuperamos los pedidos para un rango de fechas, del día anterior o de la semana anterior
    * Para cada orden:
        * Calculamos la comisión
        * Lo marcamos como pagado
    * Hacemos el cálculo de si ha llegado o no al mínimo de comisión y lo guardamos
    * Guardamos el desembolso total para las N ordenes previamente recuperadas

## Resultados:
El proceso total (instalación, importación, generación y obtención de datos) se ejecuta en ~9,5 minutos en un MacBook M1 Pro 2021 (16GB RAM, 1GB límite de memoria para PHP).

````
+------+-------------------------+-------------------------------+----------------------+--------------------------------+-------------------------------+
| Year | Number of Disbursements | Amount Disbursed to Merchants | Amount of Order Fees | Number of Monthly Fees Charged | Amount of Monthly Fee Charged |
+------+-------------------------+-------------------------------+----------------------+--------------------------------+-------------------------------+
| 2022 | 159                     | 358.382,16 €                  | 3.462,13 €           | 1                              | 25,83 €                       |
| 2023 | 1648                    | 3.653.564,66 €                | 33.600,84 €          | 18                             | 206,95 €                      |
+------+-------------------------+-------------------------------+----------------------+--------------------------------+-------------------------------+
````

## Puntos de mejora:
- Mejorar test e implementar Behat para prevenir regresiones y garantizar el escalado del proyecto.
- Multi hilo para mejorar el rendimiento de las importaciones y las demás operaciones.
- Refactorizar las clases Summary y DisbursementService para respetar los principios solid (sobre todo encapsular responsabilidades.
- Eliminar ejemplos y esqueleto de Laravel.
- El diseño de las tablas es mejorable, se ha hecho de esta mañana para no tardar demasiado tiempo, el precio a pagar ha sido una única query "complicada".
- Opcional: Tal vez se podrían crear subdirectorios dentro de Services y Contracts para organizar mejor los archivos pero al haber tan pocos no lo he visto necesario.

## Archivos .env .env.test

- .env
 ````
APP_NAME=sequra-challenge
APP_ENV=local
APP_KEY=base64:14NOgsi734l+5JPH7J3V9KHsP2Ky8uq9k//IcxiXqr0=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=sequra_db
DB_USERNAME=sequra_user
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

MERCHANTS_CSV_URL="https://sequra.github.io/backend-challenge/merchants.csv"
ORDERS_CSV_URL="https://sequra.github.io/backend-challenge/orders.csv"
````
- .env.test
````
APP_NAME=sequra-challenge
APP_ENV=local
APP_KEY=base64:14NOgsi734l+5JPH7J3V9KHsP2Ky8uq9k//IcxiXqr0=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=db-test
DB_PORT=5432
DB_DATABASE=sequra_db_test
DB_USERNAME=sequra_user_test
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

MERCHANTS_CSV_URL="/var/www/tests/Data/merchants.csv"
````
