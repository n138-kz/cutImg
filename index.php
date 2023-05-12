<?php session_start();
require_once './vendor/autoload.php';

class n138 {
	private $exit_params;
	function __construct(){
		$this->exit_params = [
			'time' => time(),
			'text' => '',
			'code' => 0,
			'remote' => [
				'address' => '',
			],
			'debug' => FALSE,
		];
	}
	function getExitStatus() {
		$this->setVal('http', http_response_code());
		return $this->exit_params;
	}
	function getVal($key) {
		return $this->exit_params[$key];
	}
	function setVal($key, $val) {
		$this->exit_params[$key] = $val;
	}
}
ini_set('upload_max_filesize', '25M');
ini_set('post_max_size', '100M');
header('Content-Type: Application/json');
$exitStatus = new n138();
$exitStatus->setVal('time', time());
$exitStatus->setVal('remote', ['address'=>$_SERVER['REMOTE_ADDR']]);
$json_encode_option = 0;
if( isset($_SERVER['HTTP_X_SCRIPT_DEBUG']) ){
	$exitStatus->setVal('debug', (bool)($_SERVER['HTTP_X_SCRIPT_DEBUG']));
	$json_encode_option = JSON_PRETTY_PRINT;
}
define('DEBUG', $exitStatus->getVal('debug'));

if( mb_strtolower($_SERVER['REQUEST_METHOD']) != 'post' ){
	http_response_code(405);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Method Not Allowed.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_POST) || !is_array($_POST) ) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_FILES['image']) ) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_FILES['image']['error']) || $_FILES['image']['error'] != 0 ) {
	http_response_code(500);
	$exitStatus->setVal('time', time());
	switch ($_FILES['image']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_INI_SIZE:
			$exitStatus->setVal('text', 'UPLOAD_ERR_INI_SIZE');
			break;
		case UPLOAD_ERR_FORM_SIZE:
			$exitStatus->setVal('text', 'UPLOAD_ERR_FORM_SIZE');
			break;
		case UPLOAD_ERR_PARTIAL:
			$exitStatus->setVal('text', 'UPLOAD_ERR_PARTIAL');
			break;
		case UPLOAD_ERR_NO_FILE:
			$exitStatus->setVal('text', 'UPLOAD_ERR_NO_FILE');
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$exitStatus->setVal('text', 'UPLOAD_ERR_NO_TMP_DIR');
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$exitStatus->setVal('text', 'UPLOAD_ERR_CANT_WRITE');
			break;
		case UPLOAD_ERR_EXTENSION:
			$exitStatus->setVal('text', 'UPLOAD_ERR_EXTENSION');
			break;
		default:
			$exitStatus->setVal('text', 'File error.');
			break;
	}
	$exitStatus->setVal('text', $exitStatus->getVal('text') . ' ');
	$exitStatus->setVal('text', $exitStatus->getVal('text') . 'Error-code: ' . $_FILES['image']['error']);
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_FILES['image']['error']) || $_FILES['image']['size'] == 0 ) {
	http_response_code(500);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'File error');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !file_exists($_FILES['image']['tmp_name']) ) {
	http_response_code(500);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

try {
	list($image_meta['size']['src_w'], $image_meta['size']['src_h'], $image_meta['type']) = getimagesize($_FILES['image']['tmp_name']);
	$image_meta['size']['dst_w'] = $image_meta['size']['src_w'] *0.8;
	$image_meta['size']['dst_h'] = $image_meta['size']['src_h'] *0.8;
	$image_meta['size']['b'] = $_FILES['image']['size'];
	$image_meta['size']['kb'] = $image_meta['size']['b']/1000;
	$image_meta['size']['mb'] = $image_meta['size']['kb']/1000;
	$baseImage = NULL;
	switch ($image_meta['type']) {
		case IMAGETYPE_JPEG:
			$baseImage = imagecreatefromjpeg($_FILES['image']['tmp_name']);
			break;
		case IMAGETYPE_PNG:
			$baseImage = imagecreatefrompng($_FILES['image']['tmp_name']);
			break;
		case IMAGETYPE_GIF:
			$baseImage = imagecreatefromgif($_FILES['image']['tmp_name']);
			break;
		default:
			throw new ErrorException('Out of format.');
			break;
	}
	$image = imagecreatetruecolor($image_meta['size']['dst_w'], $image_meta['size']['dst_h']);
	imagecopyresampled(
		$image,
		$baseImage,
		0,
		0,
		0,
		0,
		$image_meta['size']['dst_w'],
		$image_meta['size']['dst_h'],
		$image_meta['size']['src_w'],
		$image_meta['size']['src_h']
	);

	error_log(json_encode($exitStatus->getExitStatus()));

	header('Content-Type: image/png');
	imagepng($image);
	imagedestroy($baseImage);
	imagedestroy($image);

} catch (Exception $e) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', $e->getMessage());
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}
