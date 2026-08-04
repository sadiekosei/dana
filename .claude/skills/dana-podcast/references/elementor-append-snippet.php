<?php
/**
 * Narrow, single-purpose REST API for safely appending ONE new top-level
 * Elementor container to the /podcast/ page (post ID 3048) only.
 *
 * Modeled directly on the proven pattern from Melinda Hinson's site's
 * "Kosei Deploy" API (class-kosei-deploy.php): _elementor_data is plain
 * post meta holding a JSON array of top-level elements; writes go through
 * update_post_meta() + an explicit CSS regen, since Elementor doesn't
 * rebuild a page's CSS on a bare data write.
 *
 * Deliberately NOT a general-purpose page editor: hardcoded to page
 * 3048, GET returns the raw current data for backup/inspection, POST
 * only ever APPENDS (never replaces/removes existing elements) and
 * rejects the write if any submitted element ID collides with an
 * existing one on the page.
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'kosei-dana/v1', '/podcast-page-data', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_get_page_data',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-append', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_append_section',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/purge-podcast-page-cache', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_purge_cache',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
} );

function kosei_dana_purge_cache() {
	$fired = array();
	// LiteSpeed Cache: purging the specific post + a full-site purge, since
	// this page's full-page HTML is what's stale (Elementor's own internal
	// CSS/asset cache is separate and was already being cleared).
	if ( has_action( 'litespeed_purge_post' ) || function_exists( 'do_action' ) ) {
		do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
		$fired[] = 'litespeed_purge_post';
	}
	do_action( 'litespeed_purge_all' );
	$fired[] = 'litespeed_purge_all';
	return new \WP_REST_Response( array( 'ok' => true, 'fired' => $fired ), 200 );
}

const KOSEI_DANA_PAGE_ID = 3048;

function kosei_dana_collect_ids( $elements, &$ids ) {
	foreach ( (array) $elements as $el ) {
		if ( ! empty( $el['id'] ) ) {
			$ids[] = $el['id'];
		}
		if ( ! empty( $el['elements'] ) ) {
			kosei_dana_collect_ids( $el['elements'], $ids );
		}
	}
}

function kosei_dana_get_page_data( \WP_REST_Request $req ) {
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return new \WP_Error( 'no_elementor', 'Elementor is not active.', array( 'status' => 500 ) );
	}
	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}
	$ids = array();
	kosei_dana_collect_ids( $decoded, $ids );
	return new \WP_REST_Response( array(
		'ok'             => true,
		'top_elements'   => count( $decoded ),
		'existing_ids'   => array_values( array_unique( $ids ) ),
		'data'           => $decoded,
		'modified'       => get_post_modified_time( 'c', false, KOSEI_DANA_PAGE_ID ),
	), 200 );
}

function kosei_dana_append_section( \WP_REST_Request $req ) {
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return new \WP_Error( 'no_elementor', 'Elementor is not active.', array( 'status' => 500 ) );
	}
	$body = json_decode( $req->get_body(), true );
	if ( ! is_array( $body ) || empty( $body['section'] ) || ! is_array( $body['section'] ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"section": {...one Elementor container element...}}.', array( 'status' => 400 ) );
	}
	$new_section = $body['section'];

	// Always re-fetch the CURRENT live data at write time (not whatever the
	// caller may have cached earlier) so a concurrent edit can't be clobbered.
	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}

	$existing_ids = array();
	kosei_dana_collect_ids( $decoded, $existing_ids );
	$new_ids = array();
	kosei_dana_collect_ids( array( $new_section ), $new_ids );
	$collisions = array_intersect( $existing_ids, $new_ids );
	if ( ! empty( $collisions ) ) {
		return new \WP_Error( 'id_collision', 'Submitted element IDs collide with existing IDs on the page: ' . implode( ', ', $collisions ), array( 'status' => 409 ) );
	}

	$before_count = count( $decoded );
	$decoded[]    = $new_section; // pure append, nothing existing is touched

	update_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', wp_slash( wp_json_encode( $decoded ) ) );

	// Match upsert_page()'s CSS handling so the new element's (inline) styles
	// and any layout recalculation actually take effect, not just get cached
	// under the old CSS file.
	$css_regen = false;
	if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
		try {
			delete_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_css' );
			\Elementor\Core\Files\CSS\Post::create( KOSEI_DANA_PAGE_ID )->update();
			$css_regen = true;
		} catch ( \Throwable $e ) {
			$css_regen = 'err: ' . $e->getMessage();
		}
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	// Elementor's cache above is for compiled CSS/assets only -- the site
	// also runs LiteSpeed Cache, which caches full rendered page HTML
	// separately and does NOT get invalidated by an _elementor_data write
	// alone (confirmed: page served x-litespeed-cache: hit with stale
	// content after a successful append). Purge that too so the change is
	// actually visible.
	do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
	do_action( 'litespeed_purge_all' );

	return new \WP_REST_Response( array(
		'ok'            => true,
		'before_count'  => $before_count,
		'after_count'   => count( $decoded ),
		'css_regen'     => $css_regen,
		'view'          => get_permalink( KOSEI_DANA_PAGE_ID ),
	), 200 );
}
