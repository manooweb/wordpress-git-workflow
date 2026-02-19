<?php
defined( 'ABSPATH' ) || exit;

/**
 * Environment variables bootstrap using vlucas/phpdotenv.
 *
 * This block is intended to be placed at the very top of wp-config.php,
 * before any WordPress constants are defined.
 *
 * It loads environment variables from a file located OUTSIDE the webroot.
 * Example structure:
 *
 *   project-root/
 *     .env.manooweb     <-- environment file
 *     webroot/
 *       wp-config.php
 *       vendor/
 */

/**
 * Determines whether the current environment is considered a development environment.
 *
 * You can customize this logic according to your own setup (hostname, IP, env var, etc.).
 *
 * @since 1.0.0
 *
 * @return bool True if this is a development environment, false otherwise.
 */
function env_is_dev(): bool {
	return ( ! empty( $_SERVER['LANDO'] ) || getenv( 'LANDO' ) );
}

/**
 * Handles configuration errors in a clean, predictable manner.
 *
 * - Logs the detailed error message (server-side only).
 * - Displays a verbose message in development.
 * - Displays a neutral HTTP 500 error in production.
 *
 * @since 1.0.0
 *
 * @param string $public_message Message shown to end users (must be neutral).
 * @param string $log_message    Detailed message stored in server error logs.
 *
 * @return void
 */
function env_config_fail( string $public_message, string $log_message ): void {
	$is_dev = env_is_dev();

	// Log internal error message.
	error_log( '[ENV ERROR] ' . $log_message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

	if ( $is_dev ) {
		// Verbose output in development environments.
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/html; charset=utf-8' );
		}

		echo '<h1>Environment configuration error</h1>';
		echo '<p>' . esc_html( $log_message ) . '</p>';
		exit;
	}

	// Neutral output in production.
	if ( ! headers_sent() ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		header( 'Content-Type: text/plain; charset=utf-8' );
	}

	echo $public_message;
	exit;
}

// The project root is assumed to be one level above the webroot.
$project_root = dirname( __DIR__ );

// Single source of truth: environment file name.
$env_file_name = '.env.manooweb';

// Environment file located outside the webroot.
$env_file = $project_root . '/' . $env_file_name;

// Composer autoloader located inside the webroot's vendor directory.
$autoload = __DIR__ . '/vendor/autoload.php';

/**
 * 1. Ensure Composer autoloader exists.
 */
if ( ! file_exists( $autoload ) ) {
	env_config_fail(
		'Internal error. Please try again later.',
		'Composer autoloader not found: ' . $autoload
	);
}

require_once $autoload;

/**
 * 2. Ensure Dotenv is available.
 */
if ( ! class_exists( \Dotenv\Dotenv::class ) ) {
	env_config_fail(
		'Internal error. Please try again later.',
		'Dotenv class "\\Dotenv\\Dotenv" is not available. Verify Composer dependencies.'
	);
}

/**
 * 3. Ensure the environment file exists.
 */
if ( ! file_exists( $env_file ) ) {
	env_config_fail(
		'Configuration error. Please contact the site administrator.',
		'Environment file missing: ' . $env_file
	);
}

/**
 * 4. Load environment variables and validate required keys.
 */
try {
	// Load the specifically named environment file (e.g. .env.manooweb).
	$dotenv = \Dotenv\Dotenv::createImmutable( $project_root, $env_file_name );
	$dotenv->safeLoad();

	// Define required environment variables.
	$dotenv->required(
		array(
			'DB_NAME',
			'DB_USER',
			'DB_PASSWORD',
			'DB_HOST',
		)
	)->notEmpty();

} catch ( \Throwable $e ) {
	env_config_fail(
		'Internal error. Please try again later.',
		$e->getMessage()
	);
}

/**
 * Retrieves an environment variable with an optional default value.
 *
 * Example:
 *   define( 'DB_NAME', env( 'DB_NAME', 'wordpress' ) );
 *
 * @since 1.0.0
 *
 * @param string $key     Environment variable name.
 * @param mixed  $default Default value if variable is not set.
 *
 * @return mixed The environment variable value or the default.
 */
if ( ! function_exists( 'env' ) ) {
	function env( string $key, mixed $default = null ): mixed {
		return isset( $_ENV[ $key ] ) ? $_ENV[ $key ] : $default;
	}
}

/**
 * Retrieves an environment variable as a boolean.
 *
 * Accepted truthy values (case-insensitive):
 *   "1", "true", "on", "yes"
 *
 * Accepted falsy values (case-insensitive):
 *   "0", "false", "off", "no", ""
 *
 * Any other non-null value will return the default.
 *
 * Example:
 *   define( 'WP_DEBUG', env_bool( 'WP_DEBUG', false ) );
 *
 * @since 1.0.0
 *
 * @param string $key     Environment variable name.
 * @param bool   $default Default value if variable is not set or invalid.
 *
 * @return bool The boolean value of the environment variable or the default.
 */
if ( ! function_exists( 'env_bool' ) ) {
	function env_bool( string $key, bool $default = false ): bool {
		$value = env( $key, null );

		if ( null === $value ) {
			return $default;
		}

		if ( is_bool( $value ) ) {
			return $value;
		}

		$value = strtolower( (string) $value );

		$truthy = array( '1', 'true', 'on', 'yes' );
		$falsy  = array( '0', 'false', 'off', 'no', '' );

		if ( in_array( $value, $truthy, true ) ) {
			return true;
		}

		if ( in_array( $value, $falsy, true ) ) {
			return false;
		}

		return $default;
	}
}
