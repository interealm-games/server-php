<?php

use Slim\Http\Request;
use Slim\Http\Response;

$environmentPath = 
	__DIR__.DIRECTORY_SEPARATOR.".."
		.DIRECTORY_SEPARATOR.".."
		.DIRECTORY_SEPARATOR."environments".DIRECTORY_SEPARATOR;

$dotenv = new \Dotenv\Dotenv($environmentPath);
$dotenv->load();


// Routes
/*
typedef RequestHandler = 
{
	var handler:Request -> Any;
	var path:String; 
	var type:RequestType;
}
//*/

function convertHaxeValues3($value) {
	$converted = $value;
	if(!is_bool($value) && !is_numeric($value) && !is_string($value) && $value !== null) {
		switch(get_class($value)) {
			case '_hx_array' :
				$converted = array_map('convertHaxeValues3', $value->a);
				break;
			case '_hx_anonymous' :
				//$converted = array_map('convertHaxeValues', $value->a);
				$converted = [];
				foreach($value as $k => $v) {
					$converted[$k] = convertHaxeValues3($v);
				}
				break;
		}
	}
	return $converted;
}

function convertHaxeValues4($value) {
	$converted = $value;
	
	if(!is_bool($value) && !is_numeric($value) && !is_string($value) && !is_callable($value) && $value !== null) {
		if(is_array($value)) {
			if(array_key_exists('arr', $value)) {
				$converted = array_map('convertHaxeValues4', $value['arr']);
			}
		} else {
			switch(get_class($value)) {
				case 'Array_hx' :
					$converted = array_map('convertHaxeValues4', $value->arr);
					break;
				//case 'php\_Boot\HxAnon' :
				default :
					if(property_exists($value, 'arr')) {
						$converted = array_map('convertHaxeValues4', $value->arr);
					} else {
						//$converted = array_map('convertHaxeValues', $value->a);
						$converted = new stdClass();
						foreach($value as $k => $v) {
							$converted->$k = convertHaxeValues4($v);
						}
					}
					break;
			}
		}
	}
	return $converted;
}

$filepaths = explode(':', getenv('REQUEST_HANDLERS_PATH'));
$requestHandlers = [];
foreach($filepaths as $filepath) {
	$requestHandlers = 
		array_merge(
			$requestHandlers, 
			(array)convertHaxeValues4(require($environmentPath . $filepath))
		);
}

foreach($requestHandlers as $requestHandler) {
	$method = strtolower($requestHandler->type->tag);
	$handler = $requestHandler->handler;
	
	$app->$method(
		$requestHandler->path, 
		function (Request $request, Response $response, array $args) use ($handler, $requestHandler) {
			$serverRequest = new \InterealmGames\Server\Http\Slim\Request($request);
			
			try{
				$value = $handler($serverRequest);
			} catch (Exception $error) {
				$errorMessage = "An unknown error occured.";
				$errorStatus = 500;
				if($error->getMessage() == "[object interealmGames.server.http.Error]") {
					$errorStatus = (int) $error->e->status;
					$errorMessage = (string) $error->e->message;
				}

				if($errorStatus > 299 && $errorStatus < 400) {
					return $response->withRedirect($errorMessage);
				} else {
					$data = [
						'status' => $errorStatus < 300,
						'message' => $errorMessage,
					];
					
					return $response->withStatus($errorStatus, $errorMessage)->withJson($data);
				}
			}
			
			$output = convertHaxeValues4($value);
			return $response->withJson($output);
			
			return $response->withJson(['test' => true]);
		}
	);
}

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
	
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



