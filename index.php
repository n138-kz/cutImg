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

    $image['canvas'] = imagecreatetruecolor($image['imagesize'][0], $image['imagesize'][1]);
    imagefill($image['canvas'], 0, 0, imagecolorallocate($image['canvas'], 255, 255, 255));

    if (RETURN_IMAGE) {
        $image['export_name'] = time().'.png';
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="'.$image['export_name'].'"');
        imagepng( $image['canvas'], 'php://memory/'.$image['export_name'] );
        $size = filesize('php://memory/'.$image['export_name']);
        header('Content-Length: '.filesize('php://memory/'.$image['export_name']));
        #echo file_get_contents('php://memory/'.$image['export_name']);
    }
    imagepng( $image['canvas'], NULL );
    
} catch (\Throwable $th) {
    error_log($th->getMessage());
    header('Content-Type: image/png');
    putenv('GDFONTPATH=' . realpath('.'));
    imagettftext($image['canvas'], 9, 0, 0, 0, imagecolorallocate($image['canvas'], 0, 0, 0), 'SomeFont', $th->getMessage());
    imagepng( $image['canvas'] );
    exit();
}
#var_dump();
header('Content-Type: image/png');
putenv('GDFONTPATH=' . realpath('.'));
imagettftext($image['canvas'], 9, 0, 0, 0, imagecolorallocate($image['canvas'], 255, 0, 0), 'Arial.ttf', base64_encode('test'));
imagepng( $image['canvas'] );

exit();






$files_canvas_size=[ 0, 0 ];
if (FALSE) {
} elseif (FALSE) {
} elseif ( isset($files_before_1valid['cm']['tmp_name']) && isset($files_before_1valid['rm']['tmp_name'])) {
    $files_canvas_size = [
        sum($files_before_1valid['cm']['size_wh'][0], $files_before_1valid['rm']['size_wh'][0]),
        max($files_before_1valid['cm']['size_wh'][1], $files_before_1valid['rm']['size_wh'][1]),
    ];
    
    $files_canvas = imagecreatetruecolor($files_canvas_size[0], $files_canvas_size[1]);
    imagefill($files_canvas, 0, 0, imagecolorallocate($files_canvas, 255, 255, 255));
    imagecopy(
        $files_canvas,
        $files_before_1valid['cm']['raw_data'],
        0,
        0,
        0,
        0,
        $files_before_1valid['cm']['size_wh'][0],
        $files_before_1valid['cm']['size_wh'][1]
    );
    imagecopy(
        $files_canvas,
        $files_before_1valid['rm']['raw_data'],
        $files_before_1valid['cm']['size_wh'][0],
        0,
        0,
        0,
        $files_before_1valid['cm']['size_wh'][0],
        $files_before_1valid['cm']['size_wh'][1]
    );

} elseif ( isset($files_before_1valid['cm']['tmp_name']) && isset($files_before_1valid['cb']['tmp_name'])) {
    $files_canvas_size = [
        max($files_before_1valid['cm']['size_wh'][0], $files_before_1valid['cb']['size_wh'][0]),
        sum($files_before_1valid['cm']['size_wh'][1], $files_before_1valid['cb']['size_wh'][1]),
    ];
    
    $files_canvas = imagecreatetruecolor($files_canvas_size[0], $files_canvas_size[1]);
    imagefill($files_canvas, 0, 0, imagecolorallocate($files_canvas, 255, 255, 255));
    imagecopy(
        $files_canvas,
        $files_before_1valid['cm']['raw_data'],
        0,
        0,
        0,
        0,
        $files_before_1valid['cm']['size_wh'][0],
        $files_before_1valid['cm']['size_wh'][1]
    );
    imagecopy(
        $files_canvas,
        $files_before_1valid['cb']['raw_data'],
        0,
        $files_before_1valid['cm']['size_wh'][1],
        0,
        0,
        $files_before_1valid['cm']['size_wh'][0],
        $files_before_1valid['cm']['size_wh'][1]
    );

}


$item = [
    'lt', 'ct', 'rt',
    'lm', 'cm', 'rm',
    'lb', 'cb', 'rb',
];
foreach ($item as $key => $val) {
    if (!($files_before_1["error"][$val]!=0 || !isset($files_before_1["error"][$val]))) {
        // ob_start();
        // imagepng($files_before_1valid[$val]['raw_data'], NULL);
        // imagedestroy( $files_before_1valid[$val]['raw_data'] ); 
        // $files_before_1valid[$val]['raw_data'] = ob_get_clean();
        // https://9-bb.com/php-gd/
        $files_before_1valid[$val]['raw_data'] = NULL;
    }
}

$fp = fopen('access.log', 'a');
fwrite($fp, json_encode(['issued_at'=>time(),'request_by'=>$_SERVER['REMOTE_ADDR'],$files_before_1valid]) . PHP_EOL );
fclose($fp);

if (RETURN_IMAGE) {
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="'.time().'.png'.'"');
    imagepng( $files_canvas, 'php://memory/temp.png' );
    $size = filesize('php://memory/temp.png');
    header('Content-Length: '.filesize('php://memory/temp.png'));
    #echo file_get_contents('php://memory/temp.png');
}
imagepng( $files_canvas, NULL );
