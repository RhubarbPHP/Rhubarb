<?php

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */

namespace Rhubarb\Crown\Encryption\UnitTesting;

use Rhubarb\Crown\Encryption\Aes256EncryptionProvider;

class UnitTestingAes256EncryptionProvider extends Aes256EncryptionProvider
{
	protected function GetEncryptionKey($keySalt = "")
	{
		return $keySalt."awXP!_£3s5f203 QSwgWpSEs=dSgasfda_Af2ASx";
	}
}