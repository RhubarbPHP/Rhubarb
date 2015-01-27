<?php

namespace Rhubarb\Crown\UnitTesting;

/**
 * This test case class should be used for unit testing site specific code.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
use Rhubarb\Crown\Modelling\Repositories\Repository;
use Rhubarb\Crown\Modelling\Schema\SolutionSchema;

class AppTestCase extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$context = new \Rhubarb\Crown\Context();
		$context->UnitTesting = true;

		if ( class_exists( "Rhubarb\Crown\Modelling\Repositories\Repository" ) )
		{
			Repository::SetDefaultRepositoryClassName( "Rhubarb\Crown\Modelling\Repositories\Offline\Offline" );
		}
	}

	public final function CreateModels( $modelData = [] )
	{
		$lastModel = "";

		foreach( $modelData as $modelAlias => $models )
		{
			foreach( $models as $data )
			{
				$model = SolutionSchema::GetModel( $modelAlias );
				$model->ImportData( $data );
				$model->Save();

				$lastModel = $model;
			}
		}

		return $lastModel;
	}
}
