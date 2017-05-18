<?php

require_once 'vendor/autoload.php';

use Slim\Slim;

$app = new Slim();

$db = new mysqli('localhost', "wall_e", "baymax", "tienda");

$app->get("/pruebas", function() use($app, $db) {
	echo "Hola mundo";
});

// LISTAR TODOS LOS PRODUCTOS
$app->get("/productos", function() use($app, $db) {
	$sql = 'SELECT * FROM productos ORDER BY id DESC';
	$query = $db->query($sql);
	$productos = array();
	while ($producto = $query->fetch_assoc()) {
		$productos[] = $producto;
	}
	$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $productos
		);
	echo json_encode($result);
});

// DEVOLVER UN SOLO PRODUCTO
$app->get("/producto/:id", function($id) use($app, $db) {
	$sql = 'SELECT * FROM productos WHERE id=' . $id . ' ORDER BY id DESC';
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not found'
	);

	if ($query->num_rows == 1) {
		$producto = $query->fetch_assoc();
		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $producto
		);
	}
	
	echo json_encode($result);
});

// ELIMINAR UN PRODUCTO
$app->get("/producto/:id/delete", function($id) use($app, $db) {
	$sql = 'DELETE FROM productos WHERE id=' . $id;
	$query = $db->query($sql);
	echo $query;

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not found'
	);

	if ($query && mysqli_affected_rows($db)) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => "Product deleted"
		);
	}
	
	echo json_encode($result);
});

// ACTUALIZAR UN PRODUCTO
$app->post("/producto/:id/update", function($id) use($app, $db) {
	$json = $app->request->post('json');
	$data = json_decode($json, true);
	$sql = "UPDATE productos SET ".
				"nombre = '{$data['nombre']}',".
				"descripcion = '{$data['descripcion']}',".
				"precio = '{$data['precio']}'
			WHERE id = " . $id . "
	";
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not found'
	);

	if ($query && mysqli_affected_rows($db)) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => "Product updated"
		);
	}
	echo json_encode($result);
});

// SUBIR UNA IMAGEN A UN PRODUCTO
/*$app->post("/producto", function() use($app, $db) {
	echo "Subir imagen a un producto";
});*/

// GUARDAR PRODUCTOS
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
