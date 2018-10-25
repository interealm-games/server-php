<?php

namespace InterealmGames\Server\Http\Slim;

use \Slim\Http\Request as SlimRequest;
use \interealmGames_server_http_RequestType as RequestType;

class Request implements \interealmGames_server_http_Request {
	protected $request;
	
	public static function convertMethod($name) {
		//$request->getMethod();
		$method = RequestType::$GET;
		switch(strtoupper($name)) {
			case 'DELETE':
				$method = RequestType::$DELETE;
				break;
			case 'GET':
				break;
			case 'PATCH':
				$method = RequestType::$PATCH;
				break;
			case 'POST':
				$method = RequestType::$POST;
				break;
			case 'PUT':
				$method = RequestType::$PUT;
				break;
		}
		return $method;
	}
	
	public function __construct(SlimRequest $slimRequest) {
		$this->request = $slimRequest;
	}
	
	public function getHeader($name) {
		return $this->request->getHeader($name);
	}
	
	public function getParam($name) {
		return $this->request->getParam($name);
	}
	
	public function getData(){
		//return $this->request->getParsedBody();//getParsedBody?
		$body = $this->request->getBody();
		return $body->getContents();//gets the contents as a string
	}
	
	public function getPathArgument($name) {
		$route = $this->request->getAttribute('route');
		return $route->getArgument($name);
	}
	
	public function getType() {
		return self::convertMethod($this->request->getMethod());
	}
	
	public function getUrl() {
		return "";
	}
}
