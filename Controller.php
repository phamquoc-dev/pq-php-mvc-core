<?php


namespace quocpp\phpmvc;


use quocpp\phpmvc\middlewares\BaseMiddleware;

class Controller
{
	public $layout = 'main';
	public $action = '';

	/**
	 * @var BaseMiddleware[]
	 */
	protected $middlewares = [];

	public function render($view, $params = [])
	{
		return Application::$app->view->renderView($view, $params);
	}
	
	/**
	 * redirect url
	 * @param $url
	 */
	public function redirect(string $url) {
		Application::$app->response->redirect($url);
	}
	
	/**
	 * set flash message
	 * @param $key
	 * @param $message
	 */
	public function setFlash($key, $message)
	{
		Application::$app->session->setFlash($key, $message);
	}
	
	public function setLayout($layout) {
		Application::$app->layout = $layout;
	}

	public function registerMiddleware(BaseMiddleware $middleware)
	{
		$this->middlewares[] = $middleware;
	}
	
	/**
	 * @return BaseMiddleware[]
	 */
	public function getMiddlewares(): array
	{
		return $this->middlewares;
	}
	
	/**
	 * @param  BaseMiddleware[]  $middlewares
	 */
	public function setMiddlewares(array $middlewares): void
	{
		$this->middlewares = $middlewares;
	}
	
	
}
