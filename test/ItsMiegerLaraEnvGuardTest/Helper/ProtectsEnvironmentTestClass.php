<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 29.10.18
	 * Time: 11:26
	 */

	namespace ItsMiegerLaraEnvGuardTest\Helper;



	use Illuminate\Contracts\Foundation\Application;
	use ItsMieger\LaraEnvGuard\ProtectsEnvironment;

	/**
	 * Class for testing the ProtectsEnvironmentTrait
	 * @package ItsMiegerLaraEnvGuardTest\Mock
	 */
	class ProtectsEnvironmentTestClass
	{
		use ProtectsEnvironment;


		protected $app;

		/**
		 * ProtectsEnvironmentTestClass constructor.
		 * @param Application $app The application instance
		 */
		public function __construct(Application $app) {
			$this->app = $app;
		}


		public function call($method, $arguments = []) {
			return $this->{$method}(...$arguments);
		}
	}