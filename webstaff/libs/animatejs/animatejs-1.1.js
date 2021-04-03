"use strict";
// jQuery function for https://github.com/daneden/animate.css
(function ($) {
	$.fn.animateCss = function (options) {
		var _settings = $.extend({
			effect: "bounce", // One of the effects on the list above.
			duration: "1s", // Amount of seconds that the effect will be active.
			infinite: false, // Will the effect run infinitely?
			end: function () {} // Function to execute when the effect ends. Won't run if infinite:true!
		}, options);

		this.addClass("animated " + _settings.effect + ((_settings.infinite) ? " infinite" : ""));
		this.css("animation-duration", _settings.duration);
		this.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
			$(this).removeClass("animated " + _settings.effect);
			$(this).css("animation-duration", "");
			_settings.end.call(this);
		});
	};
}(jQuery));