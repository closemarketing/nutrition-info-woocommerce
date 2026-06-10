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

		// ------------------------------------------------------------------
		// Close when clicking on the backdrop (outside the card).
		// ------------------------------------------------------------------
		overlay.addEventListener( 'click', function ( event ) {
			if ( card && ! card.contains( event.target ) ) {
				const params = new URLSearchParams();

				ajaxPost( 'niw_dismiss_onboarding', params )
					.then( function () {
						closeModal( overlay );
					} )
					.catch( function () {
						closeModal( overlay );
					} );
			}
		} );
	} );
} )();
