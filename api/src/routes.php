<?php

use Slim\Http\Request;
use Slim\Http\Response;
use InterealmGames\Haxe;

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


$filepaths = explode(':', getenv('REQUEST_HANDLERS_PATH'));
$requestHandlers = [];
foreach($filepaths as $filepath) {
	$requestHandlers = 
		array_merge(
			$requestHandlers, 
			(array)Haxe::toPhp(require($environmentPath . $filepath))
		);
}

foreach($requestHandlers as $requestHandler) {
	$method = strtolower($requestHandler->type->tag);
	$handler = $requestHandler->handler;
	//echo "{$requestHandler->path}\t{$method}\n";
	$app->$method(
		$requestHandler->path, 
		function (Request $request, Response $response, array $args) use ($handler, $requestHandler) {
			$serverRequest = new \InterealmGames\Server\Http\Slim\Request($request, $response);
			//var_dump($request->getCookieParams()->get('access_token'));
			//var_dump($_COOKIE);
			try{
				$value = $handler($serverRequest);
				//var_dump($value);
			} catch (Exception $error) {
				$errorMessage = "An unknown error occured.";
				$errorStatus = 500;
				if($error->getMessage() == "[object interealmGames.server.http.Error]") {
					$errorStatus = (int) $error->e->status;
					$errorMessage = (string) $error->e->message;
				} else {
					$errorMessage = $error->getFile() . "\t" . $error->getLine() . "\t" . $error->getMessage();
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
			
			$output = Haxe::toPhp($value);
			
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



