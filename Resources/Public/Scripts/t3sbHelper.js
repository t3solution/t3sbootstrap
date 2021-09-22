/**
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

// Loading spinner - Main.html
function t3sbFadeOutEffect(fadeTarget) {
	var fadeEffect = setInterval(function () {
		if (!fadeTarget.style.opacity) {
			fadeTarget.style.opacity = 1;
		}
		if (fadeTarget.style.opacity > 0) {
			fadeTarget.style.opacity -= 0.1;
		} else {
			clearInterval(fadeEffect);
		}
	}, 60);
	fadeTarget.classList.add('d-none');		
}


// Magnifying glass icon - Main.html
function t3sbMagnifying(img, viewportWidth, navbarBreakpointWidth) {
	var zoomOverlay = img.parentElement.parentElement.querySelectorAll('div.zoom-overlay');
	if ( zoomOverlay.length ) {		
		var imgwidth = Math.round(img.getBoundingClientRect().width)+'px',
			imgheight = img.clientHeight+'px';
		if ( viewportWidth < navbarBreakpointWidth ) {
			imgwidth = '100%';
		}
		zoomOverlay[0].classList.add('card-img-overlay');
		if ( imgwidth ) {
			zoomOverlay[0].style.maxWidth = imgwidth;
		}
		if ( imgheight ) {
			zoomOverlay[0].style.maxHeight = imgheight;
		}
	}
}


// Scroll anchor - Main.html
function t3sbScrollToAnchor(sectionmenuAnchorOffset, fixedNavbar, navbarHeight, sectionhash=false) {
	var hash = window.location.hash;
	if (sectionhash) {
		hash = sectionhash;
	}
	if (hash.length) {
		var idArr = hash.split('#');
		var targetElement = document.getElementById(idArr[1]);
		if ( targetElement === null ) {
			idArr = hash.split('#c');
			targetElement = document.getElementById('tab-'+idArr[1]);
		}			
		if ( targetElement != null ) {
			var	offsetSize = sectionmenuAnchorOffset;
			if ( fixedNavbar ) {
				offsetSize += navbarHeight;
			}
			var scrollTo = Math.round(t3sbOffsetTop(targetElement)-offsetSize);
			window.scroll({ top: scrollTo, behavior: 'smooth' });
		}
	}
}


// Link to top - Main.html
function t3sbScrollToTop() {
	document.addEventListener('scroll', t3sbHandleScroll);
}

function t3sbHandleScroll() {
	var scrollToTopBtn = document.querySelector('.back-to-top');
	scrollToTopBtn.addEventListener('click', t3sbScrollIt);
	const scrollableHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
	const RATIO = 0.2;
	if ((document.documentElement.scrollTop / scrollableHeight ) > RATIO) {
		scrollToTopBtn.classList.add('st-block');
		scrollToTopBtn.classList.remove('st-none');
	} else {
		scrollToTopBtn.classList.add('st-none');
		scrollToTopBtn.classList.remove('st-block');
	}
}

function t3sbScrollIt(e) {
	e.preventDefault();
	window.scroll({ top: 0, behavior: 'smooth' });
}


// Fixed button on left or right browser edge - Page/Assets.html
function t3sbFixedButtons(buttons) {
	buttons.forEach( fixmodbtn => {
		var fixedButton = '';
		if ( fixmodbtn.classList.contains('fixedModalButton') ) {
			fixedButton = fixmodbtn.querySelector('button');
		} else {
			if ( fixmodbtn.classList.contains('fixedGroupButton') ) {				
				fixedButton = fixmodbtn.querySelector('div');
			} else {				
				fixedButton = fixmodbtn.querySelector('a');
			}
		}
		fixedButton.style.opacity = '0';
		fixmodbtn.classList.remove('d-none');
		t3sbFadeInEffect(fixedButton);
		if ( fixedButton.classList.contains('rotate-minus') || fixedButton.classList.contains('rotate-plus') ) {
			var position = fixedButton.clientWidth / 2 - fixedButton.clientHeight / 2;
			if ( fixmodbtn.classList.contains('fixedPosition-right') ) {
				fixmodbtn.style.right = '-'+position+'px';
			} else {
				fixmodbtn.style.left = '-'+position+'px';
			}
		}
	});
}

function t3sbFadeInEffect(fadeTarget) {
	window.setTimeout(() => {
		var opacity = 0;
		var interval = 50;
		var duration = 400;
		var gap = interval / duration;
		var fading = window.setInterval(() => {
			opacity = opacity + gap;
			fadeTarget.style.opacity = opacity;
			if (opacity <= 0) {
				fadeTarget.style.visibility = 'hidden';
			}
			if (opacity <= 0 || opacity >= 1) {
				window.clearInterval(fading);
			}
		}, interval);
	}, 2000);
}


// Sticky footer - Page/Footer.html
function t3sbStickyFooter(footerExtraHeight) {
	const footer = document.getElementById('page-footer');
	var footerHeight = footer.clientHeight;
	if ( footerExtraHeight > 0 ) {
		footerHeight += footerExtraHeight;
	}
	if ( footer.classList.contains('footer-sticky') ) {
		document.body.style.paddingBottom = footerHeight+'px';
	}
}


// Section Menu Navbar - Page/Navbar/Navbar.html
function t3sbSectionMenu(fixedNavbar,sectionmenuAnchorOffset,navbarHeight) {
	var scrollTrigger = document.querySelectorAll('.section-menu a.js-scroll-trigger');
	scrollTrigger.forEach( st => {
		st.addEventListener('click', (event) => {
			event.preventDefault();
			var anchor = event.currentTarget.getAttribute('href');
			if (anchor.length) {
				var offsetSize = sectionmenuAnchorOffset;		
				if ( fixedNavbar ) {
					offsetSize = navbarHeight+offsetSize;
				}
				var targetElement = document.getElementById(anchor.split('#')[1]);
				var scrollTo = Math.round(t3sbOffsetTop(targetElement)-offsetSize);
				window.scroll({ top: scrollTo, behavior: 'smooth' });
			}
		});
	});

	const navLinks = document.querySelectorAll('.nav-item:not(.dropdown)');
	const menuToggle = document.getElementById('navbarToggler');
	const bsCollapse = new bootstrap.Collapse(menuToggle, {toggle: false});
	navLinks.forEach((l) => {
		l.addEventListener('click', () => { bsCollapse.toggle() });
	});	
}

function t3sbOffsetTop(el) {
	var rect = el.getBoundingClientRect(),
		scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		return rect.top + scrollTop;
}


// Collapsible - Templates/Container/CollapsibleContainer.html
function t3sbCollapsible(fixedNavbar, navbarHeight) {
	if ( window.location.hash ) {
		var idArr = window.location.hash.split('#c'),
			accordionButton = document.getElementById('c'+idArr[1]);
		if (accordionButton !== null && accordionButton !== '') {
			var targetElement = document.getElementById('collapse-'+idArr[1]),
				collapseTrigger = new bootstrap.Collapse(targetElement),
				offsetSize = accordionButton.clientHeight;
			collapseTrigger.show();
			if ( fixedNavbar ) {
				offsetSize += navbarHeight;
			}
			var scrollTo = Math.round(t3sbOffsetTop(accordionButton)-offsetSize);
			window.scroll({ top: scrollTo, behavior: 'smooth' });
		}
	} else {		
		document.querySelectorAll('.accordion').forEach( acc => {
			var collapse = document.getElementById(acc.id);
			collapse.addEventListener('shown.bs.collapse', function (e) {
				var accordionItem = this.querySelector('.collapse.show');
				if ( accordionItem) {
					var offsetSize = accordionItem.parentNode.querySelector('.accordion-button').clientHeight;
					if ( fixedNavbar ) {
						offsetSize += navbarHeight;
					}
					var scrollTo = Math.round(t3sbOffsetTop(accordionItem.parentNode)-offsetSize);
					window.scroll({ top: scrollTo, behavior: 'smooth' });
				}
			});
		});		
	}	
}


// Tabs - Templates/Container/TabContainer.html
function t3sbTabs(fixedNavbar, navbarHeight) {
	if ( window.location.hash ) {
		var idArr = window.location.hash.split('#c');
		var targetElement = document.getElementById('tab-'+idArr[1]);		
		if (targetElement !== null && targetElement !== '') {
			var tabTrigger = new bootstrap.Tab(targetElement);
			tabTrigger.show();
			var offsetSize = targetElement.clientHeight;
			if ( fixedNavbar ) {
				offsetSize += navbarHeight;
			}
			var scrollTo = Math.round(t3sbOffsetTop(targetElement)-offsetSize);
			window.scroll({ top: scrollTo, behavior: 'smooth' });
		}
	} else {
		var tabs = document.querySelectorAll('.nav.nav-tabs, .nav.nav-pills');
		if ( tabs.length ) {
			tabs.forEach( tabcontainer => {
				var firstTabEl = document.querySelector('#'+tabcontainer.id+' li:first-child button');
				var firstTab = new bootstrap.Tab(document.getElementById(firstTabEl.id));
				firstTab.show();
			});
		}
	}
}


// Image copyright on click - Media/Type/Image.html
function t3sbCopyright() {
	document.querySelectorAll('.image .img-copyright').forEach( imgCopy => {
		imgCopy.addEventListener('click', function(ic) {
			var imgCopyright = ic.currentTarget;
			imgCopyright.style.opacity = 0;
			var tid = ic.currentTarget.parentNode.querySelector('.toast').id;
			var myToastEl = document.getElementById(tid);
			var myToast = bootstrap.Toast.getOrCreateInstance(myToastEl);
			var iw = Math.round(myToast._element.parentNode.querySelector('img').getBoundingClientRect().width);
			myToast.show();
			var th = Math.round(myToast._element.parentNode.querySelector('.toast').getBoundingClientRect().height);
			myToast._element.parentNode.querySelector('.toast').style.width = iw+'px';
			myToast._element.parentNode.querySelector('.toast').style.marginTop = -th+'px';
			myToast._element.addEventListener('hidden.bs.toast', function (icct) {
				imgCopyright.style.opacity = 1;
			});			
		});
	});	
}


// Card-flipper rotate on click - CarouselWrapper.html
function t3sbCardFlipper() {
	document.querySelectorAll('.card-flipper').forEach( cf => {
		cf.classList.add('cardflipper');
		cf.classList.remove('card-flipper');
	
	});
	document.querySelectorAll('.cardflipper .mainflip .fa-plus-square').forEach( mainflipper => {
		mainflipper.addEventListener('click', function(flipper) {
			var frontside = flipper.currentTarget.parentNode.parentNode.parentNode;
			frontside.style.transform = 'rotateY(180deg)';
			var backside = flipper.currentTarget.parentNode.parentNode.parentNode.parentNode.querySelector('.backside');
			backside.style.transform = 'rotateY(0deg)';
			var cardBody = backside.querySelector('.card-body');
			var el = document.createElement('div');
			el.classList.add('card-footer','jsfooter','text-center');
			el.innerHTML = '<i class="fas fa-minus-square fa-2x mx-auto"></i>';
			insertAfter(cardBody, el);
			backside.querySelector('.fa-minus-square').addEventListener('click', function(minus) {
				backside.style.transform = 'rotateY(180deg)';
				frontside.style.transform = 'rotateY(0deg)';
				if (backside.querySelector('.fa-minus-square') !== null && backside.querySelector('.fa-minus-square') !== '') {
					if (backside.querySelector('.card-footer').classList.contains('jsfooter')) {
						backside.querySelector('.card-footer').remove();
					}	
				}
			});
	
		});
	});	
}

function insertAfter(referenceNode, newNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}


// Navbar transparent - Navbar/Assets.html
function t3sbTransparentNavbar(colorschemes, gradient) {
	window.onscroll = function(){
		var navbar = document.getElementById('main-navbar');
		var top =	 window.pageYOffset || document.documentElement.scrollTop;
		if (top > 100) {
			navbar.classList.remove('bg-transparent');		
			navbar.classList.add(colorschemes);
			if (gradient) {
				navbar.classList.add(gradient);
			}
		} else {
			navbar.classList.add('bg-transparent');
			navbar.classList.remove(colorschemes);
			if (gradient) {
				navbar.classList.remove(gradient);
			}
		}
	};
}


// Shrinking Navbar on scrolling - Navbar/Assets.html
function t3sbShrinkingNavbar(navbar, padding) {
    let navShrinkColorschemes = navbar.getAttribute('data-shrinkcolorschemes'),
    	navShrinkColor = navbar.getAttribute('data-shrinkcolor'),
		navColorschemes = navbar.getAttribute('data-colorschemes'),
		navColor = navbar.getAttribute('data-color');
	window.addEventListener('scroll', function() {
		if ( t3sbOffsetTop(navbar) > 100) {		
			navbar.classList.remove('py-'+padding, navColorschemes, navColor);
			navbar.classList.add('navbar-shrink', navShrinkColor, navShrinkColorschemes)	
		} else {
			navbar.classList.remove('navbar-shrink', navShrinkColor, navShrinkColorschemes);
			navbar.classList.add('py-'+padding, navColorschemes, navColor);
		}
	});	
}


// Extra Row - Navbar/Assets.html
function t3sbExtraRow(vw, bp) {
	let extraRow = document.querySelector('.navbar .extra-row');
	if (extraRow) {
		if ( vw < bp ) {
			extraRow.classList.add('flex-row','me-auto','mt-4','mb-0');
			extraRow.classList.remove('ms-auto');		

		} else {
			extraRow.classList.remove('flex-row','me-auto','mt-4','mb-0');
			extraRow.classList.add('ms-auto');
		}
	}
}


// Dropdown animate - Navbar/Assets.html
function t3sbDropdownAnimate(vw, bp, dropdownAnimateValue) {
	if ( vw < bp ) {
		document.querySelectorAll('.dd-animate-'+dropdownAnimateValue).forEach(function(dda) {
			dda.classList.remove('dd-animate-'+dropdownAnimateValue);
			dda.classList.add('removed-animate-'+dropdownAnimateValue);
		});
	} else {
		document.querySelectorAll('.removed-animate-'+dropdownAnimateValue).forEach(function(dda) {
			dda.classList.add('dd-animate-'+dropdownAnimateValue);
			dda.classList.remove('removed-animate-'+dropdownAnimateValue);
		});		
	}
}


// Local video - BackgroundWrapper.html
function t3sbLocalVideo(uid, overlay, autoplay, controls, loop, mute, videoElement) {	
	videoElement.muted = mute;
	videoElement.loop = loop;	
	if (autoplay) {
		videoElement.pause();
		videoElement.currentTime = 0;
		videoElement.play();
		videoElement.setAttribute('playsinline', '');
	}	
	if (controls) {
		if (overlay) {
			var overlayChild = document.querySelector('#s-'+uid+' .card-img-overlay');
			overlayChild.style.top = '20px';
			overlayChild.style.bottom = '20px';		
		}
	} else {
		if ( videoElement.hasAttribute('controls') ) {
			videoElement.removeAttribute('controls');
		}
	}
}


// Offcanvas - Navbar/Assets.html
function t3sbOffcanvas(vw, bp, utilColorArr) {
	let navbarToggler = document.getElementById('navbarToggler');	
	if ( vw > bp ) {
		navbarToggler.classList.remove(utilColorArr[0]);
		navbarToggler.classList.remove(utilColorArr[1]);	
	} else {
		navbarToggler.classList.add(utilColorArr[0]);
		navbarToggler.classList.add(utilColorArr[1]);
	}
	let extraRow = document.querySelector('.navbar .extra-row');
	if (extraRow) {
		if ( vw < bp ) {
			extraRow.classList.add('ms-3');
		} else {
			extraRow.classList.remove('ms-3');
		}
	}
}


// Searchbox in Navbar - Navbar/Assets.html
function t3sbNavbarSearchbox(vw, bp) {
	let	form = document.querySelector('.navbar form');
	if ( vw < bp ) {
		if (form.classList.contains('ms-auto')) {
			form.classList.remove('ms-auto');
			form.classList.add('xl-ms-auto');
		}
		form.classList.add('mt-3');
	} else {
		if (form.classList.contains('xl-ms-auto')) {
			form.classList.add('ms-auto');
			form.classList.remove('xl-ms-auto');
		}
		form.classList.remove('mt-3');
	}
}


// Parallax - ParallaxWrapper.html
function t3sbParallax(addHeight) {
	document.querySelectorAll('.pv-container').forEach(function(pvc) {	
		var pvb = pvc.querySelector('.pv-block'),
			pvHeight = 0,
			textWrapper = pvc.querySelector('.text-wrapper');
		if (textWrapper !== null && textWrapper !== '') {
			textWrapper = textWrapper.querySelector('.parallax-text');
			pvHeight = parseInt(textWrapper.clientHeight + addHeight);
		} else {
			pvHeight = parseInt(300 + addHeight);
		}
		pvc.setAttribute('pv-height', pvHeight+'px');

		if (pvc.querySelector('.parallax-text')) {
			var anchorId = pvc.querySelector('.parallax-text').firstChild.id;
			pvc.querySelector('.parallax-text').firstChild.removeAttribute('id');
			pvc.setAttribute('id', anchorId);
		}

		pv.init();
	});
}


// Autoheight for backgroundimages - BackgroundWrapper.html
function t3sbAddHeight(TYPO3, objKey) {
	var overlay = document.getElementById(objKey).nextElementSibling;
	if (overlay) {
		var totalHeight = 0;
		var addHeight = 0;
		TYPO3.settings.ADDHEIGHT.forEach(function(value, key) {
			if (objKey == 'bg-img-'+key) {
				addHeight = parseInt(value);
			}
		});	
		if ( overlay.querySelector('.container') ) {
			overlay.getElementsByClassName('container')[0].childNodes.forEach( bgwContent => {
				totalHeight = bgwContent.clientHeight;
			});
			totalHeight = totalHeight + addHeight;
			document.getElementById(objKey).style.minHeight = totalHeight+'px';
		} else {
			totalHeight = overlay.childNodes[0].clientHeight;
			totalHeight = totalHeight + addHeight;
			document.getElementById(objKey).style.minHeight = totalHeight+'px';
		}
	}
	var mobile = false;
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		mobile = true;
	}
	if (mobile){
		document.querySelectorAll('.background-image').forEach( bgimg => {			
			bgimg.classList.remove('background-fixed');
		});
	}
}

/**
 * Object.prototype.forEach(TYPO3.settings.ADDHEIGHT) polyfill
 * https://gomakethings.com/looping-through-objects-with-es6/
 * @author Chris Ferdinandi
 * @license MIT
 */
if (!Object.prototype.forEach) {
	Object.defineProperty(Object.prototype, 'forEach', {
		value: function (callback, thisArg) {
			if (this == null) {
				throw new TypeError('Not an object');
			}
			thisArg = thisArg || window;
			for (var key in this) {
				if (this.hasOwnProperty(key)) {
					callback.call(thisArg, this[key], key, this);
				}
			}
		}
	});
}

