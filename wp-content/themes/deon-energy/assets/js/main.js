// Deon Energy — theme JS.

/* ---------------------------------------------------------------------------
 * Shared pagination builder — the client-side twin of deon_pagination() in
 * inc/pagination.php. Both emit the same `.deon-pager` markup (styled once in
 * main.css) so the server-rendered pagers (Knowledge Hub, Press Coverage) and
 * the JS pagers (Project Gallery, Leadership) are one control.
 *
 * deonBuildPager(nav, current, total, onGo) — 1-based pages; renders nothing
 * and hides `nav` when there is a single page.
 * ------------------------------------------------------------------------- */
(function (w) {
	'use strict';

	var ARROW = {
		prev: 'M3.825 9L9.425 14.6L8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825Z',
		next: 'M12.175 9H0V7H12.175L6.575 1.4L8 0L16 8L8 16L6.575 14.6L12.175 9Z'
	};

	// Mirrors deon_pagination_page_list(): first, last, and a window around the
	// current page, with '…' marking each gap.
	function pageList(current, total) {
		if (total <= 7) {
			var all = [];
			for (var i = 1; i <= total; i++) { all.push(i); }
			return all;
		}

		var keep = [1, total, current - 1, current, current + 1];
		if (current <= 3) { keep = keep.concat([2, 3, 4]); }
		if (current >= total - 2) { keep = keep.concat([total - 3, total - 2, total - 1]); }

		keep = keep
			.filter(function (p) { return p >= 1 && p <= total; })
			.filter(function (p, i, a) { return a.indexOf(p) === i; })
			.sort(function (a, b) { return a - b; });

		var out = [], prev = 0;
		keep.forEach(function (p) {
			if (prev && p - prev > 1) { out.push('…'); }
			out.push(p);
			prev = p;
		});
		return out;
	}

	function arrow(dir) {
		return '<svg class="deon-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">' +
			'<path d="' + ARROW[dir] + '" fill="currentColor"/></svg>';
	}

	w.deonBuildPager = function (nav, current, total, onGo) {
		if (!nav) { return; }
		nav.innerHTML = '';
		nav.classList.add('deon-pager');

		if (total <= 1) {
			nav.setAttribute('hidden', '');
			nav.style.display = 'none';
			return;
		}
		nav.removeAttribute('hidden');
		nav.style.display = '';

		function btn(html, page, mods) {
			mods = mods || {};
			var el = document.createElement('button');
			el.type = 'button';
			el.innerHTML = html;
			el.className = 'deon-pager__btn' +
				(mods.nav ? ' deon-pager__btn--nav' : '') +
				(mods.current ? ' is-current' : '');
			if (mods.disabled) {
				el.disabled = true;
				el.className += ' is-disabled';
			}
			if (mods.current) { el.setAttribute('aria-current', 'page'); }
			if (mods.label) { el.setAttribute('aria-label', mods.label); }
			if (!mods.disabled && !mods.current) {
				el.addEventListener('click', function () { onGo(page); });
			}
			return el;
		}

		nav.appendChild(btn(arrow('prev'), current - 1, {
			nav: true, disabled: current <= 1, label: 'Previous page'
		}));

		pageList(current, total).forEach(function (p) {
			if (p === '…') {
				var gap = document.createElement('span');
				gap.className = 'deon-pager__gap';
				gap.setAttribute('aria-hidden', 'true');
				gap.textContent = '…';
				nav.appendChild(gap);
				return;
			}
			nav.appendChild(btn(String(p), p, { current: p === current }));
		});

		nav.appendChild(btn(arrow('next'), current + 1, {
			nav: true, disabled: current >= total, label: 'Next page'
		}));
	};
})(window);

