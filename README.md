# EjemploIntegracionEquifax
Ejemplo de integración equifax Laravel

# 1. Instalación de GuzzleHTTP
composer require guzzlehttp/guzzle
# 2. Agregar a nuestro controlador
use GuzzleHttp\Client;
# 3. agregar ruta
Route::get('/getReporte', [SoapController::class, 'getReporte']);
# 4. crear funcion
  archivo adjunto 
 
