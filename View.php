<?php


namespace quocpp\phpmvc;


class View
{
	public $title = '';
	
	public function renderView(string $view, $params = [])
	{
		$viewContent   = $this->renderOnlyView($view, $params);
		$layoutContent = $this->layoutContent();
		return str_replace('{{content}}', $viewContent, $layoutContent);
	}
	
	public function renderContent(string $viewContent)
	{
		$layoutContent = $this->layoutContent();
		return str_replace('{{content}}', $viewContent, $layoutContent);
	}
	
	public function layoutContent()
	{
		$layout = Application::$app->layout;
		ob_start();
		include_once Application::$ROOT_DIR."/views/layouts/$layout.php";
		return ob_get_clean();
	}
	
	public function renderOnlyView($view, $params)
	{
		extract($params);
		ob_start();
		include_once Application::$ROOT_DIR."/views/$view.php";
		return ob_get_clean();
	}
}
