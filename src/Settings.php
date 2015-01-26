<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown;

require_once __DIR__ . "/Modelling/ModelState.php";

use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Modelling\ModelState;

/**
 * A base class for creating settings classes.
 *
 * All settings classes extend this base class which in turn extends the Model class meaning that
 * settings classes can also support magical properties.
 *
 * Settings must be set and got through the a settings class that derives from this.
 *
 * All settings data is cached so any instance of your settings class will be sharing the same data.
 *
 * To use a settings class simply instantiate it:
 *
 * $settings = new ModellingSettings();
 * print $settings->Host;
 *
 * All settings have a 'namespace' which is based on the class name of the settings class only - minus
 * the word 'Settings'
 *
 * For quick access you can get settings by simply doing:
 *
 * \Rhubarb\Crown\Settings::GetSetting( "Data", "Host", "127.0.0.1" )
 *
 * Note that this means you cannot have two settings classes with the same name - even if they have
 * different PHP namespaces.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
abstract class Settings extends ModelState
{
	/**
	 * The private collection of cached model state data for all the sessions.
	 *
	 * This provides the Settings class with it's Singleton like behavour.
	 *
	 * @var array
	 */
	private static $cachedModelData = array();

	/**
	 * The namespace for this settings object.
	 *
	 * @var string
	 */
	private $namespace = "";

	public function __construct()
	{
		$className = basename( str_replace( "\\", "/", get_class( $this ) ) );
		$this->namespace = str_replace( "Settings", "", $className );

		$needsInitialised = false;

		// Get the model data by using the class name
		if ( !isset( self::$cachedModelData[ $this->namespace ] ) )
		{
			self::$cachedModelData[ $this->namespace ] = array();

			// If the model data didn't exist before we know that we are being used for
			// the first time and we should call the InitialiseDefaultValues() function.
			$needsInitialised = true;
		}

		$this->modelData = &self::$cachedModelData[ $this->namespace ];

		if ( $needsInitialised )
		{
			$this->initialiseDefaultValues();
		}
	}

	/**
	 * Returns the namespace for this settings class.
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * Override this class to set default values for settings.
	 */
	protected function initialiseDefaultValues()
	{

	}

	/**
	 * Get's a setting without having to use the relevant settings object.
	 *
	 * This is a convenience method used to keep code fast and tidy.
	 *
	 * @param $namespace
	 * @param $settingName
	 * @param null $defaultValue
	 * @throws Exceptions\SettingMissingException
	 * @return mixed
	 */
	public static function getSetting( $namespace, $settingName, $defaultValue = null )
	{
		if ( isset( self::$cachedModelData[ $namespace ][ $settingName ] ) )
		{
			return self::$cachedModelData[ $namespace ][ $settingName ];
		}

		if ( $defaultValue === null )
		{
			throw new SettingMissingException( $namespace, $settingName );
		}

		return $defaultValue;
	}

	/**
	 * Removes all the settings for a particular namespace.
	 *
	 * Very rarely needed, usually by unit tests.
	 *
	 * @param $namespace
	 */
	public static function deleteSettingNamespace( $namespace )
	{
		unset( self::$cachedModelData[ $namespace ] );
	}
}