(function () {
	'use strict';

	// ------------------------------------------------------------------
	// Primary navigation
	// ------------------------------------------------------------------
	var header = document.querySelector('[data-site-header]');
	var toggle = document.querySelector('[data-nav-toggle]');
	var MOBILE_MAX = 1150; // must match the hamburger breakpoint in main.css

	function isMobileNav() {
		return window.innerWidth <= MOBILE_MAX;
	}

	function closeSubmenu(li) {
		li.classList.remove('is-open');
		var btn = li.querySelector(':scope > .sub-menu-toggle');
		if (btn) btn.setAttribute('aria-expanded', 'false');
	}

	function closeAllSubmenus(except) {
		if (!header) return;
		header.querySelectorAll('.menu-item-has-children.is-open').forEach(function (li) {
			if (li !== except) closeSubmenu(li);
		});
	}

	function openSubmenu(li) {
		// Desktop: only one panel at a time. Mobile accordion allows several.
		if (!isMobileNav()) closeAllSubmenus(li);
		li.classList.add('is-open');
		var btn = li.querySelector(':scope > .sub-menu-toggle');
		if (btn) btn.setAttribute('aria-expanded', 'true');
	}

	var navClose = document.querySelector('[data-nav-close]');
	var navScrim = document.querySelector('[data-nav-scrim]');

	function closeMobileNav() {
		if (!header || !toggle) return;
		header.classList.remove('is-open');
		document.body.classList.remove('nav-drawer-open');
		toggle.setAttribute('aria-expanded', 'false');
		closeAllSubmenus();
	}

	function openMobileNav() {
		if (!header || !toggle) return;
		header.classList.add('is-open');
		document.body.classList.add('nav-drawer-open');
		toggle.setAttribute('aria-expanded', 'true');
		if (navClose) navClose.focus();
	}

	if (header && toggle) {
		toggle.addEventListener('click', function () {
			if (header.classList.contains('is-open')) {
				closeMobileNav();
			} else {
				openMobileNav();
			}
		});

		// The drawer closes from its own ✕ and from the scrim behind it.
		if (navClose) {
			navClose.addEventListener('click', function () {
				closeMobileNav();
				toggle.focus();
			});
		}
		if (navScrim) {
			navScrim.addEventListener('click', closeMobileNav);
		}

		// In the drawer, a parent row is a disclosure, not a link: tapping the
		// label opens its children instead of navigating away (solex.in
		// behaviour). Desktop keeps the link — the section page stays reachable.
		header.querySelectorAll('.site-nav .menu-item-has-children > a').forEach(function (link) {
			link.addEventListener('click', function (e) {
				if (!isMobileNav()) return;
				var li = link.closest('.menu-item-has-children');
				if (!li) return;
				e.preventDefault();
				if (li.classList.contains('is-open')) {
					closeSubmenu(li);
				} else {
					openSubmenu(li);
				}
			});
		});

		// Tapping a real destination inside the drawer closes it — otherwise the
		// panel stays over the page the link just navigated to (same-page anchors).
		header.querySelectorAll('.site-nav a, .site-header__actions a').forEach(function (link) {
			link.addEventListener('click', function () {
				if (!isMobileNav()) return;
				if (link.getAttribute('href') === '#') return;
				// Parent rows only expand — they must not close the drawer.
				if (link.parentElement && link.parentElement.classList.contains('menu-item-has-children')) return;
				closeMobileNav();
			});
		});
	}

	if (header) {
		// Caret buttons: click / Enter / Space toggle the panel and keep
		// aria-expanded in sync. Works identically on desktop and mobile.
		header.querySelectorAll('[data-submenu-toggle]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var li = btn.closest('.menu-item-has-children');
				if (!li) return;
				if (li.classList.contains('is-open')) {
					closeSubmenu(li);
				} else {
					openSubmenu(li);
				}
			});
		});

		// Keyboard focus anywhere inside a parent item opens its panel (desktop);
		// CSS :focus-within mirrors this visually, JS keeps aria-expanded honest.
		header.querySelectorAll('.menu-item-has-children').forEach(function (li) {
			li.addEventListener('focusin', function () {
				if (!isMobileNav()) openSubmenu(li);
			});
			li.addEventListener('focusout', function (e) {
				if (isMobileNav()) return;
				if (!li.contains(e.relatedTarget)) closeSubmenu(li);
			});
			li.addEventListener('mouseenter', function () {
				if (!isMobileNav()) openSubmenu(li);
			});
			li.addEventListener('mouseleave', function () {
				if (!isMobileNav()) closeSubmenu(li);
			});
		});

		// Escape closes the open panel, then the mobile drawer.
		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape' && e.key !== 'Esc') return;
			var openItem = header.querySelector('.menu-item-has-children.is-open');
			if (openItem) {
				closeSubmenu(openItem);
				var btn = openItem.querySelector(':scope > .sub-menu-toggle');
				if (btn) btn.focus();
				return;
			}
			if (header.classList.contains('is-open')) {
				closeMobileNav();
				if (toggle) toggle.focus();
			}
		});

		// Click outside closes desktop panels.
		document.addEventListener('click', function (e) {
			if (!header.contains(e.target)) closeAllSubmenus();
		});

		// Crossing the breakpoint resets state so neither mode inherits the other's.
		var wasMobile = isMobileNav();
		window.addEventListener('resize', function () {
			var nowMobile = isMobileNav();
			if (nowMobile === wasMobile) return;
			wasMobile = nowMobile;
			closeAllSubmenus();
			if (!nowMobile) closeMobileNav();
		});
	}

	// Scroll animations via IntersectionObserver.
	if (!('IntersectionObserver' in window)) return;

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (e) {
			if (e.isIntersecting) {
				e.target.classList.add('anim--visible');
				observer.unobserve(e.target);
			}
		});
	}, { threshold: 0.12 });

	// Staggered groups — delay resets per parent container.
	var staggerSel = [
		// Homepage
		'.stats__item, .why__item, .service-card, .project-tile, .solar-card, .post-card',
		// About page
		'.about-vm__card, .about-work__card, .about-values__card, .about-certs__badge',
	].join(', ');
	var parents = new Set();
	document.querySelectorAll(staggerSel).forEach(function (el) {
		parents.add(el.parentElement);
	});
	parents.forEach(function (parent) {
		Array.from(parent.children).forEach(function (el, i) {
			el.classList.add('anim');
			el.style.setProperty('--anim-delay', (i * 0.1) + 's');
			observer.observe(el);
		});
	});

	// Growth Timeline — serpentine journey.
	//
	// On desktop (>=961px) the milestones are laid out as a snake: each visible
	// node sits alternately left/right of centre and a thick weaving SVG path is
	// drawn through them (matching the client reference). The path + node
	// coordinates are computed here so the curve always threads the *visible*
	// nodes and re-flows on resize / expand. Narrow screens (or no JS) keep the
	// CSS left-spine list. The "Show all / show fewer" toggle flips the overflow
	// nodes' hidden attribute, then relays out.
	(function () {
		var timelines = document.querySelectorAll('[data-journey]');
		if (!timelines.length) return;

		// The section used to arrive fully drawn (client note 2026-08). Now the
		// ribbon draws itself with scroll progress and each milestone fades up
		// as the band reaches it. `is-animated` is what arms the hidden initial
		// state in CSS, so a no-JS / reduced-motion visitor still sees
		// everything immediately.
		var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (!REDUCED) {
			Array.prototype.forEach.call(timelines, function (tl) {
				tl.classList.add('is-animated');
			});
		}

		var mq        = window.matchMedia('(min-width: 961px)');
		var SEG       = 170;  // Vertical spacing per milestone (px) — tight, so
		                      // consecutive loops sit close (per reference).
		var MAX_AMP   = 80;   // Max horizontal bulge from centre (keeps the two
		                      // year columns close, per reference).
		var LOOP_R    = 76;   // Radius of the U-turn loop the band makes around
		                      // each icon — leaves a clear gap between the icon
		                      // circle and the band's inner edge.
		var TEXT_ROOM = 380;  // Icon radius + offset + photo + text width — kept on-screen.

		function layoutOne(tl) {
			var svg   = tl.querySelector('.about-journey__svg');
			var paths = tl.querySelectorAll('.about-journey__path');
			var items = Array.prototype.slice.call(tl.querySelectorAll('.about-journey__item'));
			var visible = items.filter(function (it) { return !it.hidden; });

			// Mobile / narrow: drop absolute layout, let the CSS list take over.
			if (!mq.matches) {
				tl.classList.remove('is-ready');
				tl.style.height = '';
				tl.__journeyLen = 0; // No ribbon to draw — nodes still reveal on scroll.
				items.forEach(function (it) { it.style.left = ''; it.style.top = ''; });
				return;
			}

			var w  = tl.clientWidth;
			var cx = w / 2;
			var amp = Math.min(MAX_AMP, cx - TEXT_ROOM);
			if (amp < 40) amp = 40;
			var h  = SEG * visible.length;

			// Position every node. The band is a SLALOM (per client reference):
			// around each icon it makes a tangent-continuous arc, and between
			// icons it runs along the straight internal tangent of the two
			// loops — so every joint is perfectly smooth, no kinks.
			var R   = LOOP_R;
			var pts = visible.map(function (it, i) {
				var onLeft = (i % 2 === 0); // Alternate; index 0 sits on the right (per reference).
				var x = cx + (onLeft ? amp : -amp);
				var y = SEG / 2 + i * SEG;
				it.style.left = x + 'px';
				it.style.top  = y + 'px';
				// Content (connector + photo + text) extends on the side OPPOSITE
				// the band's wrap (per reference) — the band wraps the icon's
				// outward side, content reaches inward across the centre.
				it.classList.toggle('about-journey__item--right', !onLeft);
				it.classList.toggle('about-journey__item--left', onLeft);
				return { x: x, y: y, dir: onLeft ? 1 : -1 }; // dir = outward side (+1 wraps clockwise).
			});

			// Arc around loop p from entry-normal nIn to exit-normal nOut
			// (normals = unit vectors from loop centre to the point where the
			// band touches it). Sweep + large-arc derived from the wrap angle.
			function arcSeg(p, nIn, nOut) {
				var TAU   = Math.PI * 2;
				var aIn   = Math.atan2(nIn.y, nIn.x);
				var aOut  = Math.atan2(nOut.y, nOut.x);
				var swept = p.dir === 1 ? aOut - aIn : aIn - aOut;
				swept = ((swept % TAU) + TAU) % TAU;
				return ' A ' + R + ' ' + R + ' 0 ' + (swept > Math.PI ? 1 : 0) + ' ' + (p.dir === 1 ? 1 : 0) +
				       ' ' + (p.x + R * nOut.x) + ' ' + (p.y + R * nOut.y);
			}

			// Band starts at the top of the first loop (rounded cap curls it).
			var first = pts[0];
			var entry = { x: 0, y: -1 };
			var d = 'M ' + first.x + ' ' + (first.y - R);

			for (var i = 0; i < pts.length; i++) {
				var p = pts[i];

				if (i === pts.length - 1) {
					d += arcSeg(p, entry, { x: 0, y: 1 }); // Finish at the loop's bottom.
					break;
				}

				// Internal tangent between loop i and loop i+1 (equal radii,
				// opposite winding): touch-point normal n satisfies n·v = 2R.
				var q  = pts[i + 1];
				var vx = q.x - p.x, vy = q.y - p.y;
				var D  = Math.sqrt(vx * vx + vy * vy);
				var L  = Math.sqrt(Math.max(D * D - 4 * R * R, 1));
				var ux = vx / D, uy = vy / D;
				var cosf = 2 * R / D;
				var sinf = -p.dir * L / D;
				var n = { x: cosf * ux - sinf * uy, y: cosf * uy + sinf * ux };

				d += arcSeg(p, entry, n);                             // Wrap this icon…
				d += ' L ' + (q.x - R * n.x) + ' ' + (q.y - R * n.y); // …straight tangent run…
				entry = { x: -n.x, y: -n.y };                         // …into the next loop.
			}

			tl.style.height = h + 'px';
			svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
			Array.prototype.forEach.call(paths, function (p) { p.setAttribute('d', d); });
			tl.classList.add('is-ready');
			primeDraw(tl);
		}

		// Dash the ribbon by its own length so stroke-dashoffset can wipe it in.
		// Re-run after every layout — the path length changes with width and
		// with the "Show all" toggle.
		function primeDraw(tl) {
			var edge = tl.querySelector('.about-journey__path--edge');
			var len  = ( edge && edge.getTotalLength ) ? edge.getTotalLength() : 0;
			tl.__journeyLen = len;
			tl.querySelectorAll('.about-journey__path').forEach(function (p) {
				if (!len || REDUCED) {
					p.style.strokeDasharray  = '';
					p.style.strokeDashoffset = '';
					return;
				}
				p.style.strokeDasharray = len;
			});
		}

		// Scroll progress → how much of the band is drawn, and which nodes have
		// been reached. Cheap enough to run on every rAF-throttled scroll tick.
		function updateOne(tl) {
			if (REDUCED) return;

			var rect = tl.getBoundingClientRect();
			var vh   = window.innerHeight || document.documentElement.clientHeight;

			// 0 when the timeline top reaches 82% down the viewport; 1 once
			// ~80% of its height has passed that mark. Tuned so the band always
			// runs slightly AHEAD of the node reveals below — milestones land on
			// a ribbon that is already there, never on empty space.
			var p = (vh * 0.82 - rect.top) / Math.max(rect.height * 0.8, 1);
			if (p < 0) p = 0;
			if (p > 1) p = 1;

			var len = tl.__journeyLen || 0;
			if (len) {
				var off = len * (1 - p);
				tl.querySelectorAll('.about-journey__path').forEach(function (el) {
					el.style.strokeDashoffset = off;
				});
			}

			// One-way reveal — a node never fades back out on scroll-up.
			tl.querySelectorAll('.about-journey__item').forEach(function (it) {
				if (it.hidden || it.classList.contains('is-revealed')) return;
				if (it.getBoundingClientRect().top < vh * 0.88) {
					it.classList.add('is-revealed');
				}
			});
		}

		function updateAll() {
			Array.prototype.forEach.call(timelines, updateOne);
		}

		var scrollTick = false;
		function onScroll() {
			if (scrollTick) return;
			scrollTick = true;
			requestAnimationFrame(function () {
				scrollTick = false;
				updateAll();
			});
		}

		function layoutAll() {
			Array.prototype.forEach.call(timelines, layoutOne);
			updateAll();
		}

		// Toggle: reveal / hide the overflow nodes, then relayout.
		document.querySelectorAll('[data-journey-toggle]').forEach(function (btn) {
			var section  = btn.closest('.about-journey');
			var timeline = section ? section.querySelector('[data-journey]') : timelines[0];
			if (!timeline) return;

			var labelMore = btn.getAttribute('data-label-more') || btn.textContent;
			var labelLess = btn.getAttribute('data-label-less') || 'Show fewer';

			btn.addEventListener('click', function () {
				var expanded = timeline.classList.toggle('is-expanded');
				timeline.querySelectorAll('.about-journey__item[data-overflow]').forEach(function (item) {
					item.hidden = !expanded;
				});
				btn.textContent = expanded ? labelLess : labelMore;
				btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
				layoutOne(timeline);
				updateOne(timeline);
			});
		});

		var raf;
		window.addEventListener('resize', function () {
			if (raf) cancelAnimationFrame(raf);
			raf = requestAnimationFrame(layoutAll);
		});
		if (mq.addEventListener) mq.addEventListener('change', layoutAll);
		else if (mq.addListener) mq.addListener(layoutAll);

		if (!REDUCED) {
			window.addEventListener('scroll', onScroll, { passive: true });
		}

		layoutAll();
	})();

	// Project filter chips.
	//
	// The chips are real links to ?type=<term-slug> and the grid already ships
	// filtered from PHP (see inc/project-filters.php + template-parts/projects/).
	// Here we take over: filter in place, push the same URL into history so the
	// view stays linkable and the back button works, no reload.
	var filterBar    = document.querySelector('[data-project-filters]');
	var projectCards = document.querySelectorAll('[data-category]');

	if (filterBar && projectCards.length) {
		var chips       = Array.prototype.slice.call(filterBar.querySelectorAll('[data-filter]'));
		var emptyMsg    = document.querySelector('[data-filter-empty]');
		var gridEl      = document.querySelector('[data-project-grid]');
		// Tokens MUST match filters.php exactly, including the `!` (important):
		// the unlayered `a { color: inherit }` reset beats plain text-* utilities
		// on these <a> chips, so the important variants are required and the JS
		// must toggle the same class strings PHP rendered.
		var onClasses   = ['bg-deon-dark', 'border-deon-dark', 'text-white!'];
		var offClasses  = ['border-deon-border', 'text-deon-heading!'];

		// Slugs the chips actually offer — anything else falls back to "all".
		var known = chips.map(function (c) { return c.getAttribute('data-filter'); });

		function normalise(slug) {
			return (slug && slug !== 'all' && known.indexOf(slug) !== -1) ? slug : 'all';
		}

		function show(el, visible) {
			if (visible) {
				el.removeAttribute('hidden');
				el.style.display = '';
			} else {
				el.setAttribute('hidden', '');
				el.style.display = 'none';
			}
		}

		// Client-side pagination sits on top of the filter: every card is in the
		// DOM (needed for instant filtering), but only PAGE_SIZE of the currently
		// matching cards are shown at a time. Changing the filter resets to page 1.
		var pager       = document.querySelector('[data-project-pagination]');
		var searchInput = document.querySelector('[data-project-search]');
		var PAGE_SIZE   = 12; // 3 per row × 4 rows (client request 2026-08).
		var curFilter   = 'all';
		var curSearch   = '';
		var curPage     = 1;

		function matches(card, filter) {
			var catOk = filter === 'all' || card.getAttribute('data-category') === filter;
			if (!catOk) { return false; }
			if (curSearch === '') { return true; }
			return (card.getAttribute('data-search') || '').indexOf(curSearch) !== -1;
		}

		function renderPager(pageCount) {
			// Shared `.deon-pager` markup — same classes the PHP pager renders
			// (inc/pagination.php), styled once in main.css. It hides itself
			// when there is only one page.
			if (pager) { deonBuildPager(pager, curPage, pageCount, goToPage); }
		}

		function render() {
			chips.forEach(function (chip) {
				var on = chip.getAttribute('data-filter') === curFilter;
				onClasses.forEach(function (c) { chip.classList.toggle(c, on); });
				offClasses.forEach(function (c) { chip.classList.toggle(c, !on); });
				if (on) {
					chip.setAttribute('aria-current', 'true');
				} else {
					chip.removeAttribute('aria-current');
				}
			});

			var visible   = [];
			projectCards.forEach(function (card) {
				if (matches(card, curFilter)) { visible.push(card); }
				else { show(card, false); }
			});

			var pageCount = Math.max(1, Math.ceil(visible.length / PAGE_SIZE));
			if (curPage > pageCount) { curPage = pageCount; }
			if (curPage < 1) { curPage = 1; }
			var start = (curPage - 1) * PAGE_SIZE;

			visible.forEach(function (card, i) {
				var onPage = i >= start && i < start + PAGE_SIZE;
				show(card, onPage);
				// Cards paged in were display:none, so the reveal observer never
				// fired on them — force the visible state or they stay at opacity 0.
				if (onPage) { card.classList.add('anim--visible'); }
			});

			if (emptyMsg) { show(emptyMsg, visible.length === 0); }
			if (gridEl) { gridEl.setAttribute('data-active-filter', curFilter); }
			renderPager(pageCount);
		}

		function goToPage(page) {
			curPage = page;
			render();
			// Scroll the grid top just below the sticky header — scrollIntoView
			// alone aligns it to y=0, hidden behind the header.
			if (gridEl) {
				var header = document.querySelector('[data-site-header]');
				var offset = (header ? header.offsetHeight : 0) + 16;
				var top    = gridEl.getBoundingClientRect().top + window.pageYOffset - offset;
				window.scrollTo({ top: top, behavior: 'smooth' });
			}
		}

		function apply(filter) {
			curFilter = normalise(filter);
			curPage   = 1;
			render();
		}

		function urlFor(filter) {
			var url = new URL(window.location.href);
			url.hash = 'project-filters';
			if (filter === 'all') {
				url.searchParams.delete('type');
			} else {
				url.searchParams.set('type', filter);
			}
			return url.toString();
		}

		function onChipClick(e) {
			// Let modified clicks (new tab/window) behave natively.
			if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button > 0) { return; }
			e.preventDefault();

			var filter = normalise(this.getAttribute('data-filter'));
			apply(filter);
			if (window.history && window.history.pushState) {
				window.history.pushState({ deonFilter: filter }, '', urlFor(filter));
			}
		}

		document.querySelectorAll('[data-filter]').forEach(function (el) {
			el.addEventListener('click', onChipClick);
		});

		// Free-text search — combines with the active filter, resets to page 1.
		if (searchInput) {
			searchInput.addEventListener('input', function () {
				curSearch = this.value.trim().toLowerCase();
				curPage   = 1;
				render();
			});
		}

		// Back / forward through filter states.
		window.addEventListener('popstate', function () {
			var params = new URLSearchParams(window.location.search);
			apply(params.get('type') || 'all');
		});

		// Sync with whatever PHP rendered (and repair a stale/unknown ?type=).
		var initial = normalise(new URLSearchParams(window.location.search).get('type'));
		apply(initial);
		if (initial === 'all' && window.location.search.indexOf('type=') !== -1 &&
			window.history && window.history.replaceState) {
			window.history.replaceState({ deonFilter: 'all' }, '', urlFor('all'));
		}
	}

	// Investor Relations — Investor Documents.
	// One set of markup, two shapes (see template-parts/investors/resources.php):
	//  - lg and up: the CATEGORIES sidebar is a filter. The stage on the right
	//    shows ONLY the active category's documents, so the list stays short no
	//    matter how many files the client uploads.
	//  - below lg: the same panels move into the category rows and behave as an
	//    accordion — tapping a category expands its documents right underneath,
	//    which is what the client asked for on mobile (solex.in behaviour).
	// Panels are rendered once inside [data-doc-stage]; this relocates them.
	var docApp = document.querySelector('[data-doc-app]');

	if (docApp) {
		var docBtns   = Array.prototype.slice.call(docApp.querySelectorAll('[data-doc-btn]'));
		var docPanels = Array.prototype.slice.call(docApp.querySelectorAll('[data-doc-panel]'));
		var docStage  = docApp.querySelector('[data-doc-stage]');
		var docHeading = docApp.querySelector('[data-doc-heading]');
		var docLists  = Array.prototype.slice.call(docApp.querySelectorAll('[data-doc-list]'));
		var docItems  = Array.prototype.slice.call(docApp.querySelectorAll('[data-doc-item]'));
		var docWide   = window.matchMedia('(min-width: 1024px)');
		// Desktop always has one category open; mobile may have none (all collapsed).
		var docActive = docBtns.length ? docBtns[0].getAttribute('data-doc-btn') : '';

		function docLabel(id) {
			var btn = docApp.querySelector('[data-doc-btn="' + id + '"]');
			var span = btn && btn.querySelector('span');
			return span ? span.textContent.trim() : '';
		}

		function docRender() {
			var wide = docWide.matches;

			docPanels.forEach(function (panel) {
				var home = docApp.querySelector('[data-doc-home="' + panel.id + '"]');
				var dest = wide ? docStage : home;
				if (dest && panel.parentNode !== dest) { dest.appendChild(panel); }
				if (wide) { panel.removeAttribute('data-inline'); }
				else { panel.setAttribute('data-inline', ''); }
				// Inline style, not a class: the panel's own `flex` would out-order
				// a toggled `hidden` utility depending on stylesheet order.
				panel.style.display = panel.id === docActive ? '' : 'none';
			});

			docBtns.forEach(function (btn) {
				var on = btn.getAttribute('data-doc-btn') === docActive;
				btn.classList.toggle('font-bold', on);
				btn.classList.toggle('text-deon-heading', on);
				btn.classList.toggle('bg-deon-bg', on);
				btn.classList.toggle('border-deon-accent', on);
				btn.classList.toggle('font-normal', !on);
				btn.classList.toggle('text-deon-accent', !on);
				btn.classList.toggle('bg-transparent', !on);
				btn.classList.toggle('border-transparent', !on);
				btn.setAttribute('aria-expanded', on ? 'true' : 'false');
			});

			if (docHeading && docActive) { docHeading.textContent = docLabel(docActive); }
		}

		docBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var id = this.getAttribute('data-doc-btn');
				// Accordion: a second tap collapses. Filter: the stage is never blank.
				docActive = (!docWide.matches && docActive === id) ? '' : id;
				docRender();
			});
		});

		// Crossing the breakpoint re-homes the panels; re-open one if mobile had
		// everything collapsed, otherwise the desktop stage would render empty.
		function docBreakpoint() {
			if (docWide.matches && !docActive && docBtns.length) {
				docActive = docBtns[0].getAttribute('data-doc-btn');
			}
			docRender();
		}
		if (docWide.addEventListener) { docWide.addEventListener('change', docBreakpoint); }
		else if (docWide.addListener) { docWide.addListener(docBreakpoint); }

		// Deep link (…/investor-relations/#doc-annual-reports) opens that category.
		var docHash = window.location.hash.slice(1);
		if (docHash && docApp.querySelector('[data-doc-btn="' + docHash + '"]')) { docActive = docHash; }

		docRender();

		// Grid / list view toggle — applied to every category list.
		var viewBtns = Array.prototype.slice.call(docApp.querySelectorAll('[data-doc-view]'));
		var listClasses = ['flex', 'flex-col', 'border-t', 'border-deon-border'];
		var gridClasses = ['grid', 'grid-cols-1', 'sm:grid-cols-2', 'gap-4'];

		function applyView(view) {
			viewBtns.forEach(function (btn) {
				var on = btn.getAttribute('data-doc-view') === view;
				btn.classList.toggle('bg-deon-dark', on);
				btn.classList.toggle('text-white', on);
				btn.classList.toggle('bg-white', !on);
				btn.classList.toggle('text-deon-heading', !on);
				if (on) { btn.setAttribute('aria-current', 'true'); }
				else { btn.removeAttribute('aria-current'); }
			});

			docLists.forEach(function (list) {
				listClasses.forEach(function (c) { list.classList.toggle(c, view === 'list'); });
				gridClasses.forEach(function (c) { list.classList.toggle(c, view === 'grid'); });
			});
			docItems.forEach(function (item) {
				if (view === 'grid') { item.setAttribute('data-view', 'grid'); }
				else { item.removeAttribute('data-view'); }
			});
		}

		viewBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				applyView(this.getAttribute('data-doc-view'));
			});
		});
	}

	// Technical Papers — category sidebar filter. Buttons filter the paper rows
	// in place by their [data-tp-cat] slug; no reload (the page lists every
	// paper). "All Papers" resets. Mirrors the Investor doc filter pattern.
	var tpFilters = document.querySelector('[data-tp-filters]');
	var tpList    = document.querySelector('[data-tp-list]');

	if (tpFilters && tpList) {
		var tpBtns  = Array.prototype.slice.call(tpFilters.querySelectorAll('[data-tp-filter]'));
		var tpRows  = Array.prototype.slice.call(tpList.querySelectorAll('[data-tp-cat]'));
		var tpEmpty = tpList.querySelector('[data-tp-empty]');

		function applyTp(filter) {
			tpBtns.forEach(function (btn) {
				var on = btn.getAttribute('data-tp-filter') === filter;
				btn.classList.toggle('font-semibold', on);
				btn.classList.toggle('text-deon-heading', on);
				btn.classList.toggle('bg-deon-bg', on);
				btn.classList.toggle('border-deon-accent', on);
				btn.classList.toggle('font-normal', !on);
				btn.classList.toggle('text-deon-body', !on);
				btn.classList.toggle('bg-transparent', !on);
				btn.classList.toggle('border-transparent', !on);
				if (on) { btn.setAttribute('aria-current', 'true'); }
				else { btn.removeAttribute('aria-current'); }
			});

			var matches = 0;
			tpRows.forEach(function (row) {
				var visible = filter === 'all' || row.getAttribute('data-tp-cat') === filter;
				if (visible) { matches++; }
				row.hidden = !visible;
				row.style.display = visible ? '' : 'none';
			});

			if (tpEmpty) {
				tpEmpty.hidden = matches !== 0;
				tpEmpty.classList.toggle('hidden', matches !== 0);
			}
		}

		tpBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				applyTp(this.getAttribute('data-tp-filter'));
			});
		});
	}

	// Single-block reveals — section leads and full-width sections.
	var singleSel = [
		// Homepage
		'.services__head, .projects__lead, .why__lead, .articles__head, .about-strip__lead, .about-strip__stats, .why-solar__head, .cta__inner, .logos',
		// About page
		'.about-hero__heading, .about-hero__eyebrow, .about-overview__text, .about-overview__media, .about-work__lead, .about-values__head, .about-journey__h2',
	].join(', ');
	document.querySelectorAll(singleSel).forEach(function (el) {
		el.classList.add('anim');
		observer.observe(el);
	});

	// Generic hooks — any page. Add [data-anim] to a block for a single reveal,
	// or [data-anim-stagger] to a container to reveal its children in sequence.
	document.querySelectorAll('[data-anim-stagger]').forEach(function (parent) {
		Array.from(parent.children).forEach(function (el, i) {
			el.classList.add('anim');
			el.style.setProperty('--anim-delay', (i * 0.1) + 's');
			observer.observe(el);
		});
	});
	document.querySelectorAll('[data-anim]').forEach(function (el) {
		el.classList.add('anim');
		observer.observe(el);
	});

	// FAQ page — accordion toggles.
	document.querySelectorAll('[data-faq-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var item = btn.closest('[data-faq-item]');
			if (!item) return;
			var panel = item.querySelector('[data-faq-panel]');
			var chevron = item.querySelector('[data-faq-chevron]');
			var open = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			if (panel) panel.hidden = open;
			if (chevron) chevron.classList.toggle('rotate-180', !open);
		});
	});

	// FAQ page — live search filter.
	var faqSearch = document.querySelector('[data-faq-search]');
	if (faqSearch) {
		var faqItems = document.querySelectorAll('[data-faq-item]');
		var faqGroups = document.querySelectorAll('[data-faq-group]');
		var faqEmpty = document.querySelector('[data-faq-empty]');
		faqSearch.addEventListener('input', function () {
			var q = faqSearch.value.trim().toLowerCase();
			var anyVisible = false;
			faqItems.forEach(function (item) {
				var text = (item.getAttribute('data-faq-text') || '').toLowerCase();
				var show = !q || text.indexOf(q) !== -1;
				item.hidden = !show;
				if (show) anyVisible = true;
			});
			// Hide a group whose every item is filtered out.
			faqGroups.forEach(function (group) {
				var visible = group.querySelectorAll('[data-faq-item]:not([hidden])').length;
				group.hidden = q && visible === 0;
			});
			if (faqEmpty) faqEmpty.hidden = anyVisible;
		});
	}

	// FAQ page — sidebar active state on scroll to section.
	var faqCatLinks = document.querySelectorAll('[data-faq-cat-link]');
	if (faqCatLinks.length) {
		faqCatLinks.forEach(function (link) {
			link.addEventListener('click', function () {
				faqCatLinks.forEach(function (l) {
					l.classList.remove('bg-white', 'text-deon-heading', 'font-semibold');
				});
				link.classList.add('bg-white', 'text-deon-heading', 'font-semibold');
			});
		});
	}
})();

