<?php


namespace app\core\form;


use app\core\Model;

abstract class BaseField
{
	abstract public function renderInput(): string;

	public $model;
	public $attribute;

	/**
	 * Field constructor.
	 * @param $model
	 * @param $attribute
	 */
	public function __construct(Model $model, $attribute)
	{
		$this->model     = $model;
		$this->attribute = $attribute;
	}

	public function __toString()
	{
		return sprintf(
			'
			<div class="form-group">
				<label for="%1$s">%2$s</label>
				%3$s
				<div class="invalid-feedback">
					%4$s
				</div>
			</div>
		',
			$this->attribute,
			$this->model->getLabel($this->attribute),
			$this->renderInput(),
			$this->model->getFirstError($this->attribute)
		);
	}
}