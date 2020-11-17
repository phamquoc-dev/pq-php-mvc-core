<?php
namespace quocpp\phpmvc;

class Response
{
	/**
	 * @param  int  $code
	 */
	public function setStatusCode(int $code)
	{
		http_response_code($code);
	}
	
	public function redirect(string $url)
	{
		header('Location: '. $url);
	}
	
}
