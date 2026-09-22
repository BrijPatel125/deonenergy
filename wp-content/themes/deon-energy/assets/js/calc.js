/**
 * Solar Savings Calculator — client-side estimation model.
 *
 * Drives the inputs in template-parts/calculator/interface.php. On-site estimate
 * only (see the disclaimer in the results panel). Enqueued as `deon-calc` on the
 * calculator template. Guards on #calc so it no-ops on every other page.
 *
 * Model
 * -----
 *   monthly kWh   = bill / tariff
 *   annual kWh    = monthly kWh × 12
 *   yield/kWp     = specific yield × orientation factor
 *   size (kWp)    = min( annual kWh ÷ yield/kWp,  roof area ÷ sqft-per-kWp )
 *   annual saving = min( size × yield/kWp, annual kWh ) × tariff
 *   payback (yr)  = (size × cost/kWp) ÷ annual saving
 *   10-yr saving  = Σ annual saving × (1 + escalation)^year  for year 0..9
 */
( function () {
	'use strict';

	var root = document.getElementById( 'calc' );
	if ( ! root ) {
		return;
	}

	// --- Config (from data-* on #calc) ---
	var cfg = {
		specificYield: parseFloat( root.dataset.yield ) || 1500,
		sqftPerKwp:    parseFloat( root.dataset.sqftPerKwp ) || 100,
		costResi:      parseFloat( root.dataset.costResi ) || 55000,
		costCommer:    parseFloat( root.dataset.costCommer ) || 45000,
		escalation:    parseFloat( root.dataset.escalation ) || 0.03,
		orient: {
			south: parseFloat( root.dataset.orientSouth ) || 1.0,
			ew:    parseFloat( root.dataset.orientEw ) || 0.85,
			north: parseFloat( root.dataset.orientNorth ) || 0.6
		}
	};

	// --- Elements ---
	var billInput  = document.getElementById( 'calc-bill' );
	var billOut    = document.getElementById( 'calc-bill-out' );
	var roofInput  = document.getElementById( 'calc-roof' );
	var tariffIn   = document.getElementById( 'calc-tariff' );
	var barsWrap   = document.getElementById( 'calc-bars' );
	var outSize    = document.getElementById( 'calc-size' );
	var outSavings = document.getElementById( 'calc-savings' );
	var outPayback = document.getElementById( 'calc-payback' );

	// --- Toggle groups (orientation, installation type) ---
	var state = { orient: 'south', type: 'residential' };

	function initToggle( name ) {
		var group = root.querySelector( '[data-calc-toggle="' + name + '"]' );
		if ( ! group ) {
			return;
		}
		var btns = group.querySelectorAll( '.calc-seg' );
		btns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				state[ name ] = btn.dataset.value;
				btns.forEach( function ( b ) {
					var on = b === btn;
					b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
					b.classList.toggle( 'is-active', on );
					b.classList.toggle( 'border-deon-heading', on );
					b.classList.toggle( 'text-deon-heading', on );
					b.classList.toggle( 'border-deon-border', ! on );
					b.classList.toggle( 'text-deon-body/60', ! on );
				} );
				update();
			} );
		} );
	}

	// --- Formatting helpers (INR, Indian digit grouping) ---
	function money( n ) {
		return '₹' + Math.round( n ).toLocaleString( 'en-IN' );
	}

	// Display face is Plus Jakarta Sans, which carries a proper ₹ glyph — the old
	// "render ₹ in Inter" Playfair workaround is gone.
	function moneyMarkup( n ) {
		return money( n );
	}

	function compact( n ) {
		// Chart total: ₹1.6Cr / ₹16.5L / ₹42.8K by magnitude.
		if ( n >= 1e7 ) {
			return '₹' + ( n / 1e7 ).toFixed( 1 ) + 'Cr';
		}
		if ( n >= 1e5 ) {
			return '₹' + ( n / 1e5 ).toFixed( 1 ) + 'L';
		}
		if ( n >= 1e3 ) {
			return '₹' + ( n / 1e3 ).toFixed( 1 ) + 'K';
		}
		return money( n );
	}

	// --- Chart render: 10 cumulative-savings bars, warm tone ramp ---
	function renderBars( yearly, total ) {
		if ( ! barsWrap ) {
			return;
		}
		var max = yearly[ yearly.length - 1 ] || 1;
		var html = '';
		for ( var i = 0; i < yearly.length; i++ ) {
			var pct = Math.max( 4, Math.round( ( yearly[ i ] / max ) * 100 ) );
			var tone = pct >= 90 ? 'bg-deon-accent' : ( pct >= 50 ? 'bg-deon-accent/50' : 'bg-deon-border' );
			var label = i === yearly.length - 1
				? '<span class="absolute -top-6 left-0 right-0 text-center text-[12px] font-bold tracking-[1.2px] text-deon-accent">' + compact( total ) + '</span>'
				: '';
			html += '<div class="relative flex-1 ' + tone + '" style="height:' + pct + '%">' + label + '</div>';
		}
		barsWrap.innerHTML = html;
	}

	// --- Core model ---
	function update() {
		var bill   = Math.max( 0, parseFloat( billInput && billInput.value ) || 0 );
		var roof   = Math.max( 0, parseFloat( roofInput && roofInput.value ) || 0 );
		var tariff = Math.max( 0, parseFloat( tariffIn && tariffIn.value ) || 0 );
		var orientFactor = cfg.orient[ state.orient ] || 1;
		var costPerKwp = state.type === 'commercial' ? cfg.costCommer : cfg.costResi;

		if ( billOut ) {
			billOut.innerHTML = moneyMarkup( bill );
		}

		var annualKwh   = tariff > 0 ? ( bill / tariff ) * 12 : 0;
		var yieldPerKwp = cfg.specificYield * orientFactor;
		var sizeForLoad = yieldPerKwp > 0 ? annualKwh / yieldPerKwp : 0;
		var sizeForRoof = roof / cfg.sqftPerKwp;
		var size = Math.min( sizeForLoad, sizeForRoof );
		if ( ! isFinite( size ) || size < 0 ) {
			size = 0;
		}

		var annualProduction = size * yieldPerKwp;
		var offsetKwh = Math.min( annualProduction, annualKwh );
		var annualSavings = offsetKwh * tariff;
		var systemCost = size * costPerKwp;
		var payback = annualSavings > 0 ? systemCost / annualSavings : 0;

		// 10-year cumulative savings with annual tariff escalation.
		var cum = 0, yearly = [];
		for ( var y = 0; y < 10; y++ ) {
			cum += annualSavings * Math.pow( 1 + cfg.escalation, y );
			yearly.push( cum );
		}

		if ( outSize ) {
			outSize.textContent = size.toFixed( 1 );
		}
		if ( outSavings ) {
			outSavings.innerHTML = moneyMarkup( annualSavings );
		}
		if ( outPayback ) {
			outPayback.textContent = payback > 0 ? payback.toFixed( 1 ) : '—';
		}
		renderBars( yearly, cum );
	}

	// --- Wire up ---
	[ billInput, roofInput, tariffIn ].forEach( function ( el ) {
		if ( el ) {
			el.addEventListener( 'input', update );
		}
	} );
	initToggle( 'orient' );
	initToggle( 'type' );

	update();
}() );
