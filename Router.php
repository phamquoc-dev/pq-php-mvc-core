<?php

namespace quocpp\phpmvc;

use quocpp\phpmvc\exception\ForbiddenException;
use quocpp\phpmvc\exception\NotFoundException;

class Router
{
	protected $routes = [];
	protected $request;
	protected $response;
	
	/**
	 * Router constructor.
	 * @param  Request  $request
	 * @param  Response  $response
	 */
	public function __construct(Request $request, Response $response)
	{
		$this->request  = $request;
		$this->response = $response;
	}
	
	public function get($path, $callback)
	{
		$this->routes['get'][$path] = $callback;
	}
	
	public function post($path, $callback)
	{
		$this->routes['post'][$path] = $callback;
	}
	
	public function resolve()
	{
		$path     = $this->request->getPath();
		$method   = $this->request->method();
		$callback = $this->routes[$method][$path] ?? false;
		if ($callback === false) {
			throw new NotFoundException();
		}
		
		if (is_string($callback)) {
			return Application::$app->view->renderView($callback);
		}
		
		/**
		 * @var Controller $controller
		 */
		if (is_array($callback)) {
			$controller = new $callback[0]();
			$controller->action = $callback[1];
			Application::$app->controller = $controller;
			$middlewares = $controller->getMiddlewares();
			foreach ($middlewares as $middleware) {
				$middleware->execute();
			}
			$callback[0] = $controller;
		}
		
		return call_user_func($callback, $this->request, $this->response);
	}
	
}