/* --------------------------------------------------------------------------
 * Leadership — 3D flip profile cards: tap-to-flip on coarse pointers.
 * Appended block. Owned by the Leadership section; nothing above is modified.
 * Desktop hover + keyboard focus are handled purely in CSS
 * (assets/css/main.css, "Leadership — 3D flip profile cards").
 * -------------------------------------------------------------------------- */
(function () {
	'use strict';

	var cards = document.querySelectorAll('[data-leader-flip]');
	if (!cards.length) return;

	var coarse = window.matchMedia('(hover: none), (pointer: coarse)');

	function setFlipped(card, on) {
		card.classList.toggle('is-flipped', on);
		card.setAttribute('aria-expanded', on ? 'true' : 'false');
		// Focus alone flips the card via :focus-within, so drop focus when
		// closing after a tap — otherwise the card would never turn back.
		if (!on && document.activeElement === card) card.blur();
	}

	function closeOthers(current) {
		Array.prototype.forEach.call(cards, function (c) {
			if (c !== current) setFlipped(c, false);
		});
	}

	function toggle(card) {
		var on = !card.classList.contains('is-flipped');
		closeOthers(card);
		setFlipped(card, on);
	}

	Array.prototype.forEach.call(cards, function (card) {
		card.addEventListener('click', function (e) {
			// Let the LinkedIn link on the back face behave like a link.
			if (e.target.closest && e.target.closest('a')) return;
			if (!coarse.matches) return;
			toggle(card);
		});

		card.addEventListener('keydown', function (e) {
			if (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') return;
			if (e.target !== card) return;
			e.preventDefault();
			toggle(card);
		});

		// CSS flips the card on :focus-within, so keep aria-expanded honest.
		card.addEventListener('focusin', function () {
			card.setAttribute('aria-expanded', 'true');
		});
		card.addEventListener('focusout', function () {
			if (card.contains(document.activeElement)) return;
			card.setAttribute('aria-expanded', card.classList.contains('is-flipped') ? 'true' : 'false');
		});
	});

	// Tapping outside closes any open card.
	document.addEventListener('click', function (e) {
		if (!coarse.matches) return;
		if (e.target.closest && e.target.closest('[data-leader-flip]')) return;
		closeOthers(null);
	});
})();

/* ---------------------------------------------------------------------------
 * Project detail — story video facade.
 * A poster + play button loads the real player only on click (no third-party
 * iframe until the visitor asks for it). Type/embed come from PHP data attrs.
 * ------------------------------------------------------------------------- */
(function () {
	'use strict';
	var boxes = document.querySelectorAll('[data-project-video]');
	if (!boxes.length) return;

	Array.prototype.forEach.call(boxes, function (box) {
		var trigger = box.querySelector('[data-video-play]') || box;

		function play() {
			var type  = box.getAttribute('data-video-type');
			var embed = box.getAttribute('data-video-embed');
			if (!embed) return;

			var node;
			if (type === 'file') {
				node = document.createElement('video');
				node.src = embed;
				node.controls = true;
				node.autoplay = true;
				node.playsInline = true;
				node.className = 'absolute inset-0 w-full h-full object-cover bg-black';
			} else {
				node = document.createElement('iframe');
				node.src = embed;
				node.title = 'Project video';
				node.setAttribute('frameborder', '0');
				node.setAttribute('allow', 'autoplay; encrypted-media; fullscreen; picture-in-picture');
				node.setAttribute('allowfullscreen', '');
				node.className = 'absolute inset-0 w-full h-full';
			}

			['[data-video-poster]', '[data-video-scrim]', '[data-video-play]', '[data-video-caption]'].forEach(function (sel) {
				var el = box.querySelector(sel);
				if (el) el.remove();
			});
			box.classList.remove('cursor-pointer', 'group');
			box.appendChild(node);
		}

		trigger.addEventListener('click', play);
	});
})();

/* ---------------------------------------------------------------------------
 * Horizontal carousels — Related Infrastructure (and any [data-carousel]).
 * Prev/next arrows scroll the scroll-snap track by one card; arrows disable at
 * the ends. Works with touch/trackpad scrolling too.
 * ------------------------------------------------------------------------- */
(function () {
	'use strict';
	var carousels = document.querySelectorAll('[data-carousel]');
	if (!carousels.length) return;

	Array.prototype.forEach.call(carousels, function (root) {
		var track = root.querySelector('[data-carousel-track]');
		var prev  = root.querySelector('[data-carousel-prev]');
		var next  = root.querySelector('[data-carousel-next]');
		if (!track) return;

		// 'page' scrolls ~80% of the visible width (good for many small chips);
		// default scrolls one item (good for card carousels).
		var pageMode = root.getAttribute('data-carousel-step') === 'page';

		function step() {
			if (pageMode) { return Math.max(track.clientWidth * 0.8, 120); }
			var first = track.firstElementChild;
			if (!first) return track.clientWidth;
			var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0) || 0;
			return first.getBoundingClientRect().width + gap;
		}

		function sync() {
			// Arrows only exist to signal + reach overflow — hide them entirely
			// when the track fits, so they never show on a non-scrolling row.
			var scrollable = track.scrollWidth - track.clientWidth > 1;
			[prev, next].forEach(function (b) { if (b) { b.style.display = scrollable ? '' : 'none'; } });
			if (!scrollable) return;
			var max = track.scrollWidth - track.clientWidth - 1;
			if (prev) prev.disabled = track.scrollLeft <= 0;
			if (next) next.disabled = track.scrollLeft >= max;
		}

		if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
		if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
		track.addEventListener('scroll', sync, { passive: true });
		window.addEventListener('resize', sync);
		sync();
	});
})();

