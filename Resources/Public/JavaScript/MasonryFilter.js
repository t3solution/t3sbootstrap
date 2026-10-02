/*
 * Category filter of the masonry wrapper - EXT:t3sbootstrap
 *
 * Binds to every element with data-t3sb-masonry-filter. Its value is the id of the
 * grid. Filtered is the cell, which carries its filter values as a comma list in
 * data-t3sb-categories - a comma separated list of filter values.
 *
 * Two ways of filtering:
 *
 *   1. With Shuffle.js - the cells move to their new place and the gaps close.
 *      Shuffle 7 ships as an ES module only, so it is pulled in with a dynamic
 *      import(). The path sits in data-t3sb-shuffle-src.
 *
 *   2. Without it - show and hide through the hidden attribute. That is also what
 *      happens when the import fails: the grid stays usable, only the animation and
 *      the reflow are missing.
 */
(function () {
	'use strict';

	function valuesOf(cell) {
		return (cell.getAttribute('data-t3sb-categories') || '')
			.split(',')
			.map(function (value) { return value.trim(); })
			.filter(function (value) { return value !== ''; });
	}

	function matches(cell, wanted) {
		return wanted === '' || valuesOf(cell).indexOf(wanted) !== -1;
	}

	function markActive(buttons, active) {
		buttons.forEach(function (button) {
			var istAktiv = button === active;
			button.classList.toggle('btn-primary', istAktiv);
			button.classList.toggle('btn-outline-primary', !istAktiv);
			button.setAttribute('aria-pressed', istAktiv ? 'true' : 'false');
		});
	}

	/** Without Shuffle: the cell keeps its place, it is only hidden. */
	function plainFilter(grid) {
		return function (wanted) {
			grid.querySelectorAll('[data-t3sb-categories]').forEach(function (cell) {
				cell.hidden = !matches(cell, wanted);
			});
		};
	}

	/** With Shuffle: filter() is called with one function per element. */
	function shuffleFilter(instance) {
		return function (wanted) {
			instance.filter(function (element) {
				return matches(element, wanted);
			});
		};
	}

	function bind(bar) {
		var grid = document.getElementById(bar.getAttribute('data-t3sb-masonry-filter') || '');

		if (!grid) {
			return;
		}

		var buttons = Array.prototype.slice.call(bar.querySelectorAll('[data-t3sb-filter-value]'));

		if (buttons.length === 0) {
			return;
		}

		function activate(filter) {
			buttons.forEach(function (button) {
				button.addEventListener('click', function () {
					filter(button.getAttribute('data-t3sb-filter-value') || '');
					markActive(buttons, button);
				});
			});
		}

		var source = bar.getAttribute('data-t3sb-shuffle-src') || '';

		if (source === '') {
			activate(plainFilter(grid));

			return;
		}

		import(source)
			.then(function (modul) {
				var Shuffle = modul.default;
				var instance = new Shuffle(grid, {
					itemSelector: '[data-t3sb-categories]'
				});

				activate(shuffleFilter(instance));
			})
			.catch(function () {
				// The library did not load - filtering still works, without the animation.
				activate(plainFilter(grid));
			});
	}

	function start() {
		document.querySelectorAll('[data-t3sb-masonry-filter]').forEach(bind);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
