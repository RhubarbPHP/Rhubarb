<?php

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */

namespace Gcd\Core\Encryption\UnitTesting;

use Gcd\Core\Encryption\Aes256EncryptionProvider;

class UnitTestingAes256EncryptionProvider extends Aes256EncryptionProvider
{
	protected function GetEncryptionKey($keySalt = "")
	{
		return $keySalt."awXP!_£3s5f203 QSwgWpSEs=dSgasfda_Af2ASx";
	}
}