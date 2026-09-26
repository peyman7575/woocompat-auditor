<?php
/**
 * Verify that release version metadata stays synchronized.
 *
 * @package WooCompatAuditor
 */

$root        = dirname( __DIR__ );
$plugin_file = file_get_contents( $root . '/woocompat-auditor.php' );
$readme      = file_get_contents( $root . '/readme.txt' );

if ( false === $plugin_file || false === $readme ) {
	fwrite( STDERR, "Unable to read version metadata files.\n" );
	exit( 1 );
}

$matches = array();

if ( ! preg_match( '/^ \* Version:\s*(\S+)/m', $plugin_file, $matches ) ) {
	fwrite( STDERR, "Plugin header version was not found.\n" );
	exit( 1 );
}

$header_version = $matches[1];

if ( ! preg_match( "/define\(\s*'WOOCOMPAT_AUDITOR_VERSION'\s*,\s*'([^']+)'\s*\)/", $plugin_file, $matches ) ) {
	fwrite( STDERR, "WOOCOMPAT_AUDITOR_VERSION was not found.\n" );
	exit( 1 );
}

$constant_version = $matches[1];

if ( ! preg_match( '/^Stable tag:\s*(\S+)/mi', $readme, $matches ) ) {
	fwrite( STDERR, "readme.txt Stable tag was not found.\n" );
	exit( 1 );
}

$stable_tag = $matches[1];

$versions = array(
	'plugin header' => $header_version,
	'plugin constant' => $constant_version,
	'readme stable tag' => $stable_tag,
);

if ( 1 !== count( array_unique( array_values( $versions ) ) ) ) {
	fwrite( STDERR, "Version metadata mismatch:\n" );

	foreach ( $versions as $label => $version ) {
		fwrite( STDERR, sprintf( "- %s: %s\n", $label, $version ) );
	}

	exit( 1 );
}

fwrite( STDOUT, sprintf( "Version metadata OK: %s\n", $header_version ) );