/* ---------------------------------------------------------------------------
 * Newsletter subscribe.
 * Progressive enhancement: [data-newsletter] forms post to admin-ajax and show
 * inline feedback. Config (ajaxUrl, nonce, i18n) is localized as `deonNewsletter`
 * in functions.php. If that global is absent, we leave the forms untouched.
 * ------------------------------------------------------------------------- */
(function () {
	var cfg = window.deonNewsletter;
	if (!cfg || !cfg.ajaxUrl) return;

	var forms = document.querySelectorAll('[data-newsletter]');
	if (!forms.length) return;

	forms.forEach(function (form) {
		var input = form.querySelector('input[type="email"]');
		var btn = form.querySelector('[data-newsletter-submit]');
		var msg = form.querySelector('[data-newsletter-msg]');
		var busy = false;

		function showMsg(text, isError) {
			if (!msg) return;
			msg.textContent = text;
			msg.classList.remove('hidden');
			// Tint errors red without disturbing the per-form success color.
			msg.style.color = isError ? '#dc2626' : '';
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (busy) return;

			var email = input ? input.value.trim() : '';
			// Basic client-side guard; the server validates authoritatively.
			if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
				showMsg(cfg.i18n.invalid, true);
				if (input) input.focus();
				return;
			}

			var data = new FormData();
			data.append('action', 'deon_newsletter');
			data.append('nonce', cfg.nonce);
			data.append('email', email);
			data.append('source', form.getAttribute('data-source') || 'newsletter');
			var hp = form.querySelector('input[name="deon_hp"]');
			if (hp) data.append('deon_hp', hp.value);

			busy = true;
			if (btn) btn.disabled = true;

			fetch(cfg.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (res) { return res.json().catch(function () { return null; }); })
				.then(function (json) {
					// The server sends the authoritative message for success/exists/error;
					// cfg.i18n.error only covers a malformed/empty response.
					var serverMsg = json && json.data && json.data.message;
					if (json && json.success) {
						showMsg(serverMsg || cfg.i18n.error, !serverMsg);
						if (serverMsg) form.reset();
					} else {
						showMsg(serverMsg || cfg.i18n.error, true);
					}
				})
				.catch(function () { showMsg(cfg.i18n.error, true); })
				.finally(function () {
					busy = false;
					if (btn) btn.disabled = false;
				});
		});
	});
})();

