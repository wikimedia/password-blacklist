<?php

namespace Wikimedia\PasswordBlacklist\Test;

use Wikimedia\PasswordBlacklist\PasswordBlacklist;

/**
 * @covers \Wikimedia\PasswordBlacklist\PasswordBlacklist
 */
class PasswordBlacklistTest extends \PHPUnit\Framework\TestCase {

	public static function getBlacklistedPasswords() {
		$lines = [];
		$file = fopen(
			dirname( __DIR__ ) . '/scripts/data/10_million_password_list_top_100000.txt',
			'r'
		);
		while ( !feof( $file ) ) {
			$line = trim( fgets( $file ) );
			if ( !$line ) {
				continue;
			}
			$lines[] = $line;
		}

		return $lines;
	}

	/**
	 * Tests that all the passwords in the original 10_million_password_list_top_100000.txt file
	 * are in the BloomFilter.
	 *
	 * @param string $password
	 */
	function testBlacklistedPassword() {
		// We don't use a data provider here to avoid creating 100,000 test cases.
		foreach ( self::getBlacklistedPasswords() as $password ) {
			$this->assertTrue( PasswordBlacklist::isBlacklisted( $password ) );
		}
	}

	public static function getNonBlacklistedPasswords() {
		return [
			[ 'testwikijenkinspass' ],
			[ 'MediaWiki' ],
			[ 'Wikipedia' ],
			[ 'Wikimedia' ],

			// Passwords that are known to have been false positives
			[ 'yell0w lighter peeler' ],
		];
	}

	/**
	 * Tests a few passwords that aren't in the blacklist
	 *
	 * @dataProvider getNonBlacklistedPasswords
	 * @param string $password
	 */
	function testNonBlacklistedPasswords( $password ) {
		$this->assertFalse( PasswordBlacklist::isBlacklisted( $password ) );
	}
}
