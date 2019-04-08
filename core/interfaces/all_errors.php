<?php

namespace Ms\Core\Interfaces;

interface AllErrors
{
	public static function getError ($iErrorCode, $arReplace=array());

	public static function getErrorTextByCode ($iErrorCode,$arReplace=array());
}