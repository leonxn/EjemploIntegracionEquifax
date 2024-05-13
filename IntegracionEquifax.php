  <?php

      namespace App\Http\Controllers;
      
      use GuzzleHttp\Client;
      use Illuminate\Http\Request;
      
      class SoapController extends Controller
      {
          public function getReporte()
          {
             $numeroDocumento = $request->input('numeroDocumento');

              $client = new Client();
      
              $soapRequest = <<<XML
              <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://equifax.com/WebService/types">
                  <soapenv:Header/>
                  <soapenv:Body>
                      <typ:consultaServicio11>
                          <EstructuraServicio11_1>
                              <header>
                                  <modelo>00103903</modelo>
                                  <usuario>xxxxxxxxxxx</usuario>
                                  <clave>xxxxxxxxxxxxx</clave>
                              </header>
                              <integrantesServicio>
                                  <tipoDocumento>1</tipoDocumento>
                                  <numeroDocumento>$numeroDocumento</numeroDocumento>
                              </integrantesServicio>
                          </EstructuraServicio11_1>
                      </typ:consultaServicio11>
                  </soapenv:Body>
              </soapenv:Envelope>
              XML;
      
              try {
                  $response = $client->request('POST', 'URL-WS', [
                      'headers' => [
                          'Content-Type' => 'text/xml; charset=utf-8',
                          'SOAPAction' => '' // Si es necesario, especifica la acción SOAP aquí.
                      ],
                      'body' => $soapRequest,
                      'verify' => false  // Desactiva la verificación SSL si es necesario (no recomendado para producción).
                  ]);
      
             
                  $responseBody = $response->getBody()->getContents();
                  $formattedResponse = $this->formatXmlResponseForDisplay($responseBody); 
                  return response()->json($formattedResponse, 200);  // Envía la respuesta en formato JSON
      
              } catch (\GuzzleHttp\Exception\GuzzleException $e) { 
                  echo 'Request failed: ' . $e->getMessage();
              }
      }


 //FUNCIONES PARA OBTENER LOS VALORES DE CADA ETIQUETA Y FORMATEARLA EN JSON
      
       private function formatXmlResponseForDisplay($xmlContent) {
            $xml = new \SimpleXMLElement($xmlContent);
            $namespaces = $xml->getNamespaces(true);
            $soapNamespace = $namespaces['soap'] ?? 'http://schemas.xmlsoap.org/soap/envelope/';
            $body = $xml->children($soapNamespace)->Body;
        
            $responseNamespace = $body->getNamespaces(true);
            $yourNamespace = $responseNamespace['ns4'] ?? 'http://equifax.com/WebService/types';
            $response = $body->children($yourNamespace);
        
            return $this->xmlToArray($response);  // Convierte XML a array
        }
        
        private function xmlToArray($xmlElement) {
            $array = [];
            foreach ($xmlElement->children() as $node) {
                if ($node->count() > 0) {
                    $array[$node->getName()] = $this->xmlToArray($node);  // Recursividad para nodos hijos
                } else {
                    $array[$node->getName()] = strval($node);  // Convertir el nodo a string
                }
            }
            return $array;
        }

}

     
