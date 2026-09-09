<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;

class ShortcodeTest extends TestCase {

	public function test_shortcode_is_registered() {
		$this->assertTrue( shortcode_exists( 'nutritiontable' ) );
	}

	public function test_shortcode_output_replaces_the_tag_in_place() {
		// Regression guard: niw_shortcode_func() must RETURN the table markup,
		// not echo it — do_shortcode() only captures a callback's return value.
		// An echoing callback prints immediately during shortcode processing
		// (which runs before the surrounding content is output), so the table
		// would end up rendered before the whole page instead of inline where
		// the [nutritiontable] tag actually sits.
		$content = 'Before text. [nutritiontable] After text.';

		ob_start();
		$rendered = do_shortcode( $content );
		$leaked_output = ob_get_clean();

		$this->assertSame( '', $leaked_output, 'The shortcode must not echo output as a side effect.' );

		$before_pos = strpos( $rendered, 'Before text.' );
		$table_pos  = strpos( $rendered, 'nutrition-table' );
		$after_pos  = strpos( $rendered, 'After text.' );

		$this->assertNotFalse( $table_pos, 'Shortcode did not render a nutrition table at all.' );
		$this->assertTrue( $before_pos < $table_pos && $table_pos < $after_pos, 'Nutrition table did not render in place of the shortcode tag.' );
	}

	public function test_shortcode_ignores_any_passed_attributes() {
		$rendered = do_shortcode( '[nutritiontable foo="bar"]' );

		$this->assertStringContainsString( 'nutrition-table', $rendered );
	}

	public function test_shortcode_on_a_non_product_context_renders_an_empty_table_without_errors() {
		$page_id = self::factory()->post->create( array( 'post_type' => 'page' ) );

		global $post;
		$post = get_post( $page_id );
		setup_postdata( $post );

		$rendered = do_shortcode( '[nutritiontable]' );
		wp_reset_postdata();

		preg_match( '/<tbody>(.*?)<\/tbody>/s', $rendered, $matches );

		$this->assertStringContainsString( '<table id="nutrition-table">', $rendered );
		$this->assertStringNotContainsString( '<tr>', $matches[1] ?? '' );
	}
}
