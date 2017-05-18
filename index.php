<?php

require_once 'vendor/autoload.php';

use Slim\Slim;

$app = new Slim();

$db = new mysqli('localhost', "wall_e", "baymax", "tienda");

$app->get("/pruebas", function() use($app, $db) {
	echo "Hola mundo";
});

$app->post('/productos', function() use($app, $db) {
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	if (empty($data['nombre'])) {
		$data['nombre'] = NULL;
	}

	if (empty($data['descripcion'])) {
		$data['descripcion'] = NULL;
	}

	if (empty($data['precio'])) {
		$data['precio'] = NULL;
	}

	if (empty($data['imagen'])) {
		$data['imagen'] = NULL;
	}

	$query = "INSERT INTO productos VALUES (NULL, ".
				"'{$data['nombre']}',".
				"'{$data['descripcion']}',".
				"'{$data['precio']}',".
				"'{$data['imagen']}'".
			 ")";
	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Producto NO creado correctamente'
	);

	if ($insert) {
		$result = array(
				'status' => 'success',
				'code' => 200,
				'message' => 'Producto creado correctamente'
			);
	}
	echo json_encode($result);
});

$app->run();
