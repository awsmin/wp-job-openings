/**
 * Locks the Pro-only choices (Stack layout, Filtered listing, Multiple-select filters)
 * in the free "Job Listings" Elementor widget's own controls, so they're visible with
 * a "Pro" badge but can't actually be selected. Only enqueued when Pro Pack isn't
 * active — see AWSM_Job_Openings_Elementor::enqueue_widget_pro_lock_script().
 *
 * Elementor's native SELECT/CHOOSE control templates don't support a disabled/locked
 * option, so this patches the rendered DOM directly instead. A MutationObserver keeps
 * re-applying the lock because Elementor re-renders these controls whenever a
 * 'condition'-linked setting changes, which would otherwise wipe out a one-time patch.
 */
( function( $ ) {
	'use strict';

	if ( typeof elementor === 'undefined' ) {
		return;
	}

	var WIDGET_TYPE = 'awsm-job-listings';
	var observer = null;

	function lockSelectOption( $panel, controlName, lockedValue ) {
		$panel
			.find( 'select[data-setting="' + controlName + '"] option[value="' + lockedValue + '"]' )
			.prop( 'disabled', true );
	}

	function lockFilterTypeChoices( $panel ) {
		$panel.find( 'input[type="radio"][value="checkbox"]' ).each( function() {
			var $input = $( this );
			var name = $input.attr( 'name' ) || '';

			if ( name.indexOf( 'elementor-choose-filter_type_' ) !== 0 ) {
				return;
			}

			$input.prop( 'disabled', true );

			var $label = $input.next( 'label' );
			if ( $label.length && ! $label.hasClass( 'awsm-pro-locked' ) ) {
				$label.addClass( 'awsm-pro-locked' );
				$label.append( '<span class="awsm-pro-badge">Pro</span>' );
			}
		} );
	}

	function applyLock( $panel ) {
		lockSelectOption( $panel, 'layout', 'stack' );
		lockSelectOption( $panel, 'list_type', 'filtered' );
		lockFilterTypeChoices( $panel );
	}

	elementor.hooks.addAction( 'panel/open_editor/widget/' + WIDGET_TYPE, function( panel, model, view ) {
		var $panel = view.$el;

		if ( observer ) {
			observer.disconnect();
		}

		// Elementor finishes rendering this panel's controls right after this hook
		// fires, so defer the first pass; the observer covers every render after that.
		_.defer( function() {
			applyLock( $panel );
		} );

		observer = new MutationObserver( function() {
			applyLock( $panel );
		} );
		observer.observe( $panel[ 0 ], { childList: true, subtree: true } );
	} );
} )( jQuery );
