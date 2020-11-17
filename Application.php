<?php

namespace quocpp\phpmvc;

use quocpp\phpmvc\db\Database;

class Application
{
	const EVENT_BEFORE_REQUEST = 'beforeRequest';
	const EVENT_AFTER_REQUEST  = 'afterRequest';

	public static $ROOT_DIR;
	public static $app;
	public        $router;
	public        $request;
	public        $response;
	public        $session;
	public        $db;
	public        $layout = 'main';
	public        $user;
	public        $userClass;
	public        $controller;
	public        $view;

	protected $eventManagers = [];
	
	/**
	 *
	 * Application constructor.
	 * @param $rootPath
	 * @param  array  $config
	 */
	public function __construct(string $rootPath, array $config)
	{
		self::$ROOT_DIR = $rootPath;
		self::$app      = $this;
		$this->request      = new Request();
		$this->response     = new Response();
		$this->session      = new Session();
		$this->router       = new Router($this->request, $this->response);
		$this->controller   = new Controller();
		$this->view         = new View();

		$this->userClass    = $config['userClass'];
		$this->db           = new Database($config['db']);

		$primaryValue = $this->session->get('user');
		if (!empty($primaryValue)) {
			$primaryKey = $this->userClass::primaryKey();
			$this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
		} else {
			$this->user = null;
		}
	}

	public static function isGuest()
	{
		return !self::$app->user;
	}

	public function login(UserModel $user) {
		$this->user   = $user;
		$primaryKey   = $user->primaryKey();
		$primaryValue = $user->{$primaryKey};
		$this->session->set('user', $primaryValue);
		return true;
	}

	public function logout()
	{
		$this->user = null;
		$this->session->remove('user');
	}

	public function triggerEvent($eventName)
	{
		$callbacks = $this->eventManagers[$eventName] ?? [];
		foreach ($callbacks as $callback) {
			call_user_func($callback);
		}
	}
	
	public function run()
	{
		$this->triggerEvent(self::EVENT_BEFORE_REQUEST);
		try {
			echo $this->router->resolve();
		} catch (\Exception $e) {
			$this->response->setStatusCode($e->getCode());
			echo $this->view->renderView('_error', [
				'exception' => $e
			]);
		}
		
	}

	public function on($eventName, $callback)
	{
		$this->eventManagers[$eventName][] = $callback;
	}


}
