/* global niwOnboarding */

/**
 * Registration onboarding popup — vanilla JS.
 *
 * Handles display, form submission (save) and dismiss for the
 * nutritional profile modal shown after new user registration.
 */

( function () {
	'use strict';

	/**
	 * Closes the overlay by removing it from the DOM.
	 *
	 * @param {HTMLElement} overlay
	 */
	function closeModal( overlay ) {
		if ( overlay && overlay.parentNode ) {
			overlay.parentNode.removeChild( overlay );
		}
	}

	const ACTIVITY_LABELS = {
		sedentary: 'Sedentary',
		light:     'Lightly active',
		moderate:  'Moderately active',
		very:      'Very active',
		extra:     'Extremely active',
	};

	const GOAL_LABELS = {
		muscle:      'Build muscle',
		maintenance: 'Maintenance',
		fat_loss:    'Lose fat',
	};

	/**
	 * Updates the nutritional profile card in Mi Perfil (if present on the page).
	 *
	 * @param {FormData} formData
	 */
	function updateNiwView( formData ) {
		var niwView = document.getElementById( 'masai-niw-view' );
		if ( ! niwView ) return;

		var map = {
			age:      { raw: formData.get( 'niw_user_age' ),      format: function ( v ) { return v ? v + ' años' : ''; } },
			height:   { raw: formData.get( 'niw_user_height' ),   format: function ( v ) { return v ? v + ' cm' : ''; } },
			weight:   { raw: formData.get( 'niw_user_weight' ),   format: function ( v ) { return v ? v + ' kg' : ''; } },
			meals:    { raw: formData.get( 'niw_user_meals' ),    format: function ( v ) { return v || ''; } },
			activity: { raw: formData.get( 'niw_user_activity' ), format: function ( v ) { return ACTIVITY_LABELS[ v ] || ''; } },
			goal:     { raw: formData.get( 'niw_user_goal' ),     format: function ( v ) { return GOAL_LABELS[ v ] || ''; } },
		};

		Object.keys( map ).forEach( function ( field ) {
			var container = niwView.querySelector( '[data-niw-field="' + field + '"]' );
			if ( ! container ) return;

			var span    = container.querySelector( '.masai-perfil__field-value' );
			var display = map[ field ].format( map[ field ].raw );

			if ( ! span ) return;

			if ( display ) {
				span.textContent = display;
				span.classList.remove( 'masai-perfil__field-value--empty' );
			} else {
				span.textContent = '';
				span.classList.add( 'masai-perfil__field-value--empty' );
			}
		} );
	}

	/**
	 * Sends a POST request to admin-ajax.php and resolves with the parsed JSON.
	 *
	 * @param {string}     action  WP AJAX action name.
	 * @param {FormData|URLSearchParams} body   Request body.
	 * @returns {Promise<object>}
	 */
	function ajaxPost( action, body ) {
		body.set( 'action', action );
		body.set( 'nonce', niwOnboarding.nonce );

		return fetch( niwOnboarding.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} ).then( function ( response ) {
			return response.json();
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		const overlay = document.getElementById( 'niw-onboarding-overlay' );

		if ( ! overlay ) {
			return;
		}

		// Show the modal (it starts hidden via CSS).
		overlay.classList.add( 'niw-onboarding-visible' );

		const form     = document.getElementById( 'niw-onboarding-form' );
		const skipLink = document.getElementById( 'niw-onboarding-skip' );
		const card     = overlay.querySelector( '.niw-onboarding-card' );

		// ------------------------------------------------------------------
		// Save form submission.
		// ------------------------------------------------------------------
		if ( form ) {
			form.addEventListener( 'submit', function ( event ) {
				event.preventDefault();

				const formData = new FormData( form );

				ajaxPost( 'niw_save_onboarding', formData )
					.then( function ( response ) {
						if ( response.success ) {
							updateNiwView( formData );
							closeModal( overlay );
						}
					} )
					.catch( function () {
						// Silent fail — still close so the user is not stuck.
						closeModal( overlay );
					} );
			} );
		}

		// ------------------------------------------------------------------
		// Dismiss link ("Saltar por ahora").
		// ------------------------------------------------------------------
		if ( skipLink ) {
			skipLink.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				const params = new URLSearchParams();

				ajaxPost( 'niw_dismiss_onboarding', params )
					.then( function () {
						closeModal( overlay );
					} )
					.catch( function () {
						closeModal( overlay );
					} );
			} );
		}

		// Backdrop click intentionally disabled — popup only closes on submit or skip.
	} );
} )();
