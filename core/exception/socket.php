<?php

namespace Ms\Core\Exception;

use Ms\Core\Interfaces\ILogger;

class SocketException extends SystemException
{
	public function __construct ($file = "", $line = 0, $message = "", $code = 0, \Exception $previous = null)
	{
		if (
			$message == ''
			|| $code == 0
			|| $message == false
			|| $code == false
			|| is_null($message)
			|| is_null($code)
		) {
			$code = socket_last_error();
			$message = socket_strerror($code);
		}

		parent::__construct($message, $code, $file, $line, $previous);
	}

	public function addMessageToLog (ILogger $logger)
	{
		$logger->addMessage(
			'SOCKET ERROR [#CODE#]: #MESSAGE#',
			[
				'CODE'=>$this->getCode(),
				'MESSAGE'=>$this->getMessage()
			]
		);

		return $this;
	}
}