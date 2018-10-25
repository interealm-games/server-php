<?php

namespace InterealmGames\Server\Http\Slim;

use \interealmGames_server_http_RequestType as RequestType;

/**
 * I don't think I need this here
 * Mainly here for testing
 */

class RequestHandler implements \interealmGames_server_http_RequestHandler {
	protected $type;
	protected $path;
	protected $handler;
	
	
	public function __construct(RequestType $type, $path, $handler) {
		$this->type = $type;
		$this->path = $path;
		$this->handler = $handler;
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function getType() {
		return $this->type;
	}
}
