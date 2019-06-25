<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 29.10.18
	 * Time: 11:02
	 */

	namespace ItsMiegerLaraEnvGuardTest\Cases;



	use Illuminate\Foundation\Application;
	use Illuminate\Support\Facades\DB;
	use ItsMiegerLaraEnvGuardTest\Helper\ProtectsEnvironmentTestClass;
	use ItsMiegerLaraEnvGuardTest\TestCase;
	use PHPUnit\Framework\MockObject\MockObject;

	class ProtectsEnvironmentTest extends TestCase
	{
		const METHOD_ENV = 'environment';
		const METHOD_CONFIG_CACHED = 'configurationIsCached';
		const METHOD_ENV_FILE = 'environmentFile';

		public function testApp_environment() {
			// this test ensures laravel's app()->environment() returns the correct environment. We check this here since we have to mock the app
			// class for some of the following tests

			$this->assertEquals('testing', app()->{self::METHOD_ENV}());
		}

		public function testApp_configurationIsCached() {
			// this test ensures laravel's app()->configurationIsCached() is working. We check this here since we have to mock the app
			// class for some of the following tests

			$this->assertFalse(app()->{self::METHOD_CONFIG_CACHED}());
		}

		public function testApp_environmentFile() {
			// this test ensures laravel's app()->environmentFile() returns the environment file used. We check this here since we have to mock the app
			// class for some of the following tests

			$this->assertEquals('.env.testing', app()->{self::METHOD_ENV_FILE}());
		}

		public function testConfig_DatabaseHost() {
			// this test ensures database hosts are configured at "database.connections.{name}.host". We check this here since we have to mock the configuration
			// for some of the following tests

			$this->assertEquals('127.0.0.1', config("database.connections." . DB::getDefaultConnection() .".host"));
		}

		public function testCheckEnvironmentAllowed_default_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('testing');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentAllowed');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_default_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_default_notAllowed_configurationCached() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(true);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*cached.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_byArguments_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentAllowed', ['tests', ['myenv']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_byArguments_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests', ['yourEnv']]);

		}

		public function testCheckEnvironmentAllowed_byArguments_multiple_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentAllowed', ['tests', ['yourenv', 'myenv']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_byArguments_multiple_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests', ['xenv', 'yourEnv']]);

		}

		public function testCheckEnvironmentAllowed_byArguments_string_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentAllowed', ['tests', 'myenv']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_byArguments_string_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests', 'yourEnv']);

		}

		public function testCheckEnvironmentAllowed_byArguments_notAllowed_configurationCached() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(true);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*cached.*/');

			$tc->call('checkEnvironmentAllowed', ['tests', ['yourEnv']]);

		}

		public function testCheckEnvironmentAllowed_classProperty_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = ['myenv'];

			$tc->call('checkEnvironmentAllowed');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_classProperty_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = ['your-env'];

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_classProperty_notAllowed_configurationCached() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(true);

			$tc = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = ['your-env', 'another-env'];

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*cached.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_classProperty_multiple_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc                      = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = ['myenv', 'another-env'];

			$tc->call('checkEnvironmentAllowed');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_classProperty_multiple_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc                      = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = ['your-env', 'another-env'];

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_classProperty_string_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc                      = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = 'myenv';

			$tc->call('checkEnvironmentAllowed');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentAllowed_classProperty_string_notAllowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc                      = new ProtectsEnvironmentTestClass($appMock);
			$tc->allowedEnvironments = 'another-env';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed');

		}

		public function testCheckEnvironmentAllowed_envVar_allowed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);


			putenv('ENV_ALLOWED_FOR_TESTS1=true');

			$tc->call('checkEnvironmentAllowed', ['tests1']);

			$this->expectNotToPerformAssertions();
		}


		public function testCheckEnvironmentAllowed_envVar_notAllowed_unset() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);


			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests2']);

		}

		public function testCheckEnvironmentAllowed_envVar_notAllowed_false() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);

			$tc = new ProtectsEnvironmentTestClass($appMock);


			putenv('ENV_ALLOWED_FOR_TESTS2=false');

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests2']);

		}

		public function testCheckEnvironmentAllowed_envVar_notAllowed_configurationCached() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(true);

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*myenv.*/');

			$tc->call('checkEnvironmentAllowed', ['tests2']);

		}

		public function testCheckEnvironmentConfigured_configured() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env.myenv');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentConfigured');

			$this->expectNotToPerformAssertions();
		}


		public function testCheckEnvironmentConfigured_notConfigured() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured');

		}

		public function testCheckEnvironmentConfigured_notConfigured_configurationCached() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(true);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*cached.*/');

			$tc->call('checkEnvironmentConfigured');

		}

		public function testCheckEnvironmentConfigured_notConfigured_production() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('production');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentConfigured');

			$this->expectNotToPerformAssertions();
		}


		public function testCheckEnvironmentConfigured_notConfigured_allowedByArguments() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentConfigured', ['tests', ['myenv']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByArguments() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured', ['tests', ['yourenv']]);

		}

		public function testCheckEnvironmentConfigured_notConfigured_allowedByArguments_multiple() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentConfigured', ['tests', ['yourenv', 'myenv']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByArguments_multiple() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured', ['tests', ['yourenv', 'anotherenv']]);

		}

		public function testCheckEnvironmentConfigured_notConfigured_allowedByArguments_string() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->call('checkEnvironmentConfigured', ['tests', 'myenv']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByArguments_string() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured', ['tests', 'yourenv']);

		}

		public function testCheckEnvironmentConfigured_notConfigured_allowedByClassProperty() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->allowedEnvironmentsWithoutSpecialConfiguration = ['myenv'];

			$tc->call('checkEnvironmentConfigured');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByClassProperty() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->allowedEnvironmentsWithoutSpecialConfiguration = ['your-env', 'another-env'];

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured');

		}


		public function testCheckEnvironmentConfigured_notConfigured_allowedByEnvVar() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('ENV_WHITELIST_FOR_TESTSA1=myenv');

			$tc->call('checkEnvironmentConfigured', ['testsA1']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_allowedByEnvVar_multiple() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('ENV_WHITELIST_FOR_TESTSA2=yourenv|myenv');

			$tc->call('checkEnvironmentConfigured', ['testsA2']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByEnvVar() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('ENV_CONFIG_SHARED_FOR_TESTSA3=yourenv');

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured', ['testsA3']);
		}

		public function testCheckEnvironmentConfigured_notConfigured_notAllowedByEnvVar_multiple() {
			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
			$appMock->method(self::METHOD_ENV)
				->willReturn('myenv');
			$appMock->method(self::METHOD_ENV_FILE)
				->willReturn('.env');
			$appMock->method(self::METHOD_CONFIG_CACHED)
				->willReturn(false);


			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('ENV_CONFIG_SHARED_FOR_TESTSA4=yourenv|anotherenv');

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*\.env\.myenv.*/');

			$tc->call('checkEnvironmentConfigured', ['testsA4']);
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_defaultPatterns_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			// pattern "-dev"
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'db-dev';
			$tc->call('checkDatabaseHostWhitelisted');

			// pattern "-development"
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'db-development';
			$tc->call('checkDatabaseHostWhitelisted');

			// pattern "-testing"
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'db-testing';
			$tc->call('checkDatabaseHostWhitelisted');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_defaultPatterns_notWhitelistedButSqlLiteMemory() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			// pattern "-dev"
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = '';
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.driver'] = 'sqlite';
			app()['config']['database.connections.' . DB::getDefaultConnection() . '.database'] = ':memory:';

			$tc->call('checkDatabaseHostWhitelisted');

			$this->expectNotToPerformAssertions();

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_defaultPatterns_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted');

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_defaultPatterns_notWhitelisted_hostNotSet() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection()] = [];

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted');

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', ['ydb']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', ['x']]);

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_multiple_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', ['x', 'ydb']]);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_multiple_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', ['x', 'z']]);

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_string_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', 'ydb']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byArguments_string_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [null, 'tests', 'z']);

		}


		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = ['ydb'];


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = ['x'];

			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted');

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_multiple_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = ['x', 'ydb'];


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_multiple_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = ['x', 'z'];


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted');

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_string_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = 'ydb';

			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted');

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byClassProperty_string_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			$tc->databaseHostWhitelistPatterns = 'z';


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted');

		}


		public function testCheckDatabaseHostWhitelisted_defaultConnection_byEnvVar_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('DB_WHITELIST_PATTERN_FOR_TESTSB1=ydb');


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted', [null, 'testsb1']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byEnvVar_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('DB_WHITELIST_PATTERN_FOR_TESTSB2=asd');

			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [null, 'testsb2']);

		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byEnvVar_multiple_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('DB_WHITELIST_PATTERN_FOR_TESTSB3=x|ydb');


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$tc->call('checkDatabaseHostWhitelisted', [null, 'testsb3']);

			$this->expectNotToPerformAssertions();
		}

		public function testCheckDatabaseHostWhitelisted_defaultConnection_byEnvVar_multiple_notWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);

			putenv('DB_WHITELIST_PATTERN_FOR_TESTSB4=x|z');


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'mydb1';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [null, 'testsb4']);

		}

		public function testCheckDatabaseHostWhitelisted_multipleConnections_defaultPatterns_whitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'my-testing';
			app()['config']['database.connections.conn1.host'] = 'my-dev';


			$tc->call('checkDatabaseHostWhitelisted', [[null, 'conn1']]);

			$this->expectNotToPerformAssertions();

		}

		public function testCheckDatabaseHostWhitelisted_multipleConnections_defaultPatterns_NotWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'db-dev';
			app()['config']['database.connections.conn1.host']                              = 'mydb';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*conn1.*/');

			$tc->call('checkDatabaseHostWhitelisted', [[null, 'conn1']]);

		}


		public function testCheckDatabaseHostWhitelisted_multipleConnections_defaultPatterns_defaultNotWhitelisted() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			app()['config']['database.connections.' . DB::getDefaultConnection() . '.host'] = 'dbprod';
			app()['config']['database.connections.conn1.host'] = 'db-dev';

			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessageRegExp('/.*' . DB::getDefaultConnection() . '.*/');

			$tc->call('checkDatabaseHostWhitelisted', [[null, 'conn1']]);

		}

		public function testCheckDatabaseHostWhitelisted_noConnectionsPassed() {

			/** @var Application|MockObject $appMock */
			$appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

			$tc = new ProtectsEnvironmentTestClass($appMock);


			$this->expectException(\InvalidArgumentException::class);
			$this->expectExceptionMessageRegExp('/.*connection.*/');

			$tc->call('checkDatabaseHostWhitelisted', [[]]);
		}



	}