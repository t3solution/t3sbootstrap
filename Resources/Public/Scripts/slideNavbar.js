
var wrapper	   = $("#page-wrapper"),
	menu		= $(".menu"),
	menuLinks	= $(".menu ul li a"),
	toggle		= $(".nav-toggle"),
	toggleIcon = $(".nav-toggle span");

function toggleThatNav() {
	if (menu.hasClass("show-nav")) {
		menu.removeClass("show-nav");
		toggle.removeClass("show-nav");
	} else {
		menu.addClass("show-nav");
		toggle.addClass("show-nav");
	}
}

function changeToggleClass() {
	toggleIcon.toggleClass("fa-times");
	toggleIcon.toggleClass("fa-bars");
}

$(function() {
	toggle.on("click", function(e) {
		e.stopPropagation();
		e.preventDefault();
		toggleThatNav();
		changeToggleClass();
	});
	wrapper.on("click", function(e) {
		if (menu.hasClass("show-nav")) {
			menu.removeClass("show-nav");
			toggle.removeClass("show-nav");
			changeToggleClass();
		}
	});
});