/*
 * Count-up — stat numbers tick 0 → value when they scroll into view.
 * Any element tagged [data-count-up] whose text contains a number animates.
 * A number's prefix ($) and suffix (MW, %, +, B…) are preserved; decimals and
 * thousands separators are kept; non-numeric text (e.g. "Gujarat") is left as
 * is. Honours prefers-reduced-motion and degrades to the final value with no
 * IntersectionObserver.
 */
(function () {
	var els = document.querySelectorAll('[data-count-up]');
	if (!els.length) return;

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function parse(text) {
		var m = /^(\D*?)([\d,]*\.?\d+)(.*)$/.exec(text);
		if (!m) return null;
		var raw = m[2];
		return {
			prefix: m[1],
			suffix: m[3],
			target: parseFloat(raw.replace(/,/g, '')),
			decimals: raw.indexOf('.') !== -1 ? raw.split('.')[1].length : 0,
			grouped: raw.indexOf(',') !== -1
		};
	}

	function format(n, p) {
		var s = n.toFixed(p.decimals);
		if (p.grouped) {
			var parts = s.split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			s = parts.join('.');
		}
		return p.prefix + s + p.suffix;
	}

	function run(el) {
		var p = parse((el.textContent || '').trim());
		if (!p) return;              // no number — leave the text untouched
		if (reduce) return;          // reduced motion — keep the final value
		var dur = 1400, start = null;
		el.textContent = format(0, p);
		requestAnimationFrame(function step(ts) {
			if (start === null) start = ts;
			var t = Math.min((ts - start) / dur, 1);
			var eased = 1 - Math.pow(1 - t, 3); // easeOutCubic
			el.textContent = format(p.target * eased, p);
			if (t < 1) requestAnimationFrame(step);
			else el.textContent = format(p.target, p);
		});
	}

	if (!('IntersectionObserver' in window)) return; // leave final values in place

	var io = new IntersectionObserver(function (entries, obs) {
		entries.forEach(function (e) {
			if (e.isIntersecting) { run(e.target); obs.unobserve(e.target); }
		});
	}, { threshold: 0.4 });

	els.forEach(function (el) { io.observe(el); });
})();

