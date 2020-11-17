<?php


namespace app\core\db;


use app\core\Application;
use app\core\Model;

abstract class DbModel extends Model
{
	abstract public static function tableName(): string;
	
	abstract public function attributes(): array;

	abstract public static function primaryKey(): string;
	
	public function save()
	{
		$tableName  = $this->tableName();
		$attributes = $this->attributes();
		$params     = array_map(
			function ($attr) {
				return ":$attr";
			}, $attributes);
		
		$sSql      = sprintf(
			'INSERT INTO %1$s (%2$s) VALUES (%3$s)',
			$tableName, implode(',', $attributes), implode(',', $params)
		);
		$statement = self::prepare($sSql);
		
		foreach ($attributes as $attribute) {
			$statement->bindValue(":$attribute", $this->{$attribute});
		}
		$statement->execute();
		return true;
	}

	/**
	 * get one item with conditions
	 * @param $where
	 */
	public static function findOne(array $where = [])
	{
		$tableName = static::tableName();
		$sSQL = sprintf('SELECT * FROM %1$s ', $tableName);

		if (!empty($where)) {
			$attributes = array_keys($where);
			$aConds     = array_map(function ($attr) {
				return "$attr = :$attr";
			}, $attributes);
			$sSQL .= ' WHERE '. implode('AND ', $aConds);
		}

		$statement = self::prepare($sSQL);
		foreach ($where as $key =>  $item) {
			$statement->bindValue(":$key", $item);
		}
		$statement->execute();
		return $statement->fetchObject(static::class);
	}
	
	public static function prepare($sql)
	{
		return Application::$app->db->pdo->prepare($sql);
	}
}
