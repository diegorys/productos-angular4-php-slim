<?php

require_once 'vendor/autoload.php';

use Slim\Slim;

// Configuración de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

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
				"descripcion = '{$data['descripcion']}',";

	if (isset($data['imagen'])) {
		$sql .= "imagen = '{$data['imagen']}', ";
	}
	$sql .= "precio = '{$data['precio']}' WHERE id = " . $id;
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not updated'
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
$app->post("/upload-file", function() use($app, $db) {
	$result = array(
		'status' => 'error',
		'code' => 500,
		'message' => 'Error uploading file'
	);

	if (isset($_FILES['uploads'])) {
		$piramideUploader = new PiramideUploader();
		$upload = $piramideUploader->upload('img', 'uploads', 'uploads',
							 array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];
		
		if (isset($upload) && $upload["uploaded"] == false) {
			$result = array(
				'status' => 'error',
				'code' => 400,
				'message' => 'Error uploading file'
			);
		} else {
			$result = array(
				'status' => 'success',
				'code' => 200,
				'filename' => $file_name,
				'message' => 'File uploaded'
			);
		}
	}

	echo json_encode($result);
});

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
