(function($bs) {
	const CLASS_NAME = 'has-child-dropdown-show';
	$bs.Dropdown.prototype.toggle = function(_orginal) {
		return function() {
			document.querySelectorAll('.' + CLASS_NAME).forEach(function(e) {
				e.classList.remove(CLASS_NAME);
			});
			if (this._element.closest('.dropdown') != null) {
				let dd = this._element.closest('.dropup').parentNode.closest('.dropup');
				for (; dd && dd !== document; dd = dd.parentNode.closest('.dropup')) {
					dd.classList.add(CLASS_NAME);
				}
			}
			return _orginal.call(this);
		};
	}($bs.Dropdown.prototype.toggle);

	document.querySelectorAll('.dropup').forEach(function(dd) {
		dd.addEventListener('hide.bs.dropdown', function(e) {
			if(this.classList.contains(CLASS_NAME)) {
				this.classList.remove(CLASS_NAME);
				e.preventDefault();
			}
			e.stopPropagation();
		});
	});

	document.querySelectorAll('.dropup [data-bs-toggle="dropdown"]').forEach(function(dd) {
		dd.addEventListener('click', function(e) {
			var navbar = document.getElementById('main-navbar'),
				clickableparent = navbar.classList.contains('clickableparent') ? 1 : 0,
				isHover = navbar.classList.contains('navbarHover') ? 1 : 0,
				href = e.currentTarget.getAttribute('href');
			if ( clickableparent ) {
				if (isHover) {
					e.preventDefault();	
				}
				if(href != '#') {
					location.href = href;
				}
			}
		});
	});

	function getDropdown(element) {
		return $bs.Dropdown.getInstance(element) || new $bs.Dropdown(element);
	}

	document.querySelectorAll('.dropdown-hover, .dropdown-hover-all .dropup').forEach(function(dd) {
		dd.addEventListener('mouseenter', function(e) {
			let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]');
			if (!toggle.classList.contains('show')) {
				getDropdown(toggle).toggle();
			}
		});
		dd.addEventListener('mouseleave', function(e) {
			let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]');
			if (toggle.classList.contains('show')) {
				getDropdown(toggle).toggle();
			}
		});
	});

})(bootstrap);
