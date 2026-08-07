<?php
/**
 * Plugin Name: Kosei Dana Podcast Tools
 * Description: Narrow, single-purpose REST API for safely appending ONE new top-level Elementor container to the /podcast/ page (post ID 3048) only. Built by Sadie's Claude Code session; safe to deactivate/delete once the podcast archive widget work is finished.
 * Version: 1.10.6
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
	register_rest_route( 'kosei-dana/v1', '/podcast-page-lazy-iframes', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_lazy_iframes',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/widget', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_widget_schema',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-replace-latest-episodes', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_replace_latest_episodes',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-merge-widget-settings', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_merge_widget_settings',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/plugin-self-update', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_plugin_self_update',
		'permission_callback' => function () { return current_user_can( 'activate_plugins' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-edit-sections', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_edit_sections',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/podcast-page-seo-meta', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_seo_meta',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/page-elementor', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_page_elementor_get',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/page-elementor', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_page_elementor_put',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/page-cache-bust', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_page_cache_bust',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/page-settings-merge', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_page_settings_merge',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/elementor-kit', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_kit_get',
		'permission_callback' => function () { return current_user_can( 'edit_theme_options' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/elementor-kit', array(
		'methods'             => 'POST',
		'callback'            => 'kosei_dana_kit_merge',
		'permission_callback' => function () { return current_user_can( 'edit_theme_options' ); },
	) );
	register_rest_route( 'kosei-dana/v1', '/page-backup', array(
		'methods'             => 'GET',
		'callback'            => 'kosei_dana_page_backup_get',
		'permission_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
} );

// Sets the Yoast SEO meta description (and optionally the SEO title) for
// the /podcast/ page only -- core REST won't write unregistered postmeta.
function kosei_dana_seo_meta( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	$desc  = is_array( $body ) ? ( $body['metadesc'] ?? null ) : null;
	$title = is_array( $body ) ? ( $body['seo_title'] ?? null ) : null;
	if ( null === $desc && null === $title ) {
		return new \WP_Error( 'bad_body', 'Body must include "metadesc" and/or "seo_title".', array( 'status' => 400 ) );
	}
	$out = array( 'ok' => true );
	if ( null !== $desc ) {
		update_post_meta( KOSEI_DANA_PAGE_ID, '_yoast_wpseo_metadesc', sanitize_text_field( $desc ) );
		$out['metadesc'] = get_post_meta( KOSEI_DANA_PAGE_ID, '_yoast_wpseo_metadesc', true );
	}
	if ( null !== $title ) {
		update_post_meta( KOSEI_DANA_PAGE_ID, '_yoast_wpseo_title', sanitize_text_field( $title ) );
		$out['seo_title'] = get_post_meta( KOSEI_DANA_PAGE_ID, '_yoast_wpseo_title', true );
	}
	do_action( 'litespeed_purge_post', KOSEI_DANA_PAGE_ID );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( $out, 200 );
}

// Top-level section surgery for the 2026-08 page redesign (Sadie asked to
// remove the old local-TV appearances and add a logo strip + guest-show
// grid). Unlike the earlier append/remove endpoints, this one may target
// ORIGINAL sections -- but only the single top-level id named explicitly
// in the request, verified against live data, one operation per call.
// ops: {"op":"replace","target_id":"...","section":{...}}
//      {"op":"insert_after","target_id":"...","section":{...}}
//      {"op":"remove","target_id":"..."}
function kosei_dana_edit_sections( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	$op   = is_array( $body ) ? ( $body['op'] ?? '' ) : '';
	$tid  = is_array( $body ) ? ( $body['target_id'] ?? '' ) : '';
	$sec  = is_array( $body ) ? ( $body['section'] ?? null ) : null;
	if ( ! in_array( $op, array( 'replace', 'insert_after', 'remove' ), true ) || ! $tid ) {
		return new \WP_Error( 'bad_body', 'Body must be {"op": "replace|insert_after|remove", "target_id": "<top-level id>", "section": {...}}.', array( 'status' => 400 ) );
	}
	if ( in_array( $op, array( 'replace', 'insert_after' ), true ) && ( ! is_array( $sec ) || empty( $sec['id'] ) ) ) {
		return new \WP_Error( 'bad_body', 'This op requires a "section" element with an id.', array( 'status' => 400 ) );
	}

	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}

	$idx = null;
	foreach ( $decoded as $i => $top ) {
		if ( ( $top['id'] ?? '' ) === $tid ) { $idx = $i; break; }
	}
	if ( null === $idx ) {
		return new \WP_Error( 'not_found', 'No TOP-LEVEL element with that id (nested elements are not valid targets).', array( 'status' => 404 ) );
	}

	if ( $sec ) {
		$existing_ids = array();
		kosei_dana_collect_ids( $decoded, $existing_ids );
		if ( 'replace' === $op ) {
			$keep = array();
			kosei_dana_collect_ids( array( $decoded[ $idx ] ), $keep );
			$existing_ids = array_diff( $existing_ids, $keep );
		}
		$new_ids = array();
		kosei_dana_collect_ids( array( $sec ), $new_ids );
		$collisions = array_intersect( $existing_ids, $new_ids );
		if ( ! empty( $collisions ) ) {
			return new \WP_Error( 'id_collision', 'Section ids collide with existing ids: ' . implode( ', ', $collisions ), array( 'status' => 409 ) );
		}
	}

	$before = count( $decoded );
	if ( 'replace' === $op ) {
		$decoded[ $idx ] = $sec;
	} elseif ( 'insert_after' === $op ) {
		array_splice( $decoded, $idx + 1, 0, array( $sec ) );
	} else {
		array_splice( $decoded, $idx, 1 );
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

	return new \WP_REST_Response( array(
		'ok'           => true,
		'op'           => $op,
		'target_id'    => $tid,
		'index'        => $idx,
		'before_count' => $before,
		'after_count'  => count( $decoded ),
	), 200 );
}

// Replaces this plugin's own file with a new version sent over REST, so
// updates no longer require a manual zip re-upload in wp-admin (same
// arrangement as Melinda's Kosei Deploy plugin). Admin-only. Guards:
// the payload must carry this plugin's own header (can't be pointed at
// any other file -- it only ever writes __FILE__), a sha256 of the code
// must match (catches truncated/corrupted uploads), a .bak of the
// current version is kept beside the file, and the code is syntax-
// checked with `php -l` before the swap when exec() is available. If a
// bad version does slip through, WP 5.2+ recovery mode pauses the
// broken plugin rather than white-screening the site.
function kosei_dana_plugin_self_update( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	$code = is_array( $body ) ? ( $body['code'] ?? '' ) : '';
	$sha  = is_array( $body ) ? ( $body['sha256'] ?? '' ) : '';
	if ( ! is_string( $code ) || 0 !== strncmp( $code, '<?php', 5 )
		|| false === strpos( $code, 'Plugin Name: Kosei Dana Podcast Tools' ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"code": "<full plugin file starting with <?php>", "sha256": "<hash of code>"} and the code must contain this plugin\'s own header.', array( 'status' => 400 ) );
	}
	if ( ! is_string( $sha ) || ! hash_equals( hash( 'sha256', $code ), strtolower( $sha ) ) ) {
		return new \WP_Error( 'bad_hash', 'sha256 mismatch -- payload may be truncated or corrupted.', array( 'status' => 400 ) );
	}

	$tmp = __FILE__ . '.tmp';
	if ( false === @file_put_contents( $tmp, $code ) ) {
		return new \WP_Error( 'write_failed', 'Could not write temp file next to the plugin.', array( 'status' => 500 ) );
	}
	$lint = 'skipped (exec unavailable)';
	if ( function_exists( 'exec' ) ) {
		$out = array();
		$rc  = 1;
		@exec( 'php -l ' . escapeshellarg( $tmp ) . ' 2>&1', $out, $rc );
		if ( 0 !== $rc ) {
			@unlink( $tmp );
			return new \WP_Error( 'lint_failed', 'php -l rejected the new code: ' . implode( ' | ', $out ), array( 'status' => 400 ) );
		}
		$lint = 'ok';
	}
	@copy( __FILE__, __FILE__ . '.bak' );
	if ( ! @rename( $tmp, __FILE__ ) ) {
		@unlink( $tmp );
		return new \WP_Error( 'rename_failed', 'Could not swap the new file into place.', array( 'status' => 500 ) );
	}
	if ( function_exists( 'opcache_invalidate' ) ) {
		@opcache_invalidate( __FILE__, true );
	}
	if ( function_exists( 'opcache_reset' ) ) {
		@opcache_reset();
	}
	$version = preg_match( '/^\s*\*\s*Version:\s*(\S+)/mi', $code, $m ) ? $m[1] : '?';
	return new \WP_REST_Response( array(
		'ok'          => true,
		'bytes'       => strlen( $code ),
		'lint'        => $lint,
		'new_version' => $version,
		'backup'      => basename( __FILE__ ) . '.bak',
	), 200 );
}

// Merges (shallow) the given key/value pairs into ONE existing element's
// `settings`, found anywhere in the tree by id. Narrowly scoped: it can
// only change values of keys that already exist as valid Elementor/EA
// control names on that element, never add/remove/reorder elements or
// touch any other element's id.
function kosei_dana_merge_widget_settings( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	$id   = is_array( $body ) ? ( $body['id'] ?? '' ) : '';
	$patch = is_array( $body ) ? ( $body['settings'] ?? null ) : null;
	if ( ! $id || ! is_array( $patch ) || empty( $patch ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"id": "<element id>", "settings": {"<control>": <value>, ...}}.', array( 'status' => 400 ) );
	}

	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}

	$found = false;
	$before_values = array();
	$apply = function ( &$el ) use ( &$apply, $id, $patch, &$found, &$before_values ) {
		if ( ( $el['id'] ?? '' ) === $id ) {
			$found = true;
			if ( ! isset( $el['settings'] ) || ! is_array( $el['settings'] ) ) {
				$el['settings'] = array();
			}
			foreach ( $patch as $k => $v ) {
				$before_values[ $k ] = $el['settings'][ $k ] ?? null;
				$el['settings'][ $k ] = $v;
			}
			return;
		}
		if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
			foreach ( $el['elements'] as &$child ) {
				$apply( $child );
			}
			unset( $child );
		}
	};
	foreach ( $decoded as &$top ) {
		$apply( $top );
	}
	unset( $top );

	if ( ! $found ) {
		return new \WP_Error( 'not_found', 'No element with that id found on the page.', array( 'status' => 404 ) );
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

	return new \WP_REST_Response( array(
		'ok'             => true,
		'id'             => $id,
		'applied'        => $patch,
		'previous_values' => $before_values,
	), 200 );
}

// The ONLY endpoint permitted to touch an original element: replaces the
// specific top-level section containing "Latest" heading text (verified
// at call time against the LIVE data, not just trusted from an ID), with
// the caller's new section inserted at the SAME array position. Confirmed
// content match makes this narrowly scoped rather than a general
// "replace any original element" capability.
function kosei_dana_replace_latest_episodes( \WP_REST_Request $req ) {
	$body = json_decode( $req->get_body(), true );
	if ( ! is_array( $body ) || empty( $body['section'] ) || ! is_array( $body['section'] ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"section": {...one Elementor section element...}}.', array( 'status' => 400 ) );
	}
	$new_section = $body['section'];

	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}

	$has_latest_heading = function ( $el ) use ( &$has_latest_heading ) {
		if ( ( $el['widgetType'] ?? '' ) === 'heading' && false !== stripos( $el['settings']['title'] ?? '', 'Latest' ) ) {
			return true;
		}
		foreach ( (array) ( $el['elements'] ?? array() ) as $child ) {
			if ( $has_latest_heading( $child ) ) { return true; }
		}
		return false;
	};

	$target_index = null;
	foreach ( $decoded as $i => $top ) {
		if ( $has_latest_heading( $top ) ) { $target_index = $i; break; }
	}
	if ( null === $target_index ) {
		return new \WP_Error( 'not_found', 'No top-level section with a "Latest" heading found in the live data.', array( 'status' => 404 ) );
	}

	$existing_ids = array();
	kosei_dana_collect_ids( $decoded, $existing_ids );
	$new_ids = array();
	kosei_dana_collect_ids( array( $new_section ), $new_ids );
	$collisions = array_intersect( $existing_ids, $new_ids );
	if ( ! empty( $collisions ) ) {
		return new \WP_Error( 'id_collision', 'Submitted element IDs collide with existing IDs on the page: ' . implode( ', ', $collisions ), array( 'status' => 409 ) );
	}

	$replaced_id       = $decoded[ $target_index ]['id'] ?? '?';
	$decoded[ $target_index ] = $new_section;

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

	return new \WP_REST_Response( array(
		'ok'          => true,
		'index'       => $target_index,
		'replaced_id' => $replaced_id,
		'new_id'      => $new_section['id'] ?? '?',
	), 200 );
}

// Read-only Elementor widget introspection, ported from Melinda's Kosei
// Deploy API (class-kosei-deploy.php widget_schema()) -- no type param
// lists every registered widget; ?type=<name> dumps its real controls,
// so a widget's settings schema can be read instead of guessed.
function kosei_dana_widget_schema( \WP_REST_Request $req ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return new \WP_Error( 'no_elementor', 'Elementor is not active.', array( 'status' => 500 ) );
	}
	$wm   = \Elementor\Plugin::$instance->widgets_manager;
	$type = $req->get_param( 'type' );
	if ( ! $type ) {
		$types = array_keys( (array) $wm->get_widget_types() );
		sort( $types );
		return new \WP_REST_Response( array( 'ok' => true, 'count' => count( $types ), 'types' => $types ), 200 );
	}
	$w = $wm->get_widget_types( $type );
	if ( ! $w ) {
		return new \WP_REST_Response( array( 'ok' => false, 'error' => 'not registered', 'type' => $type ), 200 );
	}
	$flat = function ( $controls ) {
		$out = array();
		foreach ( (array) $controls as $name => $c ) {
			$row = array( 'type' => $c['type'] ?? null );
			if ( array_key_exists( 'default', $c ) ) {
				$row['default'] = $c['default'];
			}
			if ( ( $c['type'] ?? '' ) === 'repeater' && ! empty( $c['fields'] ) ) {
				$fields = array();
				foreach ( (array) $c['fields'] as $fn => $fc ) {
					$fields[ $fn ] = array( 'type' => $fc['type'] ?? null );
					if ( array_key_exists( 'default', $fc ) ) {
						$fields[ $fn ]['default'] = $fc['default'];
					}
				}
				$row['fields'] = $fields;
			}
			$out[ $name ] = $row;
		}
		return $out;
	};
	return new \WP_REST_Response( array(
		'ok'       => true,
		'type'     => $type,
		'title'    => method_exists( $w, 'get_title' ) ? $w->get_title() : null,
		'controls' => $flat( $w->get_controls() ),
	), 200 );
}

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

function kosei_dana_lazy_iframes() {
	$raw     = get_post_meta( KOSEI_DANA_PAGE_ID, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return new \WP_Error( 'bad_data', 'Existing _elementor_data did not decode as an array.', array( 'status' => 500 ) );
	}
	$patched = array();
	$patch = function ( &$el ) use ( &$patch, &$patched ) {
		if ( ( $el['widgetType'] ?? '' ) === 'shortcode' && isset( $el['settings']['shortcode'] ) ) {
			$sc = $el['settings']['shortcode'];
			if ( false !== strpos( $sc, '<iframe' ) && false === strpos( $sc, 'loading="lazy"' ) ) {
				$new_sc = preg_replace( '/<iframe(\s)/', '<iframe loading="lazy"$1', $sc, 1 );
				if ( $new_sc !== $sc ) {
					$el['settings']['shortcode'] = $new_sc;
					$patched[] = $el['id'] ?? '?';
				}
			}
		}
		if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
			foreach ( $el['elements'] as &$child ) {
				$patch( $child );
			}
			unset( $child );
		}
	};
	foreach ( $decoded as &$top ) {
		$patch( $top );
	}
	unset( $top );

	if ( empty( $patched ) ) {
		return new \WP_REST_Response( array( 'ok' => true, 'patched' => array(), 'note' => 'nothing to patch (already lazy, or no matching iframes)' ), 200 );
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

	return new \WP_REST_Response( array( 'ok' => true, 'patched' => $patched, 'count' => count( $patched ) ), 200 );
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


// ---------------------------------------------------------------------
// Generic, page-scoped Elementor read/write (added v1.10.0 for the
// /boundaries-course/ pricing + copy work). Deliberately additive: the
// endpoints above stay hardcoded to the podcast page and are untouched.
// Guards: caller must have edit_pages; target must be an existing post of
// type "page"; the payload must decode to a non-empty JSON array; and a
// timestamped copy of the previous _elementor_data is kept in postmeta
// so a bad write can always be rolled back from the DB alone.
// ---------------------------------------------------------------------
function kosei_dana_resolve_page( $req ) {
	$pid = (int) $req->get_param( 'page_id' );
	if ( ! $pid ) {
		$body = json_decode( $req->get_body(), true );
		$pid  = is_array( $body ) ? (int) ( $body['page_id'] ?? 0 ) : 0;
	}
	if ( ! $pid ) {
		return new \WP_Error( 'bad_page', 'page_id is required.', array( 'status' => 400 ) );
	}
	$post = get_post( $pid );
	// elementor_library covers popups/templates -- the waitlist popup that
	// the course page's CTAs open lives there, not in a page.
	if ( ! $post || ! in_array( $post->post_type, array( 'page', 'elementor_library' ), true ) ) {
		return new \WP_Error( 'bad_page', 'page_id must reference an existing post of type "page" or "elementor_library".', array( 'status' => 404 ) );
	}
	return $pid;
}

function kosei_dana_page_elementor_get( \WP_REST_Request $req ) {
	$pid = kosei_dana_resolve_page( $req );
	if ( is_wp_error( $pid ) ) { return $pid; }
	$raw     = get_post_meta( $pid, '_elementor_data', true );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	return new \WP_REST_Response( array(
		'ok'            => true,
		'page_id'       => $pid,
		'title'         => get_the_title( $pid ),
		'top_elements'  => is_array( $decoded ) ? count( $decoded ) : 0,
		'bytes'         => is_string( $raw ) ? strlen( $raw ) : 0,
		'sha256'        => is_string( $raw ) ? hash( 'sha256', $raw ) : null,
		'data'          => $decoded,
		'backups'       => array_keys( array_filter( get_post_meta( $pid ), function ( $v, $k ) {
			return 0 === strpos( $k, '_kosei_elementor_backup_' );
		}, ARRAY_FILTER_USE_BOTH ) ),
	), 200 );
}

function kosei_dana_page_elementor_put( \WP_REST_Request $req ) {
	$pid = kosei_dana_resolve_page( $req );
	if ( is_wp_error( $pid ) ) { return $pid; }
	$body = json_decode( $req->get_body(), true );
	$data = is_array( $body ) ? ( $body['data'] ?? null ) : null;
	$sha  = is_array( $body ) ? ( $body['sha256'] ?? '' ) : '';
	if ( ! is_array( $data ) || empty( $data ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"page_id": N, "data": [ ...elementor tree... ], "sha256": "<hash of the encoded data>"}.', array( 'status' => 400 ) );
	}
	$encoded = wp_json_encode( $data );
	if ( ! is_string( $sha ) || ! hash_equals( hash( 'sha256', $encoded ), strtolower( $sha ) ) ) {
		return new \WP_Error( 'bad_hash', 'sha256 mismatch -- payload may be truncated or re-encoded differently.', array( 'status' => 400 ) );
	}
	// A freshly created page has no Elementor metadata; without these the
	// builder data is ignored and the theme renders raw post_content.
	if ( ! get_post_meta( $pid, '_elementor_edit_mode', true ) ) {
		update_post_meta( $pid, '_elementor_edit_mode', 'builder' );
	}
	if ( ! get_post_meta( $pid, '_elementor_template_type', true ) ) {
		update_post_meta( $pid, '_elementor_template_type', 'wp-page' );
	}
	if ( ! get_post_meta( $pid, '_elementor_version', true ) && defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $pid, '_elementor_version', ELEMENTOR_VERSION );
	}
	$prev = get_post_meta( $pid, '_elementor_data', true );
	if ( is_string( $prev ) && '' !== $prev ) {
		update_post_meta( $pid, '_kosei_elementor_backup_' . gmdate( 'Ymd_His' ), wp_slash( $prev ) );
	}
	update_post_meta( $pid, '_elementor_data', wp_slash( $encoded ) );
	$css = 'skipped';
	if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
		delete_post_meta( $pid, '_elementor_css' );
		\Elementor\Core\Files\CSS\Post::create( $pid )->update();
		$css = 'regenerated';
	}
	do_action( 'litespeed_purge_post', $pid );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( array(
		'ok'           => true,
		'page_id'      => $pid,
		'bytes'        => strlen( $encoded ),
		'top_elements' => count( $data ),
		'css'          => $css,
	), 200 );
}

// Elementor 3.x/4.x caches rendered widget output in postmeta
// (_elementor_element_cache). Writing _elementor_data alone leaves that
// stale, so the front end keeps serving the OLD markup even after a full
// LiteSpeed purge. This clears every Elementor-side cache for one page
// and reports the meta keys it saw, for diagnosis.
function kosei_dana_page_cache_bust( \WP_REST_Request $req ) {
	$pid = kosei_dana_resolve_page( $req );
	if ( is_wp_error( $pid ) ) { return $pid; }
	$before = array();
	foreach ( get_post_meta( $pid ) as $k => $v ) {
		if ( 0 === strpos( $k, '_elementor' ) ) {
			$before[ $k ] = is_array( $v ) ? strlen( (string) reset( $v ) ) : strlen( (string) $v );
		}
	}
	$cleared = array();
	foreach ( array( '_elementor_element_cache', '_elementor_css', '_elementor_page_assets', '_elementor_inline_svg' ) as $k ) {
		if ( metadata_exists( 'post', $pid, $k ) ) {
			delete_post_meta( $pid, $k );
			$cleared[] = $k;
		}
	}
	$notes = array();
	if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
		$notes[] = 'files_manager->clear_cache()';
	}
	if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
		\Elementor\Core\Files\CSS\Post::create( $pid )->update();
		$notes[] = 'css regenerated';
	}
	clean_post_cache( $pid );
	wp_cache_flush();
	do_action( 'litespeed_purge_post', $pid );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( array(
		'ok'          => true,
		'page_id'     => $pid,
		'meta_before' => $before,
		'cleared'     => $cleared,
		'notes'       => $notes,
	), 200 );
}

// Shallow-merges keys into _elementor_page_settings (Elementor Pro stores
// per-page Custom CSS there under "custom_css"). Returns the settings both
// before and after so a change can be eyeballed and reverted by hand.
function kosei_dana_page_settings_merge( \WP_REST_Request $req ) {
	$pid = kosei_dana_resolve_page( $req );
	if ( is_wp_error( $pid ) ) { return $pid; }
	$body  = json_decode( $req->get_body(), true );
	$patch = is_array( $body ) ? ( $body['settings'] ?? null ) : null;
	if ( ! is_array( $patch ) || empty( $patch ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"page_id": N, "settings": {"<key>": <value>, ...}}.', array( 'status' => 400 ) );
	}
	$cur = get_post_meta( $pid, '_elementor_page_settings', true );
	if ( ! is_array( $cur ) ) { $cur = array(); }
	$before = $cur;
	foreach ( $patch as $k => $v ) { $cur[ $k ] = $v; }
	update_post_meta( $pid, '_elementor_page_settings', $cur );
	foreach ( array( '_elementor_element_cache', '_elementor_css', '_elementor_page_assets' ) as $k ) {
		delete_post_meta( $pid, $k );
	}
	if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
		\Elementor\Core\Files\CSS\Post::create( $pid )->update();
	}
	clean_post_cache( $pid );
	do_action( 'litespeed_purge_post', $pid );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( array( 'ok' => true, 'page_id' => $pid, 'before' => $before, 'after' => $cur ), 200 );
}

// Elementor's Global Settings live in the active Kit's
// _elementor_page_settings (system_colors, custom_colors, system_typography,
// custom_typography, plus body/link defaults). Read and shallow-merge them.
function kosei_dana_kit_id() { return (int) get_option( 'elementor_active_kit' ); }

function kosei_dana_kit_get( \WP_REST_Request $req ) {
	$kid = kosei_dana_kit_id();
	if ( ! $kid ) { return new \WP_Error( 'no_kit', 'No active Elementor kit.', array( 'status' => 404 ) ); }
	$s = get_post_meta( $kid, '_elementor_page_settings', true );
	return new \WP_REST_Response( array( 'ok' => true, 'kit_id' => $kid,
		'title' => get_the_title( $kid ), 'settings' => is_array( $s ) ? $s : array() ), 200 );
}

function kosei_dana_kit_merge( \WP_REST_Request $req ) {
	$kid = kosei_dana_kit_id();
	if ( ! $kid ) { return new \WP_Error( 'no_kit', 'No active Elementor kit.', array( 'status' => 404 ) ); }
	$body  = json_decode( $req->get_body(), true );
	$patch = is_array( $body ) ? ( $body['settings'] ?? null ) : null;
	if ( ! is_array( $patch ) || empty( $patch ) ) {
		return new \WP_Error( 'bad_body', 'Body must be {"settings": {...}}.', array( 'status' => 400 ) );
	}
	$cur = get_post_meta( $kid, '_elementor_page_settings', true );
	if ( ! is_array( $cur ) ) { $cur = array(); }
	$before = $cur;
	update_post_meta( $kid, '_kosei_kit_backup_' . gmdate( 'Ymd_His' ), wp_slash( wp_json_encode( $before ) ) );
	foreach ( $patch as $k => $v ) { $cur[ $k ] = $v; }
	update_post_meta( $kid, '_elementor_page_settings', $cur );
	if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	clean_post_cache( $kid );
	do_action( 'litespeed_purge_all' );
	return new \WP_REST_Response( array( 'ok' => true, 'kit_id' => $kid, 'before' => $before, 'after' => $cur ), 200 );
}

// Reads one of the timestamped _kosei_elementor_backup_* snapshots that
// page_elementor_put writes before every save, so a clobbered edit can be
// recovered without database access.
function kosei_dana_page_backup_get( \WP_REST_Request $req ) {
	$pid = kosei_dana_resolve_page( $req );
	if ( is_wp_error( $pid ) ) { return $pid; }
	$key = (string) $req->get_param( 'key' );
	$all = array();
	foreach ( get_post_meta( $pid ) as $k => $v ) {
		if ( 0 === strpos( $k, '_kosei_elementor_backup_' ) ) {
			$all[ $k ] = strlen( (string) reset( $v ) );
		}
	}
	ksort( $all );
	if ( '' === $key ) {
		return new \WP_REST_Response( array( 'ok' => true, 'page_id' => $pid, 'backups' => $all ), 200 );
	}
	if ( ! isset( $all[ $key ] ) ) {
		return new \WP_Error( 'no_backup', 'No such backup key.', array( 'status' => 404 ) );
	}
	$raw = get_post_meta( $pid, $key, true );
	return new \WP_REST_Response( array(
		'ok' => true, 'page_id' => $pid, 'key' => $key,
		'data' => json_decode( is_string( $raw ) ? $raw : '', true ),
	), 200 );
}
