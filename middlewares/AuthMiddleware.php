<?php


namespace quocpp\phpmvc\middlewares;


use quocpp\phpmvc\Application;
use quocpp\phpmvc\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
	protected $actions = [];
	
	/**
	 * AuthMiddleware constructor.
	 * @param  array  $actions
	 */
	public function __construct(array $actions) { $this->actions = $actions; }
	
	
	public function execute()
	{
		if (Application::isGuest()) {
			if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
				throw new ForbiddenException();
			}
		}
	}
	
}
