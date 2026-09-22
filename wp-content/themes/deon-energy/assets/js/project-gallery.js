/**
 * Project detail gallery lightbox.
 *
 * Zero-dependency. Turns [data-deon-gallery] .deon-gallery-item buttons into a
 * full-screen viewer with prev/next, keyboard nav, and click-to-close.
 */
( function () {
	'use strict';

	var gallery = document.querySelector( '[data-deon-gallery]' );
	if ( ! gallery ) {
		return;
	}

	var items = Array.prototype.slice.call( gallery.querySelectorAll( '.deon-gallery-item' ) );
	if ( ! items.length ) {
		return;
	}

	var sources = items.map( function ( el ) {
		return { src: el.getAttribute( 'data-full' ), alt: ( el.querySelector( 'img' ) || {} ).alt || '' };
	} );

	var current = 0;
	var overlay, imgEl;

	function build() {
		overlay = document.createElement( 'div' );
		overlay.className = 'deon-lightbox';
		overlay.setAttribute( 'role', 'dialog' );
		overlay.setAttribute( 'aria-modal', 'true' );
		overlay.innerHTML =
			'<button type="button" class="deon-lightbox__close" aria-label="Close">&times;</button>' +
			'<button type="button" class="deon-lightbox__nav deon-lightbox__prev" aria-label="Previous">&#8249;</button>' +
			'<figure class="deon-lightbox__figure"><img class="deon-lightbox__img" src="" alt=""></figure>' +
			'<button type="button" class="deon-lightbox__nav deon-lightbox__next" aria-label="Next">&#8250;</button>' +
			'<span class="deon-lightbox__count"></span>';
		document.body.appendChild( overlay );
		imgEl = overlay.querySelector( '.deon-lightbox__img' );

		overlay.querySelector( '.deon-lightbox__close' ).addEventListener( 'click', close );
		overlay.querySelector( '.deon-lightbox__prev' ).addEventListener( 'click', function ( e ) { e.stopPropagation(); step( -1 ); } );
		overlay.querySelector( '.deon-lightbox__next' ).addEventListener( 'click', function ( e ) { e.stopPropagation(); step( 1 ); } );
		overlay.addEventListener( 'click', function ( e ) {
			if ( e.target === overlay || e.target.classList.contains( 'deon-lightbox__figure' ) ) {
				close();
			}
		} );
	}

	function render() {
		var item = sources[ current ];
		imgEl.setAttribute( 'src', item.src );
		imgEl.setAttribute( 'alt', item.alt );
		overlay.querySelector( '.deon-lightbox__count' ).textContent = ( current + 1 ) + ' / ' + sources.length;
		var single = sources.length < 2;
		overlay.querySelector( '.deon-lightbox__prev' ).hidden = single;
		overlay.querySelector( '.deon-lightbox__next' ).hidden = single;
	}

	function open( index ) {
		current = index;
		if ( ! overlay ) {
			build();
		}
		render();
		overlay.classList.add( 'is-open' );
		document.body.style.overflow = 'hidden';
		document.addEventListener( 'keydown', onKey );
	}

	function close() {
		overlay.classList.remove( 'is-open' );
		document.body.style.overflow = '';
		document.removeEventListener( 'keydown', onKey );
	}

	function step( dir ) {
		current = ( current + dir + sources.length ) % sources.length;
		render();
	}

	function onKey( e ) {
		if ( e.key === 'Escape' ) { close(); }
		else if ( e.key === 'ArrowLeft' ) { step( -1 ); }
		else if ( e.key === 'ArrowRight' ) { step( 1 ); }
	}

	items.forEach( function ( el, i ) {
		el.addEventListener( 'click', function () { open( i ); } );
	} );
}() );
