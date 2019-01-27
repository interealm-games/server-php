<?php

namespace InterealmGames\Server\Http\Slim;

use \interealmGames\server\http\RequestType as RequestType;

/**
 * I don't think I need this here
 * Mainly here for testing
 */

class RequestHandler{
	public $type;
	public $path;
	public $handler;
	
	
	public function __construct(RequestType $type, $path, $handler) {
		$this->type = $type;
		$this->path = $path;
		$this->handler = $handler;
	}
}
