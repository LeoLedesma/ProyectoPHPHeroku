<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController implements IApiUsable
{

    function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (
            (isset($parametros['cod_identificacion']) && strlen($parametros['cod_identificacion']) == 5 &&
                isset($parametros['nombre_cliente'])
            )
        ) {
            $nuevaMesa = new Mesa();
            $nuevaMesa->cod_identificacion = $parametros['cod_identificacion'];
            $nuevaMesa->nombre_cliente = $parametros['nombre_cliente'];
            $id = $nuevaMesa->alta();

            $payload = json_encode(array("mensaje" => "Mesa creada con exito. Id: " . $id));
        } else {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function ObtenerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Mesa" => $lista), JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function ConsultarTiempoMesa($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if (isset($parametros['id_mesa'])) {
            $demora = Mesa::buscarDemoraMesa($parametros['id_mesa']);
            $payload = json_encode(array("Tiempo demora mesa" => $demora), JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array("ERROR" => 'No se recibieron todos los parametros'), JSON_PRETTY_PRINT);

        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function ConsultarTiempoPedido($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if (isset($parametros['id_pedido'])) {
            $demora = Mesa::buscarDemoraPedido($parametros['id_pedido']);
            $payload = json_encode(array("Tiempo demora pedido" => $demora), JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array("ERROR" => 'No se recibieron todos los parametros'), JSON_PRETTY_PRINT);

        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function PedirCuenta($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if (isset($parametros['id_mesa'])) {
            $total = Mesa::pedirCuenta($parametros['id_mesa']);
            if ($total != false) {
                $pedidos = Pedido::obtenerPedidosMesaByIdMesa($parametros['id_mesa']);
                Mesa::actualizarMesa($parametros['id_mesa'], Mesa::PAGANDO);
                $payload = json_encode(array("Pedidos:" => $pedidos, "Total" => $total), JSON_PRETTY_PRINT);
            }
        } else {
            $payload = json_encode(array("ERROR" => 'No se recibieron todos los parametros'), JSON_PRETTY_PRINT);

        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function CerrarMesa($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if (isset($parametros['id_mesa'])) {
            Mesa::actualizarMesa($parametros['id_mesa'], Mesa::CERRADA);
            $payload = json_encode(array("Ok" => 'Mesa cerrada'), JSON_PRETTY_PRINT);
        } else {
            $payload = json_encode(array("ERROR" => 'No se recibieron todos los parametros'), JSON_PRETTY_PRINT);

        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function MejorMesa($request, $response, $args)
    {

        $mesa = Mesa::MejorMesa();
        $payload = json_encode(array("Mejor mesa" => $mesa), JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



    function CargarFoto($request, $response, $args)
    {

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

}