<?php

	namespace ItsMieger\LaraEnvGuard;


	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Str;

	/**
	 * Contains helper functions to prevent code from being run in undesired environments by misconfiguration
	 * @package ItsMieger\LaraEnvGuard
	 */
	trait ProtectsEnvironment
	{

		/**
		 * Checks if the current environment is allowed here. If not allowed a runtime exception is thrown
		 * @param string $target Name of the target to check for. Environments can explicitly be allowed if env variable ENV_ALLOWED_FOR_{target} is set to true
		 * @param string[]|string|null $allowedEnvironments The list of allowed environments. If omitted, the allowedEnvironments property of this instance is used. If also unset, only "testing" will be
		 * allowed as environment.
		 * @throws \RuntimeException
		 */
		protected function checkEnvironmentAllowed($target = 'tests', $allowedEnvironments = null) {

			$app = ($this->app ?? null) ?: app();

			if ($allowedEnvironments === null)
				$allowedEnvironments = $this->allowedEnvironments ?? ['testing'];
			if (!is_array($allowedEnvironments))
				$allowedEnvironments = [$allowedEnvironments];

			$currentAppEnv  = $app->environment();

			if (!in_array($currentAppEnv, $allowedEnvironments) && !env('ENV_ALLOWED_FOR_' . strtoupper($target)))
				throw new \RuntimeException("Execution in \"{$currentAppEnv}\" environment prohibited." . ($app->configurationIsCached() ? ' Note that configurations is cached and env variables did not take effect.' : ''));

		}


		/**
		 * Checks if a special configuration (.env-file) is loaded for current environment. Else, a runtime exception is thrown
		 * @param string $target Name of the target to check for. Environments can explicitly be allowed without special configuration if listed in env variable ENV_CONFIG_SHARES_FOR_{target}.
		 * @param string[]|null $allowedEnvironmentsWithoutSpecialConfiguration The list of environments which are allowed without a special configuration file. ("production" is always allowed)
		 * If omitted, the allowedEnvironmentsWithoutSpecialConfiguration property of this instance is used. If also unset, only "production" is allowed
		 * @throws \RuntimeException
		 */
		protected function checkEnvironmentConfigured($target = 'tests', $allowedEnvironmentsWithoutSpecialConfiguration = null) {

			$app = ($this->app ?? null) ?: app();

			if ($allowedEnvironmentsWithoutSpecialConfiguration === null)
				$allowedEnvironmentsWithoutSpecialConfiguration = $this->allowedEnvironmentsWithoutSpecialConfiguration ?? [];
			if (!is_array($allowedEnvironmentsWithoutSpecialConfiguration))
				$allowedEnvironmentsWithoutSpecialConfiguration = [$allowedEnvironmentsWithoutSpecialConfiguration];

			$allowedEnvironmentsWithoutSpecialConfiguration = array_merge($allowedEnvironmentsWithoutSpecialConfiguration, array_filter(explode('|', env('ENV_WHITELIST_FOR_' . strtoupper($target)))));
			$allowedEnvironmentsWithoutSpecialConfiguration[] = 'production';

			$currentAppEnv = $app->environment();

			$specificEnvFile = ".env.{$currentAppEnv}";

			if (!in_array($currentAppEnv, $allowedEnvironmentsWithoutSpecialConfiguration) && ($app->configurationIsCached() || $app->environmentFile() != $specificEnvFile)) {

				if ($app->configurationIsCached())
					throw new \RuntimeException("This environment requires {$specificEnvFile} to be loaded. However configuration is cached and .env files are not loaded at all.");
				else
					throw new \RuntimeException("This environment requires {$specificEnvFile} to be loaded but this is not the case.");
			}

		}

		/**
		 * Checks if the host of the given DB connection is whitelisted. Else a runtime exception is thrown
		 * @param string|string[]|null $connections Name(s) of the connection(s) to be checked using the whitelist
		 * @param string $target Name of the target to check for. Hosts can explicitly be allowed if a pattern of env variable DB_WHITELIST_PATTERN_FOR_{target} matches.
		 * @param string[]|null $patterns The patterns. At least one of them must be contained in the hostname for a DB to pass the check. If omitted, the databaseHostWhitelistPatterns
		 * property of the current instance is used. If also unset, the default patterns are "-dev", "-development" and "-testing"
		 * @throw \RuntimeException
		 */
		protected function checkDatabaseHostWhitelisted($connections = null, $target = 'tests', $patterns = null) {
			if (!is_array($connections))
				$connections = [$connections];

			if (count($connections) == 0)
				throw new \InvalidArgumentException('No connection names to check passed');

			if ($patterns === null)
				$patterns = $this->databaseHostWhitelistPatterns ?? ['-dev', '-development', '-testing'];
			if (!is_array($patterns))
				$patterns = [$patterns];

			$patterns = array_merge($patterns, array_filter(explode('|', env('DB_WHITELIST_PATTERN_FOR_' . strtoupper($target)))));

			foreach($connections as $currConnectionName) {

				if (empty($currConnectionName))
					$currConnectionName = DB::getDefaultConnection();

				$currHost = config("database.connections.{$currConnectionName}.host");

				if (!Str::contains($currHost, $patterns))
					throw new \RuntimeException("Database whitelist check failed for connection \"{$currConnectionName}\". (Host \"{$currHost}\" does not contain any of following patterns: \"" . implode('", "', $patterns) ."\")");
			}
		}

//      It's better to use whitelist, so we wait for well founded feature request until we add this code
//
//
//		/**
//		 * Checks if the host of the given DB connection is blacklisted and throws a runtime exception in that case
//		 * @param string|string[]|null $connections Name(s) of the connection(s) to be checked using the blacklist
//		 * @param string[]|null $patterns The patterns. None of them must be contained in the hostname for a DB to pass the check. If empty, the databaseHostBlacklistPatterns
//		 * property of the current instance is used. If also empty, the default patterns are "live", "production" and "staging"
//		 * @throw \RuntimeException
//		 */
//		protected function checkDatabaseHostBlacklisted($connections = null, $patterns = null) {
//
//			if (!is_array($connections))
//				$connections = [$connections];
//
//			if (!$patterns)
//				$patterns = $this->databaseHostBlacklistPatterns ?? ['live', 'production', 'staging'];
//
//			if (count($connections) == 0)
//				throw new \InvalidArgumentException('No connection names to check passed');
//			if (count($patterns) == 0)
//				throw new \InvalidArgumentException('No patterns for connection name blacklist passed');
//
//			foreach($connections as $currConnectionName) {
//
//				if (empty($currConnectionName))
//					$currConnectionName = DB::getDefaultConnection();
//
//				$currHost = config("database.connections.{$currConnectionName}.host");
//
//				if (Str::contains($currHost, $patterns))
//					throw new \RuntimeException("Database blacklist check failed for connection \"{$currConnectionName}\". (Host \"{$currHost}\" must not contain any of following patterns: \"" . implode('", "', $patterns) ."\")");
//			}
//		}


	}