/* ---------------------------------------------------------------------------
 * Video lightbox — opens a YouTube/Vimeo iframe or an inline <video> (MP4)
 * for any [data-video-trigger]. One overlay, lazily built, reused. Closing
 * clears the player so playback stops. Used by the Investor Video Library.
 * ------------------------------------------------------------------------- */
(function () {
	var triggers = document.querySelectorAll('[data-video-trigger]');
	if (!triggers.length) return;

	var overlay, stage, lastFocus;

	function build() {
		overlay = document.createElement('div');
		overlay.className = 'deon-video-overlay';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.hidden = true;
		overlay.innerHTML =
			'<div class="deon-video-backdrop" data-video-close></div>' +
			'<div class="deon-video-dialog">' +
				'<button type="button" class="deon-video-x" data-video-close aria-label="Close video">&times;</button>' +
				'<div class="deon-video-stage"></div>' +
			'</div>';
		document.body.appendChild(overlay);
		stage = overlay.querySelector('.deon-video-stage');
		overlay.addEventListener('click', function (e) {
			if (e.target.hasAttribute('data-video-close')) close();
		});
	}

	function open(type, src) {
		if (!overlay) build();
		if (type === 'file') {
			stage.innerHTML = '<video class="deon-video-el" src="' + src + '" controls autoplay playsinline></video>';
		} else {
			stage.innerHTML = '<iframe class="deon-video-el" src="' + src + '" title="Video player" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
		}
		overlay.hidden = false;
		document.body.style.overflow = 'hidden';
		var x = overlay.querySelector('.deon-video-x');
		if (x) x.focus();
	}

	function close() {
		if (!overlay || overlay.hidden) return;
		stage.innerHTML = ''; // stops playback
		overlay.hidden = true;
		document.body.style.overflow = '';
		if (lastFocus && lastFocus.focus) lastFocus.focus();
	}

	triggers.forEach(function (t) {
		t.addEventListener('click', function () {
			lastFocus = t;
			open(t.getAttribute('data-video-type'), t.getAttribute('data-video-src'));
		});
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') close();
	});
})();

