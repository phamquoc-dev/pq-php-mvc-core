<?php
namespace app\core\form;

class InputField extends BaseField
{
	public const TYPE_TEXT     = 'text';
	public const TYPE_PASSWORD = 'password';
	public const TYPE_NUMBER   = 'number';
	public const TYPE_EMAIL   = 'email';
	public const TYPE_BUTTON_SUBMIT   = 'button_submit';

	public $type;

	public function __construct($model, $attribute)
	{
		parent::__construct($model, $attribute);
		$this->type = self::TYPE_TEXT;
	}

	public function passwordField() {
		$this->type = self::TYPE_PASSWORD;
		return $this;
	}
	
	public function emailField() {
		$this->type = self::TYPE_EMAIL;
		return $this;
	}

	public function renderInput(): string
	{
		return sprintf('<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="form-control %4$s">',
		               $this->type,
		               $this->attribute,
		               $this->model->{$this->attribute},
		               $this->model->hasError($this->attribute) ? ' is-invalid' : '',

		);
	}


}
