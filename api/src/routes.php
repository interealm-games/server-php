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

$requestHandlers = require($environmentPath.getenv('REQUEST_HANDLERS_PATH'));
foreach($requestHandlers as $requestHandler) {
	$method = strtolower($requestHandler->type->tag);
	$handler = $requestHandler->handler;
	$app->$method(
		$requestHandler->path, 
		function (Request $request, Response $response, array $args) use ($handler) {
			$serverRequest = new \InterealmGames\Server\Http\Slim\Request($request);
			return $response->withJson($handler($serverRequest));
		}
	);
}

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
	
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



