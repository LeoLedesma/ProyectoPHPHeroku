<?php
interface IApiUsable
{
	public function Alta($request, $response, $args);
	public function Baja($request, $response, $args);
	public function Modificacion($request, $response, $args);
	public function Borrar($request, $response, $args);	
}
