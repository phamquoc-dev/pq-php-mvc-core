<?php


namespace app\core;


class Session
{
	protected const FLASH_KEY = 'flash_messages';
	/**
	 * Session constructor.
	 */
	public function __construct()
	{
		session_start();
		$flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
		foreach ($flashMessages as $key => &$flashMessage) {
			// Mark to be removed
			$flashMessage['removed'] = true;
		}
		$_SESSION[self::FLASH_KEY] = $flashMessages;
	}
	
	public function setFlash($key, $message)
	{
		$_SESSION[self::FLASH_KEY][$key] = [
			'removed' => false,
			'value'   => $message
		];
	}
	
	public function getFlash($key)
	{
		return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
	}

	/**
	 * set session by key and value
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	 * get session
	 * @param $key
	 * @return false|mixed
	 */
	public function get($key)
	{
		return $_SESSION[$key] ?? false;
	}

	/**
	 * remove session by key
	 * @param $key
	 */
	public function remove($key)
	{
		unset($_SESSION[$key]);
	}
	
	public function __destruct()
	{
		$flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
		foreach ($flashMessages as $key => &$flashMessage) {
			if ($flashMessage['removed']) {
				unset($flashMessages[$key]);
			}
		}
		$_SESSION[self::FLASH_KEY] = $flashMessages;
	}
}
