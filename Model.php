<?php


namespace quocpp\phpmvc;


abstract class Model
{
	public const RULE_REQUIRED = 'required';
	public const RULE_EMAIL    = 'email';
	public const RULE_MIN      = 'min';
	public const RULE_MAX      = 'max';
	public const RULE_MATCH    = 'match';
	public const RULE_UNIQUE   = 'unique';
	
	public $errors = [];
	
	public function loadData(array $data)
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}
	
	abstract public function rules(): array;
	
	public function labels()
	{
		return [];
	}
	
	public function getLabel($attribute)
	{
		return $this->labels()[$attribute] ?? $attribute;
	}
	
	/**
	 * function validate data
	 * @return bool
	 */
	public function validate()
	{
		foreach ($this->rules() as $attribute => $rules) {
			$value = $this->{$attribute};
			foreach ($rules as $rule) {
				$ruleName = $rule;
				if ( ! is_string($ruleName)) {
					$ruleName = $rule[0];
				}
				if ($ruleName === self::RULE_REQUIRED && ! $value) {
					$this->addErrorByRule($attribute, self::RULE_REQUIRED);
				}
				
				if ($ruleName === self::RULE_EMAIL && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$this->addErrorByRule($attribute, self::RULE_EMAIL);
				}
				
				if ($ruleName === self::RULE_MIN && mb_strlen($value) < $rule['min']) {
					$this->addErrorByRule($attribute, self::RULE_MIN, ['min' => $rule['min']]);
				}
				
				if ($ruleName === self::RULE_MAX && mb_strlen($value) > $rule['max']) {
					$this->addErrorByRule($attribute, self::RULE_MAX, ['max' => $rule['max']]);
				}
				
				if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
					$this->addErrorByRule($attribute, self::RULE_MATCH, ['match' => $this->getLabel($rule['match'])]);
				}
				
				if ($ruleName === self::RULE_UNIQUE) {
					$className  = $rule['class'];
					$uniqueAttr = $rule['attribute'] ?? $attribute;
					$tableName  = $className::tableName();
					$statement  = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr ");
					$statement->bindValue(":attr", $this->{$uniqueAttr});
					$statement->execute();
					$record = $statement->fetchObject();
					if ($record) {
						$this->addErrorByRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute) ]);
					}
				}
				
			}
		}
		return empty($this->errors);
	}
	
	/**
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array  $params
	 */
	private function addErrorByRule(string $attribute, string $rule, $params = [])
	{
		$message = $this->errorMessages()[$rule] ?? '';
		if ( ! empty($params)) {
			foreach ($params as $key => $value) {
				$message = str_replace("{{$key}}", $value, $message);
			}
		}
		$this->errors[$attribute][] = $message;
	}
	
	/**
	 * @param  string  $attribute
	 * @param  string  $message
	 */
	public function addError(string $attribute, string $message)
	{
		$this->errors[$attribute][] = $message;
	}
	
	/**
	 * define validate message
	 * @return string[]
	 */
	public function errorMessages()
	{
		return [
			self::RULE_REQUIRED => 'This field is required',
			self::RULE_EMAIL    => 'This field must be valid email address',
			self::RULE_MIN      => 'Min length of this field must be {min}',
			self::RULE_MAX      => 'Max length of this field must be {max}',
			self::RULE_MATCH    => 'This field must be the same as {match}',
			self::RULE_UNIQUE   => 'Record with with this {field} already exists',
		];
	}
	
	/**
	 * check attribute has error
	 * @param $attribute
	 * @return false|mixed
	 */
	public function hasError($attribute)
	{
		return $this->errors[$attribute] ?? false;
	}
	
	/**
	 * get first error show on UI
	 * @param $attribute
	 * @return false|mixed
	 */
	public function getFirstError($attribute)
	{
		return $this->errors[$attribute][0] ?? false;
	}
	
	
}
