<?php

namespace Ms\Core\Interfaces;

interface ILogger
{
	public function addMessage (string $strMessage, array $arReplace = []);

	public function addMessageOtherType (string $type, string $strMessage, array $arReplace = []);
}