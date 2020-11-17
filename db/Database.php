<?php


namespace quocpp\phpmvc\db;


use quocpp\phpmvc\Application;

class Database
{
	public $pdo;
	
	
	/**
	 * Database constructor.
	 * @param  array  $config
	 */
	public function __construct(array $config)
	{
		$dsn      = $config['dsn'] ?? '';
		$user     = $config['user'] ?? '';
		$password = $config['password'] ?? '';
		$this->pdo = new \PDO($dsn, $user, $password);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	/**
	 * apply migration
	 */
	public function applyMigration()
	{
		$this->createMigrationsTable();
		$appliedMigrations = $this->getAppliedMigrations();
		
		$files = scandir(Application::$ROOT_DIR . '/migrations');
		$toApplyMigrations = array_diff($files, $appliedMigrations);
		$newMigrations = [];
		
		foreach($toApplyMigrations as $migration) {
			if ($migration === '.' || $migration === '..' ) {
				continue;
			}
			require_once Application::$ROOT_DIR . '/migrations/'. $migration;
			$className = pathinfo($migration, PATHINFO_FILENAME);
			$instance = new $className();
			$this->log("Applying migration $migration" . PHP_EOL);
			$instance->up();
			$this->log("Applied migration $migration" . PHP_EOL);
			$newMigrations[] = $migration;
		}
		
		if (!empty($newMigrations)) {
			$this->saveMigrations($newMigrations);
		} else {
			$this->log('All migrations are applied '. PHP_EOL);
		}
		
	}
	
	/**
	 * create migration table
	 */
	public function createMigrationsTable()
	{
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
	}
	
	/**
	 * get applied migrations
	 * @return array
	 */
	public function getAppliedMigrations()
	
	{
		$statement = $this->pdo->prepare("SELECT migration FROM migrations");
		$statement->execute();
		return $statement->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * @param  array  $migrations
	 */
	public  function saveMigrations(array $migrations)
	{
		$sValue = implode(',', array_map(function ($m){return "('$m')"; }, $migrations));
		$statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $sValue ");
		$statement->execute();
	}
	
	/**
	 * @param  string  $message
	 */
	private function log(string $message)
	{
		echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
	}
	
	/**
	 * @param  string  $sql
	 * @return bool|\PDOStatement
	 */
	public function prepare(string $sql)
	{
		return $this->pdo->prepare($sql);
	}
	
	
}
