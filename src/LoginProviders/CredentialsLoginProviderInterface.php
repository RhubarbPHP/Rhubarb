<?php

namespace Rhubarb\Crown\LoginProviders;

/**
 * Provides a pattern of login provider that uses credentials to 'login'
 *
 */
interface CredentialsLoginProviderInterface
{
    function login($identity, $password);
}