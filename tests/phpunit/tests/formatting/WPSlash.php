<?php

/**
 * @group formatting
 */
class Tests_Formatting_WPSlash extends WP_UnitTestCase {

	/**
	 * @dataProvider data_wp_slash
	 *
	 * @ticket 42195
	 *
	 * @param string $value
	 * @param string $expected
	 */
	public function test_wp_slash_with( $value, $expected ) {
		$this->assertSame( $expected, wp_slash( $value ) );
	}

	/**
	 * Data provider for test_wp_slash().
	 *
	 * @return array {
	 *     @type array {
	 *         @type mixed  $value    The value passed to wp_slash().
	 *         @type string $expected The expected output of wp_slash().
	 *     }
	 * }
	 */
	public function data_wp_slash() {
		return array(
			array( 123, 123 ),
			array( 123.4, 123.4 ),
			array( true, true ),
			array( false, false ),
			array(
				array(
					'hello',
					null,
					'"string"',
					125.41,
				),
				array(
					'hello',
					null,
					'\"string\"',
					125.41,
				),
			),
			array( "first level 'string'", "first level \'string\'" ),
		);
	}

	/**
	 * @ticket 24106
	 */
	function test_adds_slashes() {
		$old = "I can't see, isn't that it?";
		$new = "I can\'t see, isn\'t that it?";
		$this->assertEquals( $new, wp_slash( $old ) );
		$this->assertEquals( "I can\\\\\'t see, isn\\\\\'t that it?", wp_slash( $new ) );
		$this->assertEquals( array( 'a' => $new ), wp_slash( array( 'a' => $old ) ) ); // Keyed array
		$this->assertEquals( array( $new ), wp_slash( array( $old ) ) ); // Non-keyed
	}

	/**
	 * @ticket 24106
	 */
	function test_preserves_original_datatype() {

		$this->assertEquals( true, wp_slash( true ) );
		$this->assertEquals( false, wp_slash( false ) );
		$this->assertEquals( 4, wp_slash( 4 ) );
		$this->assertEquals( 'foo', wp_slash( 'foo' ) );
		$arr      = array(
			'a' => true,
			'b' => false,
			'c' => 4,
			'd' => 'foo',
		);
		$arr['e'] = $arr; // Add a sub-array
		$this->assertEquals( $arr, wp_slash( $arr ) ); // Keyed array
		$this->assertEquals( array_values( $arr ), wp_slash( array_values( $arr ) ) ); // Non-keyed

		$obj = new stdClass;
		foreach ( $arr as $k => $v ) {
			$obj->$k = $v;
		}
		$this->assertEquals( $obj, wp_slash( $obj ) );
	}

	/**
	 * @ticket 24106
	 */
	function test_add_even_more_slashes() {
		$old = 'single\\slash double\\\\slash triple\\\\\\slash';
		$new = 'single\\\\slash double\\\\\\\\slash triple\\\\\\\\\\\\slash';
		$this->assertEquals( $new, wp_slash( $old ) );
		$this->assertEquals( array( 'a' => $new ), wp_slash( array( 'a' => $old ) ) ); // Keyed array
		$this->assertEquals( array( $new ), wp_slash( array( $old ) ) ); // Non-keyed
	}

}