/* ---------------------------------------------------------------------------
 * Video library pagination — client-side, responsive page size. Shows one
 * page of [data-video-item] cards at a time so long lists don't force endless
 * scrolling (esp. mobile). Nav is hidden unless there's more than one page.
 * ------------------------------------------------------------------------- */
(function () {
	var pager = document.querySelector('[data-video-pager]');
	if (!pager) return;

	var items  = Array.prototype.slice.call(pager.querySelectorAll('[data-video-item]'));
	var nav    = document.querySelector('[data-video-nav]');
	var status = nav && nav.querySelector('[data-video-status]');
	var prev   = nav && nav.querySelector('[data-video-prev]');
	var next   = nav && nav.querySelector('[data-video-next]');
	if (!items.length || !nav) return;

	var sizeDesktop = parseInt(pager.getAttribute('data-page-size'), 10) || 4;
	var sizeMobile  = parseInt(pager.getAttribute('data-page-size-mobile'), 10) || sizeDesktop;
	var mq   = window.matchMedia('(max-width: 767px)'); // Tailwind md breakpoint
	var page = 0;

	function pageSize() { return mq.matches ? sizeMobile : sizeDesktop; }
	function pageCount() { return Math.max(1, Math.ceil(items.length / pageSize())); }

	function render() {
		var size = pageSize();
		var total = pageCount();
		if (page > total - 1) page = total - 1; // clamp after a resize
		var start = page * size;
		items.forEach(function (el, i) {
			// Inline style beats the .flex utility class; the `hidden` attribute
			// would lose to it (preflight is off, equal specificity, class wins).
			el.style.display = (i < start || i >= start + size) ? 'none' : '';
		});
		if (total <= 1) {
			nav.classList.add('hidden');
			nav.classList.remove('flex');
			return;
		}
		nav.classList.remove('hidden');
		nav.classList.add('flex');
		if (status) status.textContent = (page + 1) + ' / ' + total;
		if (prev) prev.disabled = (page === 0);
		if (next) next.disabled = (page === total - 1);
	}

	if (prev) prev.addEventListener('click', function () { if (page > 0) { page--; render(); } });
	if (next) next.addEventListener('click', function () { if (page < pageCount() - 1) { page++; render(); } });

	// Re-flow when crossing the mobile/desktop boundary.
	if (mq.addEventListener) mq.addEventListener('change', render);
	else if (mq.addListener) mq.addListener(render);

	render();
})();

/* ---------------------------------------------------------------------------
 * Leadership grid pagination — client-side, per-section. Each
 * [data-leader-paginate] holds one leader grid ([data-leader-track]) plus its
 * own prev/next controls; both Board and Key Management run independently.
 * Shows one page of cards at a time (data-page-size, default 10). Controls stay
 * hidden when everything fits on one page. No URL changes, no reload — matches
 * the flip-card client behavior already on this page.
 * ------------------------------------------------------------------------- */
(function () {
	document.querySelectorAll('[data-leader-paginate]').forEach(function (root) {
		var track = root.querySelector('[data-leader-track]');
		if (!track) return;

		var cards = Array.prototype.slice.call(track.children);
		var nav   = root.querySelector('[data-leader-pagination]');
		var size  = parseInt(root.getAttribute('data-page-size'), 10) || 10;

		function pageCount() { return Math.max(1, Math.ceil(cards.length / size)); }

		var page = 1; // 1-based, to match the shared pager.

		function render() {
			var total = pageCount();
			if (page > total) page = total;
			if (page < 1) page = 1;
			var start = (page - 1) * size;
			cards.forEach(function (el, i) {
				// Inline style beats the grid utility class; the `hidden`
				// attribute would lose to it (equal specificity, class wins).
				el.style.display = (i < start || i >= start + size) ? 'none' : '';
			});
			// Shared `.deon-pager` control (inc/pagination.php twin) — hides
			// itself when everything fits on one page.
			if (nav) deonBuildPager(nav, page, total, goTo);
		}

		function goTo(p) {
			page = p;
			render();
		}

		render();
	});
})();

/* ---------------------------------------------------------------------------
 * Page loading — preloader curtain + navigation progress bar.
 * Markup: header.php. Styles: main.css (`Page loading` block).
 *
 * Curtain: hidden on window load (after a short minimum so it can't strobe on
 * a warm cache), with a hard failsafe in case an asset never fires `load`.
 * Progress bar: a fake-but-honest ramp shown while the browser fetches the
 * next document — classic multi-page navigation has no progress event, so the
 * bar creeps toward 90% and only completes when the page actually unloads.
 * ------------------------------------------------------------------------- */
