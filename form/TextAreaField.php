<?php


namespace app\core\form;


class TextAreaField extends BaseField
{
	public function renderInput(): string
	{
		return sprintf('<textarea class="form-control %1$s" id="%2$s"  name="%2$s">%3$s</textarea>',
		               $this->model->hasError($this->attribute) ? ' is-invalid' : '',
		               $this->attribute,
		               $this->model->{$this->attribute},
		);
	}

}