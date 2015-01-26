<?php

namespace Gcd\Core\UnitTesting;

/**
 * This test case class should be used for unit testing site specific code.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
use Gcd\Core\Modelling\Repositories\Repository;
use Gcd\Core\Modelling\Schema\SolutionSchema;

class AppTestCase extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$context = new \Gcd\Core\Context();
		$context->UnitTesting = true;

		if ( class_exists( "Gcd\Core\Modelling\Repositories\Repository" ) )
		{
			Repository::SetDefaultRepositoryClassName( "Gcd\Core\Modelling\Repositories\Offline\Offline" );
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