(function () {
	'use strict';

	var MIN_VISIBLE = 350;  // ms — below this the curtain reads as a flicker.
	var FAILSAFE    = 6000; // ms — a stalled asset must never trap the page.
	var start       = Date.now();

	var curtain  = document.querySelector('[data-preloader]');
	var progress = document.querySelector('[data-nav-progress]');
	var bar      = progress && progress.querySelector('.deon-progress__bar');

	/* ----- curtain ----- */
	var hidden = false;

	function hideCurtain() {
		if (hidden || !curtain) return;
		hidden = true;
		curtain.classList.add('is-done');
		// Drop it entirely once the fade is over; a fixed, faded overlay left
		// in the DOM still swallows clicks in some engines.
		window.setTimeout(function () {
			if (curtain.parentNode) curtain.parentNode.removeChild(curtain);
		}, 600);
	}

	function scheduleHide() {
		var waited = Date.now() - start;
		window.setTimeout(hideCurtain, Math.max(0, MIN_VISIBLE - waited));
	}

	if (curtain) {
		if (document.readyState === 'complete') {
			scheduleHide();
		} else {
			window.addEventListener('load', scheduleHide);
		}
		window.setTimeout(hideCurtain, FAILSAFE);
	}

	/* ----- navigation progress ----- */
	var timer   = null;
	var running = false;
	var pct     = 0;

	function setPct(p) {
		pct = p;
		if (bar) bar.style.transform = 'scaleX(' + (p / 100) + ')';
	}

	function startProgress() {
		if (running || !progress) return;
		running = true;
		setPct(0);
		progress.classList.add('is-active');
		// Ease toward 90% — never claim done until the document actually goes.
		timer = window.setInterval(function () {
			setPct(pct + (90 - pct) * 0.12);
		}, 180);
	}

	function stopProgress(complete) {
		if (!progress) return;
		window.clearInterval(timer);
		timer = null;
		running = false;
		if (complete) setPct(100);
		progress.classList.remove('is-active');
		window.setTimeout(function () { setPct(0); }, 300);
	}

	// Same-document, new-tab, download and non-http links must not trigger it.
	function isPageNavigation(link) {
		if (!link || !link.href) return false;
		if (link.target && link.target !== '_self') return false;
		if (link.hasAttribute('download')) return false;
		if (link.getAttribute('href').charAt(0) === '#') return false;
		if (link.protocol !== 'http:' && link.protocol !== 'https:') return false;
		if (link.origin !== window.location.origin) return false;
		// Pure hash change on the current page — no document fetch.
		return !(link.pathname === window.location.pathname &&
			link.search === window.location.search && link.hash);
	}

	document.addEventListener('click', function (e) {
		if (e.defaultPrevented || e.button !== 0) return;
		if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
		var link = e.target.closest && e.target.closest('a[href]');
		if (isPageNavigation(link)) startProgress();
	});

	document.addEventListener('submit', function (e) {
		var form = e.target;
		// AJAX forms (newsletter) call preventDefault — they never leave the page.
		window.setTimeout(function () {
			if (!e.defaultPrevented && form.method.toLowerCase() !== 'dialog') startProgress();
		}, 0);
	});

	window.addEventListener('beforeunload', function () { startProgress(); });

	// Back/forward out of the bfcache restores this page mid-animation: the
	// curtain comes back with its old classes and the bar is stuck part-way.
	window.addEventListener('pageshow', function (e) {
		if (!e.persisted) return;
		stopProgress(false);
		hideCurtain();
	});
})();

/* ---------------------------------------------------------------------------
 * Careers — application submit confirmation dialog.
 * Markup: template-parts/job/apply.php ([data-apply-modal]), rendered only when
 * a form-plugin shortcode is configured (Customize → Careers).
 *
 * Contact Form 7 reports the outcome in a small response line at the foot of
 * the form, which applicants miss. We listen for CF7's own DOM events, suppress
 * that line for terminal outcomes and raise the dialog instead. Validation
 * errors (wpcf7invalid) are deliberately left alone — those belong beside the
 * fields they refer to.
 * ------------------------------------------------------------------------- */
(function () {
	'use strict';

	var modal = document.querySelector('[data-apply-modal]');
	if (!modal) return;

	var titleEl = modal.querySelector('[data-apply-modal-title]');
	var msgEl   = modal.querySelector('[data-apply-modal-message]');
	var okIcon  = modal.querySelector('[data-apply-modal-icon="success"]');
	var errIcon = modal.querySelector('[data-apply-modal-icon="error"]');
	var lastFocus = null;

	function open(state, reason) {
		var ok = state !== 'error';
		// A reason from the built-in form (?reason=nocv) swaps in copy that says
		// what to fix; anything unmapped falls back to the generic error line.
		var detail = (!ok && reason) ? modal.getAttribute('data-message-' + reason) : null;

		titleEl.textContent = modal.getAttribute(ok ? 'data-title-success' : 'data-title-error');
		msgEl.textContent   = detail || modal.getAttribute(ok ? 'data-message-success' : 'data-message-error');
		// Inline display beats the `hidden`/`flex` utility classes, which carry
		// equal specificity and would resolve by stylesheet order.
		okIcon.style.display  = ok ? 'flex' : 'none';
		errIcon.style.display = ok ? 'none' : 'flex';

		lastFocus = document.activeElement;
		modal.style.display = 'flex';
		document.body.style.overflow = 'hidden';

		var focusable = modal.querySelector('[data-apply-modal-close]:not(.absolute)') ||
			modal.querySelector('[data-apply-modal-close]');
		if (focusable && focusable.focus) focusable.focus();
	}

	function close() {
		modal.style.display = '';
		document.body.style.overflow = '';
		if (lastFocus && lastFocus.focus) lastFocus.focus();
	}

	modal.addEventListener('click', function (e) {
		if (e.target.closest('[data-apply-modal-close]')) close();
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && modal.style.display === 'flex') close();
	});

	// Keep tabbing inside the dialog while it is up.
	modal.addEventListener('keydown', function (e) {
		if (e.key !== 'Tab') return;
		var items = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
		if (!items.length) return;
		var first = items[0], last = items[items.length - 1];
		if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
	});

	// CF7 events fire on the form element and bubble to document.
	function handle(state) {
		return function (e) {
			var out = e.target && e.target.querySelector
				? e.target.querySelector('.wpcf7-response-output')
				: null;
			if (out) out.style.display = 'none'; // the dialog is now the report
			open(state);
		};
	}

	document.addEventListener('wpcf7mailsent', handle('success'));
	document.addEventListener('wpcf7mailfailed', handle('error'));
	document.addEventListener('wpcf7spam', handle('error'));

	// A fresh attempt must show CF7's inline line again if it ever gets that far.
	document.addEventListener('wpcf7beforesubmit', function (e) {
		var out = e.target && e.target.querySelector
			? e.target.querySelector('.wpcf7-response-output')
			: null;
		if (out) out.style.display = '';
	});

	// Built-in form: the handler redirects back with ?applied=success|error
	// (plus ?reason= on failure). Report it, then strip the params so a reload
	// or a shared link does not replay the dialog.
	var params = new URLSearchParams(window.location.search);
	var applied = params.get('applied');
	if (applied === 'success' || applied === 'error') {
		open(applied, params.get('reason'));

		params.delete('applied');
		params.delete('reason');
		var query = params.toString();
		window.history.replaceState(
			{},
			'',
			window.location.pathname + (query ? '?' + query : '') + window.location.hash
		);
	}
})();

/* ---------------------------------------------------------------------------
 * Careers — application form file field.
 * The designed drop panel wraps a transparent native file input, so all we add
 * is the chosen filename and a drag-over cue.
 * Markup: template-parts/job/apply-form.php.
 * ------------------------------------------------------------------------- */
(function () {
	'use strict';

	var form = document.querySelector('[data-apply-form]');
	if (!form) return;

	var input = form.querySelector('#deon-apply-cv');
	var drop  = form.querySelector('[data-apply-drop]');
	var name  = form.querySelector('[data-apply-file-name]');
	var idle  = name ? name.textContent : '';

	if (input && name) {
		input.addEventListener('change', function () {
			name.textContent = input.files && input.files.length ? input.files[0].name : idle;
		});
	}

	if (drop) {
		['dragenter', 'dragover'].forEach(function (evt) {
			drop.addEventListener(evt, function () { drop.classList.add('border-deon-accent'); });
		});
		['dragleave', 'drop'].forEach(function (evt) {
			drop.addEventListener(evt, function () { drop.classList.remove('border-deon-accent'); });
		});
	}

	// Uploads take a moment; block the double-click that would post twice.
	form.addEventListener('submit', function () {
		var btn = form.querySelector('button[type="submit"]');
		if (btn) {
			btn.disabled = true;
			setTimeout(function () { btn.disabled = false; }, 10000); // re-arm if the POST fails.
		}
	});
})();
