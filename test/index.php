<?php

use \InterealmGames\Server\Http\Slim\RequestHandler;
use \interealmGames_server_http_Request as Request;
use \interealmGames_server_http_RequestType as RequestType;

$handlers = [];

$handlers[] = new RequestHandler(RequestType::$GET, "/maps[/]", function(Request $request){
	return ['pork'];
});

$handlers[] = new RequestHandler(RequestType::$POST, "/maps[/]", function(Request $request){
	$data = json_decode($request->getData(), true);
	return $data;
});

return $handlers;