<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 29.10.18
	 * Time: 12:47
	 */

	namespace ItsMiegerLaraEnvGuardTest;


	use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

	class TestCase extends \Orchestra\Testbench\TestCase
	{
		protected function getEnvironmentSetUp($app) {
			// make sure, our .env file is loaded
			$app->useEnvironmentPath(__DIR__ . '/../..');
			$app->bootstrapWith([LoadEnvironmentVariables::class]);
			parent::getEnvironmentSetUp($app);
		}
	}