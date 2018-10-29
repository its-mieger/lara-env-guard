# ItsMieger LaraEnvGuard - Prevents code to be run in undesired environments

This package helps to prevent code to be executed in undesired environments. Everyone who
unintentionally destroyed a production database because unit tests were running with wrong 
environmental settings, knows how easy this happens...

Basically this package offers three protection mechanisms you easily can add to your code
by including a single trait:

 * check for correct application environment (APP_ENV)
 * check for application environment having a separate `.env` file loaded
 * check database host against pattern whitelist
 
## Usage

Simply add the `ProtectsEnvironment` trait and call the corresponding check functions. These
throw a `RuntimeException` in case your environment is not configured as expected. For
tests the `CreatesApplication` trait is the perfect place to put your check functions in.
See following example:

	trait CreatesApplication {
	
		use ProtectsEnvironment;
    
        public function createApplication() {
            $app = require __DIR__.'/../bootstrap/app.php';
            $app->make(Kernel::class)->bootstrap();
            
            // do environment checks
            $this->checkEnvironmentAllowed();
            $this->checkEnvironmentConfigured();
            $this->checkDatabaseHostWhitelisted();
            
            
            return $app;
        }
    }
    
    
### Checking for allowed environments
The `checkEnvironmentAllowed` method checks that the current environment matches
one of the expected environments.

If called without any parameters only the "testing" environment is allowed.

Of course you may specify a custom list of allowed environments. You may do so by setting
the instance's `allowedEnvironments` property or by passing allowed environments as second
argument:

	$this->checkEnvironmentAllowed('tests', ['testing', 'dusk']);
	
Since you might not be able to list all environments in your code, you can also allow your
current environment by setting a specific variable in your `.env` file. The name of the
variable must match the first argument of the function call. This is the `target` argument
and defaults to `"tests"`. So for your "tests" target, add the following line to your
`.env` file:

	ENV_ALLOWED_FOR_TESTS=true
	
	
### Checking for separate environment configuration being loaded
The `checkEnvironmentConfigured` method checks that the specific `.env` file for the current
environment is exists and is active.

If called without any parameters only the "production" environment can be run with the default
`.env` file. Testing and other environments have to load their corresponding configuration
file, e.g. `.env.testing`.

Of course you may specify a list of other environments for which this check should be bypassed.
You may do so by setting the instance's `allowedEnvironmentsWithoutSpecialConfiguration`
property or by passing allowed environments as second argument:

	$this->checkEnvironmentConfigured('tests', ['staging']);
	
You may also allow the usage of a specific configuration file by setting a specific variable
within that file. The name of the variable must match the first argument of the function
call. This is the `target` argument and defaults to `"tests"`. So for your "tests" target,
add the following line to your `.env` file to allow it's usage in testing environment:
                  
	ENV_WHITELIST_FOR_TESTS=testing
	
You may specify multiple environments separating them by `|`:

	ENV_WHITELIST_FOR_TESTS=testing|dusk
	
	
### Checking database host whitelist
It's always a good idea, to check that you are facing your test database when running tests!
You can do so by calling `checkDatabaseHostWhitelisted`.

The database host is read from the application configuration and checked against a whitelist
of patterns. If the host name contains at least one pattern from the whitelist, the check
check will pass.
The default patterns are `"-dev"`, `"-development"` and `"-testing"`. (including the leading `"-"`).
The patterns are simple strings to be contained, not regular expressions or s.th. similar.

If called without any parameters only the default connection will be checked.

Of course you can pass other connection names also. `null` will always represent the default
connection, so to check the default connection and a custom connection named "my-connection"
call:

	$this->checkDatabaseHostWhitelisted([null, 'my-connection']);
	
If you want to use a custom pattern list, you can set the instance's
`databaseHostWhitelistPatterns` property or pass the patterns as third argument:

	$this->checkDatabaseHostWhitelisted(null, 'tests', ['127.0.0.1', 'localhost']);
	
Also here, you may use an environment variable to specify the whitelist from within
your configuration: 

	DB_WHITELIST_PATTERN_FOR_TESTS=127.0.0.1|localhost|test-db
	
The variable name must be suffixed by the target given in the function call. See above checks
to learn more about the target parameter.