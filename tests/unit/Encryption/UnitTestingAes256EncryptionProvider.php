<?php

namespace Rhubarb\Crown\Tests\unit\Encryption;

use Rhubarb\Crown\Encryption\Aes256EncryptionProvider;

class UnitTestingAes256EncryptionProvider extends Aes256EncryptionProvider
{
    protected function getEncryptionKey($keySalt = "")
    {
        return $keySalt . "awXP!_£3s5f203 QSwgWpSEs=dSgasfda_Af2ASx";
    }
}