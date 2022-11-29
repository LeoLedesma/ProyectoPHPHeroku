<?php
use Psr7Middlewares\Middleware\Payload;
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
use Dompdf\Dompdf;

class ProductoController implements IApiUsable
{
   
    function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        if ((isset($parametros['txt_desc']) && 
            isset($parametros['txt_desc']) && 
            isset($parametros['precio']) && 
            isset($parametros['tiempo_est_preparacion'])))
        {
            $nuevoProducto = new Producto();    
            $nuevoProducto->txt_desc = $parametros['txt_desc'];    
            $nuevoProducto->precio = $parametros['precio'];
            $nuevoProducto->cod_producto = $parametros['cod_producto'];
            $nuevoProducto->tiempo_est_preparacion = $parametros['tiempo_est_preparacion'];
            $id = $nuevoProducto->alta();
        
            $payload = json_encode(array("mensaje" => "Producto creado con exito. Id: ".$id));
        } else {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }    
      
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');    
    }

    function ObtenerTodos($request, $response, $args)
    {
        $cod_usuario = AutentificadorJWT::ObtenerRol($request);

        $lista = "";
        if($cod_usuario != Usuario::SOCIO)
        {
            $lista = Producto::obtenerTodosByTipoUsuario($cod_usuario);
        }else
        {
            $lista = Producto::obtenerTodos();
        }

        $payload = json_encode(array("Producto" => $lista), JSON_PRETTY_PRINT);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
    }

    function Baja($request, $response, $args)
    {

    }
    function Modificacion($request, $response, $args)
    {

    }
    function Borrar($request, $response, $args)
    {

    }

    function CargaMasiva($request, $response, $args)
    {        
        $archivo = $request->getUploadedFiles()['archivo'];
        $payload = "";
                
        
        if(isset($archivo)){
            
            if(pathinfo($archivo->getClientFileName(), PATHINFO_EXTENSION) == 'csv')
            {
                $path = "./". $archivo->getClientFileName();
                
                $archivo->moveTo($path);

                $payload = json_encode(Producto::leerArchivo($path));
            }else
            {
                $payload = json_encode(array('ERROR' => 'Extension invalida'));
            }                   
        }else
        {
            $payload =  json_encode(array('ERROR' => 'No se encontró ningun archivo'));
        }

       


        $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');    
    }

    function ExportarTodos($request, $response, $args)
    {           
        $array = Producto::obtenerTodosShort();
        $payload = json_encode($array);            
        
        header('Content-Type: application/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=productos.csv');
        ob_end_clean();

        if(file_exists('./productos.csv'))
        {
            $data = json_decode($payload, true);
            $fp = fopen("./productos.csv", 'w');
            foreach($data as $row)
            {
                fputcsv($fp, $row);
            }
            fclose($fp);
        }

        readfile('./productos.csv');    
        
          return $response->withHeader('Content-Type', 'application/csv');    
    }

     function ExportarTodosPDF($request, $response, $args)
     {             
          // instantiate and use the dompdf class
          $dompdf = new Dompdf();
      
          $array = Producto::obtenerTodos();
      
          $stringHTML = Producto::getHtml($array);
      
          $dompdf->loadHtml($stringHTML);
      
          // (Optional) Setup the paper size and orientation
          $dompdf->setPaper('A4', 'landscape');
      
          // Render the HTML as PDF
          $dompdf->render();
      
          // Output the generated PDF to Browser
          $dompdf->stream();
      
          $response->getBody()->write($dompdf);
          return $response
            ->withHeader('Content-Type', 'application/pdf');        
     }

        

}
