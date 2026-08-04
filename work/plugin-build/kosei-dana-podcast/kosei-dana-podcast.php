<?php
/**
 * Plugin Name: Kosei Dana Podcast Tools
 * Description: Narrow, single-purpose REST API for safely appending ONE new top-level Elementor container to the /podcast/ page (post ID 3048) only. Built by Sadie's Claude Code session; safe to deactivate/delete once the podcast archive widget work is finished.
 * Version: 1.3.1
 * Author: Kosei Designs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

// New routes kept 404ing for a while after each plugin upload/update on
// this host -- strongly suggests PHP OPcache serving stale compiled
// bytecode for this file path across uploads (opcache.validate_timestamps
// off, or a long revalidate_freq, are common on shared LiteSpeed hosts).
// Force a fresh compile on load so updates take effect immediately.
if ( function_exists( 'opcache_reset' ) ) {
	@opcache_reset();
}

const KOSEI_DANA_PAGE_ID = 3048;

// The ORIGINAL 10 top-level element IDs, captured in a backup before any
// of this plugin's writes. Anything appended later (this widget's own
// prior attempts) is safe to remove; these never are.
const KOSEI_DANA_ORIGINAL_TOP_IDS = array(
	'18d7e890', '4fd4e4c7', '21bf7e0d', '918be9b', 'b105d98',
	'2b3f6748', '15afa906', '68be86fb', '6ac163cb', '6e92f2a',
);

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
	register_rest_route( 'kosei-dana/v1', '/podcast-page-diag', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_diag',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-remove-appended', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_remove_appended',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
} );

function kosei_dana_remove_appended( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	$id   = is_array( $body ) ? ( $body['id'] ?? '' ) : '';
	if ( ! $id ) {
		return new \WP_Error( 'bad_body', 'Body must be {"id": "<top-level element id to remove>"}.', array( 'status' => 400 ) );
	}
	if ( in_array( $id, KOSEI_DANA_ORIGINAL_TOP_IDS, true ) ) {
		return new \WP_Error( 'protected', 'Refusing to remove an original page element.', array( 'status' => 403 ) );
	}
	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}
	$before = count( $decoded );
	$decoded = array_values( array_filter( $decoded, function ( $el ) use ( $id ) {
		return ( $el['id'] ?? '' ) !== $id;
	} ) );
	if ( count( $decoded ) === $before ) {
		return new \WP_Error( 'not_found', 'No top-level element with that id.', array( 'status' => 404 ) );
	}
	update_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', wp_slash( wp_json_encode( $decoded ) ) );
	if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
		try {
			delete_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_css' );
			\Elementor\Core\Files\CSS\Post::create( KOSEI_DANA_PAGE_ID )->update();
		} catch ( \Throwable $e ) { /* non-fatal */ }
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( array( 'ok' => true, 'before_count' => $before, 'after_count' => count( $decoded ) ), 200 );
}

function kosei_dana_diag() {
	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	$out = array(
		'top_elements' => is_array( $decoded ) ? count( $decoded ) : null,
		'render_error' => null,
		'rendered_len' => null,
	);
	if ( is_array( $decoded ) ) {
		$types = array();
		$walk = function ( $els ) use ( &$walk, &$types ) {
			foreach ( (array) $els as $el ) {
				$k = ( $el['elType'] ?? '?' ) . ( isset( $el['widgetType'] ) ? ':' . $el['widgetType'] : '' );
				$types[ $k ] = ( $types[ $k ] ?? 0 ) + 1;
				if ( ! empty( $el['elements'] ) ) { $walk( $el['elements'] ); }
			}
		};
		$walk( $decoded );
		$out['element_types'] = $types;
	}
	$out['meta'] = array(
		'_elementor_edit_mode'     => get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_edit_mode', true ),
		'_elementor_template_type' => get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_template_type', true ),
		'_elementor_version'       => get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_version', true ),
	);
	global $wp_object_cache;
	$out['persistent_object_cache'] = wp_using_ext_object_cache();

	if ( class_exists( '\Elementor\Plugin' ) ) {
		try {
			$doc = \Elementor\Plugin::$instance->documents->get( KOSEI_DANA_PAGE_ID );
			if ( $doc ) {
				// What does the Document object itself think the elements are,
				// independent of HTML rendering -- if THIS is also 10 (not 11),
				// the Document is reading/caching from somewhere other than the
				// _elementor_data we just verified has 11 elements.
				$doc_elements = $doc->get_elements_data();
				$out['document_top_element_count'] = is_array( $doc_elements ) ? count( $doc_elements ) : null;
				$out['document_last_element_type']  = is_array( $doc_elements ) && $doc_elements
					? ( ( end( $doc_elements )['elType'] ?? '?' ) . ':' . ( end( $doc_elements )['widgetType'] ?? '' ) )
					: null;
			}
			$html = $doc ? $doc->get_content() : '(no document)';
			$out['rendered_len']   = strlen( (string) $html );
			$out['has_new_widget'] = ( false !== strpos( (string) $html, 'kosei-podcast-archive' ) );
			$out['rendered_tail']  = substr( (string) $html, -900 );
		} catch ( \Throwable $e ) {
			$out['render_error'] = $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
		}
	}
	$log = WP_CONTENT_DIR . '/debug.log';
	if ( file_exists( $log ) ) {
		$out['debug_log_tail'] = array_slice( array_filter( explode( "\n", (string) file_get_contents( $log ) ) ), -15 );
	}
	return new \WP_REST_Response( $out, 200 );
}

function kosei_dana_purge_cache() {
	$fired = array();
	do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
	$fired[] = 'litespeed_purge_post';
	do_action( 'litespeed_purge_all' );
	$fired[] = 'litespeed_purge_all';
	return new \WP_REST_Response( array( 'ok' => true, 'fired' => $fired ), 200 );
}

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
		'ok'           => true,
		'top_elements' => count( $decoded ),
		'existing_ids' => array_values( array_unique( $ids ) ),
		'data'         => $decoded,
		'modified'     => get_post_modified_time( 'c', false, KOSEI_DANA_PAGE_ID ),
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
	$decoded[]    = $new_section;

	update_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', wp_slash( wp_json_encode( $decoded ) ) );

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
	do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
	do_action( 'litespeed_purge_all' );

	return new \WP_REST_Response( array(
		'ok'           => true,
		'before_count' => $before_count,
		'after_count'  => count( $decoded ),
		'css_regen'    => $css_regen,
		'view'         => get_permalink( KOSEI_DANA_PAGE_ID ),
	), 200 );
}
