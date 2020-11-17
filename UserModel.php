<?php


namespace quocpp\phpmvc;


use quocpp\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
	abstract public function getDisplayName(): string;
}