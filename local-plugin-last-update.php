<?php
/**
 * Plugin Name: Local Plugin Last Update
 * Description: Displays the date that plugins were last updated on this site, with a sortable column header.
 * Version: 1.1.0
 * Author: Topher
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Tested up to:      6.9.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: local-plugin-last-update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Last_Updated_Column {

	public function __construct() {
		add_filter( 'manage_plugins_columns',          array( $this, 'add_column' ) );
		add_filter( 'manage_plugins_sortable_columns', array( $this, 'sortable_column' ) );
		add_action( 'manage_plugins_custom_column',    array( $this, 'render_column' ), 10, 3 );
		add_action( 'admin_enqueue_scripts',           array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the "Last Updated" column.
	 */
	public function add_column( $columns ) {
		$columns['last_updated'] = __( 'Last Updated', 'local-plugin-last-update' );
		return $columns;
	}

	/**
	 * Declare the column as sortable so WordPress renders the header as a link.
	 */
	public function sortable_column( $sortable ) {
		$sortable['last_updated'] = 'last_updated';
		return $sortable;
	}

	/**
	 * Render the last-updated date for each plugin row.
	 * Stores a Unix timestamp in data-timestamp so JS can sort accurately.
	 */
	public function render_column( $column_name, $plugin_file, $plugin_data ) {
		if ( $column_name !== 'last_updated' ) {
			return;
		}

		$plugin_path   = WP_PLUGIN_DIR . '/' . $plugin_file;
		$modified_time = file_exists( $plugin_path ) ? filemtime( $plugin_path ) : false;

		if ( $modified_time ) {
			printf(
				'<span data-timestamp="%d">%s</span>',
				(int) $modified_time,
				esc_html( date_i18n( 'j M, Y', $modified_time ) )
			);
		} else {
			echo '<em data-timestamp="0">' . esc_html__( 'Unknown', 'local-plugin-last-update' ) . '</em>';
		}
	}

	/**
	 * Enqueue CSS + JS on the plugins screen only.
	 */
	public function enqueue_assets( $hook ) {
		if ( $hook !== 'plugins.php' ) {
			return;
		}

		// Column width — inline is fine, avoids an extra HTTP request.
		wp_add_inline_style( 'wp-admin', '.column-last_updated { width: 130px; }' );

		// Sorting script — hooks into jQuery which WP always loads on plugins.php.
		wp_add_inline_script( 'jquery', $this->sort_script() );
	}

	/**
	 * Returns the client-side sort script.
	 *
	 * Clicking the column header lets WordPress reload the page with
	 * ?orderby=last_updated&order=asc|desc. This script detects those params
	 * and reorders the rows by their data-timestamp values, keeping any
	 * plugin-update-tr rows paired with their parent plugin row.
	 */
	private function sort_script() {
		return <<<'JS'
( function( $ ) {
	'use strict';

	$( function() {
		var params  = new URLSearchParams( window.location.search );
		var orderby = params.get( 'orderby' );
		var order   = ( params.get( 'order' ) || 'asc' ).toLowerCase();

		if ( orderby !== 'last_updated' ) {
			return;
		}

		var $tbody = $( '#the-list' );

		// Grab plugin rows only (skip the inline update-detail rows).
		var $rows = $tbody.children( 'tr' ).not( '.plugin-update-tr' );

		var sorted = $rows.toArray().sort( function( a, b ) {
			var tsA = parseInt( $( a ).find( '[data-timestamp]' ).attr( 'data-timestamp' ) || 0, 10 );
			var tsB = parseInt( $( b ).find( '[data-timestamp]' ).attr( 'data-timestamp' ) || 0, 10 );
			return order === 'asc' ? tsA - tsB : tsB - tsA;
		} );

		// Re-append each row, dragging its update-detail row along with it.
		$( sorted ).each( function() {
			var $row       = $( this );
			var pluginSlug = $row.attr( 'data-plugin' );
			var $updateRow = $tbody.find( '.plugin-update-tr[data-plugin="' + pluginSlug + '"]' );

			$tbody.append( $row );
			if ( $updateRow.length ) {
				$tbody.append( $updateRow );
			}
		} );
	} );
} )( jQuery );
JS;
	}
}

new Plugin_Last_Updated_Column();
