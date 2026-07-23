/**
 * Locks the Pro-only choices (Stack layout, Filtered listing, Multiple-select filters)
 * in the free "Job Listings" Elementor widget's own controls, so they're visible with
 * a "Pro" badge but can't actually be selected. Only enqueued when Pro Pack isn't
 * active — see AWSM_Job_Openings_Elementor::enqueue_widget_pro_lock_script().
 *
 * Elementor's native SELECT/CHOOSE control templates don't support a disabled/locked
 * option, so this patches the DOM directly instead, using two layers that don't depend
 * on any specific Elementor lifecycle hook (e.g. "panel opened for this widget type")
 * actually firing — this watches and reacts to the editor's DOM directly:
 *
 * 1. A MutationObserver on the whole editor document, from script load onward, that
 *    disables the locked <option>/<input> and adds the "Pro" badge wherever they
 *    appear — covers first render, re-renders, and duplicating an existing widget.
 * 2. A capture-phase `change` listener on `document`, using event delegation, that
 *    independently re-checks every change on these controls and blocks a NEW attempt
 *    to select the locked value, snapping it back to its safe fallback — this is what
 *    actually guarantees the option can't be switched to, regardless of whether layer
 *    1's `disabled` patch happens to be applied at that exact moment.
 *
 * Deliberately does NOT touch a value that's already saved as the locked one (e.g. from
 * before Pro Pack was deactivated) — only the option/input is disabled and new attempts
 * to change TO it are blocked. The already-saved choice stays intact and inert (the
 * free widget's own render() already guarantees a locked value never has any effect
 * while Pro Pack is inactive — see class-awsm-job-openings-elementor-widget.php), so it
 * comes back automatically if Pro Pack is reactivated later, instead of being silently
 * overwritten every time the editor happens to be opened while Pro Pack is off.
 */
( function( $ ) {
	'use strict';

	var LOCKED_SELECT_VALUES = {
		layout: { locked: 'stack', fallback: 'list' },
		list_type: { locked: 'filtered', fallback: 'all' }
	};
	var FILTER_TYPE_NAME_PREFIX = 'elementor-choose-filter_type_';

	function lockSelect( $select ) {
		var config = LOCKED_SELECT_VALUES[ $select.attr( 'data-setting' ) ];
		if ( ! config ) {
			return;
		}

		$select.find( 'option[value="' + config.locked + '"]' ).prop( 'disabled', true );
	}

	function lockFilterTypeChoice( $input ) {
		var name = $input.attr( 'name' ) || '';
		if ( name.indexOf( FILTER_TYPE_NAME_PREFIX ) !== 0 ) {
			return;
		}

		$input.prop( 'disabled', true );

		var $label = $input.next( 'label' );
		if ( $label.length && ! $label.hasClass( 'awsm-pro-locked' ) ) {
			$label.addClass( 'awsm-pro-locked' );
			$label.append( '<span class="awsm-pro-badge">Pro</span>' );
		}
	}

	function scanAndLock( root ) {
		$( root ).find( 'select[data-setting="layout"], select[data-setting="list_type"]' ).each( function() {
			lockSelect( $( this ) );
		} );

		$( root ).find( 'input[type="radio"][value="checkbox"]' ).each( function() {
			lockFilterTypeChoice( $( this ) );
		} );
	}

	function init() {
		scanAndLock( document.body );

		var observer = new MutationObserver( function() {
			scanAndLock( document.body );
		} );
		observer.observe( document.body, { childList: true, subtree: true } );

		// Event delegation on `document`, capture phase: catches the interaction the
		// instant it happens, anywhere in the editor, with no dependency on when or
		// how the control was created.
		document.addEventListener( 'change', function( event ) {
			var target = event.target;

			if ( 'SELECT' === target.tagName && target.dataset ) {
				var config = LOCKED_SELECT_VALUES[ target.dataset.setting ];
				if ( config && target.value === config.locked ) {
					event.preventDefault();
					event.stopImmediatePropagation();
					target.value = config.fallback;
					$( target ).trigger( 'change' );
				}
				return;
			}

			if ( 'INPUT' === target.tagName && 'radio' === target.type && 'checkbox' === target.value ) {
				var name = target.getAttribute( 'name' ) || '';
				if ( name.indexOf( FILTER_TYPE_NAME_PREFIX ) === 0 ) {
					event.preventDefault();
					event.stopImmediatePropagation();
					target.checked = false;
					var dropdownInput = document.querySelector( 'input[type="radio"][name="' + name + '"][value="dropdown"]' );
					if ( dropdownInput ) {
						dropdownInput.checked = true;
						$( dropdownInput ).trigger( 'change' );
					}
				}
			}
		}, true );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )( jQuery );
