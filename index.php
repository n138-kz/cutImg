<?php session_start();
require_once './vendor/autoload.php';
require_once('./lib/Discode_push_class.php');

$config=json_decode(file_get_contents(__DIR__.'/.secret/config.json'), TRUE);

$discord = new discord();
$discord->endpoint = $config['external']['discord']['endpoint']['url'];

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
            'return_image' => FALSE,
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
function sum($param1, $param2) {
    return $param1+$param2;
}
ini_set('upload_max_filesize', '25M');
ini_set('post_max_size', '100M');
header('Content-Type: text/plain');
$exitStatus = new n138();
$exitStatus->setVal('time', time());
$exitStatus->setVal('remote', ['address'=>$_SERVER['REMOTE_ADDR']]);
$json_encode_option = 0;
if( isset($_SERVER['HTTP_X_SCRIPT_DEBUG']) ){
	$exitStatus->setVal('debug', (bool)($_SERVER['HTTP_X_SCRIPT_DEBUG']));
	$json_encode_option = JSON_PRETTY_PRINT;
}
define('DEBUG', $exitStatus->getVal('debug'));

if( isset($_SERVER['HTTP_X_RETURN_IMAGE']) ){
    $exitStatus->setVal('return_image', (bool)($_SERVER['HTTP_X_RETURN_IMAGE']));
}
define('RETURN_IMAGE', $exitStatus->getVal('return_image'));

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

if ( !isset($_FILES['image']) || !is_array($_FILES['image']) ) {
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

if ( !isset($_FILES['image']["name"]) || mb_strlen($_FILES['image']["tmp_name"])==0 || $_FILES['image']["size"]==0 || $_FILES['image']["error"]!=UPLOAD_ERR_OK ) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.'.__LINE__);
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
    $image['imagesize'] = getimagesize($_FILES['image']['tmp_name']);

    $image['position'] = [ 0, 0, 0, 0 ];
    if ( !isset($_POST['position_0x']) || (int)$_POST['position_0x']<0 || (int)$_POST['position_0x']>$image['imagesize'][0] ) {
        $image['position'][0] = 0;
    } else {
        $image['position'][0] = (int)$_POST['position_0x'];
    }
    if ( !isset($_POST['position_0y']) || (int)$_POST['position_0y']<0 || (int)$_POST['position_0y']>$image['imagesize'][1] ) {
        $image['position'][1] = 0;
    } else {
        $image['position'][1] = (int)$_POST['position_0y'];
    }
    if ( !isset($_POST['position_1x']) || (int)$_POST['position_1x']<0 || (int)$_POST['position_1x']>$image['imagesize'][0] ) {
        $image['position'][2] = (int)$image['imagesize'][0];
    } else {
        $image['position'][2] = (int)$_POST['position_1x'];
    }
    if ( !isset($_POST['position_1y']) || (int)$_POST['position_1y']<0 || (int)$_POST['position_1y']>$image['imagesize'][1] ) {
        $image['position'][3] = (int)$image['imagesize'][1];
    } else {
        $image['position'][3] = (int)$_POST['position_1y'];
    }
    
    switch ($image['imagesize'][2]) {
        case IMAGETYPE_JPEG:
            $image['raw_data'] = imagecreatefromjpeg($_FILES['image']['tmp_name']);
            $image['imagetype'] = 'JPEG';
            break;
        case IMAGETYPE_PNG:
            $image['raw_data'] = imagecreatefrompng($_FILES['image']['tmp_name']);
            $image['imagetype'] = 'PNG';
            break;
        case IMAGETYPE_GIF:
            $image['raw_data'] = imagecreatefromgif($_FILES['image']['tmp_name']);
            $image['imagetype'] = 'GIF';
            break;
        default:
            break;
    }

    $image['canvassize'] = [
        min($image['imagesize'][0], $image['position'][2]),
        min($image['imagesize'][1], $image['position'][3]),
    ];

    $image['canvas'] = imagecreatetruecolor($image['canvassize'][0], $image['canvassize'][1]);
    imagefill($image['canvas'], 0, 0, imagecolorallocate($image['canvas'], 255, 255, 255));

    imagecopy(
        $image['canvas'],
        $image['raw_data'],
        0,
        0,
        $image['position'][0],
        $image['position'][1],
        $image['position'][2],
        $image['position'][3]
    );

    $discord->setValue('content', json_encode([
        $image['position'],
        $image['canvassize'],
        $image['imagesize'],
    ]));$discord->exec_curl();

    if (RETURN_IMAGE) {
        $image['export']['name'] = time().'.png';
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="'.$image['export']['name'].'"');
        imagepng( $image['canvas'], 'php://memory/'.$image['export']['name'] );

        $image['export']['size'] = filesize('php://memory/'.$image['export']['name']);
        header('Content-Length: '.filesize('php://memory/'.$image['export']['name']));
        #echo file_get_contents('php://memory/'.$image['export']['name']);
    } else {
        header('Content-Type: image/png');
        imagepng( $image['canvas'], NULL );
    }
    
} catch (\Throwable $th) {
    error_log($th->getMessage());
    header('Content-Type: image/png');
    putenv('GDFONTPATH=' . realpath('.'));
    imagettftext($image['canvas'], 9, 0, 0, 0, imagecolorallocate($image['canvas'], 0, 0, 0), 'Arial.ttf', $th->getMessage());
    imagepng( $image['canvas'] );
    exit();
}

exit();
