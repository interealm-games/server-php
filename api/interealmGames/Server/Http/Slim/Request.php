<?php

namespace InterealmGames\Server\Http\Slim;

use \Slim\Http\Request as SlimRequest;
use \Slim\Http\Reponse as SlimReponse;
use \interealmGames\server\http\RequestType as RequestType;
use InterealmGames\Haxe;

class Request implements \interealmGames\server\http\Request {
	protected $request;
	protected $response;
	
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
	
	public function __construct(SlimRequest $slimRequest, &$slimResponse) {
		$this->request = $slimRequest;
		$this->response = $slimResponse;
	}
	
	public function getCookie ($name) {
		return array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : "";
	}

	public function getHeader($name) {
		return Haxe::toHaxe($this->request->getHeader($name));
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

	public function getStatus() {
		return $this->response->getStatusCode();
	}
	
	public function getType() {
		return self::convertMethod($this->request->getMethod());
	}
	
	public function getUrl() {
		return "";
	}

	public function setHeader ($name, $value, $append = null) {
		if ( $append ) {
			$this->response = $this->response->withAddedHeader($name, $value);
		} else {
			$this->response = $this->response->withHeader($name, $value);
		}
	}

	public function setCookie ($name, $value, $options = null) {
		$options = Haxe::toPhp($options);
		
		$sess = setcookie(
			$name, 
			$value, 
			property_exists($options, 'expires') ? $options->expires : 0, 
			property_exists($options, 'path') ? $options->path : "", 
			property_exists($options, 'domain') ? $options->domain : "", 
			property_exists($options, 'secure') ? $options->secure : FALSE, 
			property_exists($options, 'httpOnly') ? $options->httpOnly : FALSE
		);
	}

	public function setStatus ($status) {
		$this->response = $this->response->withStatus($status);
	}
}
