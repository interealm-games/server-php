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

function convertHaxeValues($value) {
	$converted = $value;
	if(!is_bool($value) && !is_numeric($value) && !is_string($value) && $value !== null) {
		switch(get_class($value)) {
			case '_hx_array' :
				$converted = array_map('convertHaxeValues', $value->a);
				break;
			case '_hx_anonymous' :
				//$converted = array_map('convertHaxeValues', $value->a);
				$converted = [];
				foreach($value as $k => $v) {
					$converted[$k] = convertHaxeValues($v);
				}
				break;
		}
	}
	return $converted;
}

$requestHandlers = require($environmentPath.getenv('REQUEST_HANDLERS_PATH'));
foreach($requestHandlers as $requestHandler) {
	$method = strtolower($requestHandler->type->tag);
	$handler = $requestHandler->handler;
	$app->$method(
		$requestHandler->path, 
		function (Request $request, Response $response, array $args) use ($handler) {
			$serverRequest = new \InterealmGames\Server\Http\Slim\Request($request);
			$value = $handler($serverRequest);
			
			$output = convertHaxeValues($value);
			return $response->withJson($output);
		}
	);
}

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
	
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



