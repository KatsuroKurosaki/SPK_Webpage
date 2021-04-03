"use strict";
// jQuery function for https://github.com/daneden/animate.css
(function ($) {
	$.fn.animateCss = function (options) {
		var _settings = $.extend({
			effect: "bounce", // One of the effects on the list above.
			duration: "1s", // Amount of seconds that the effect will be active.
			delay: "0s", // Amount of seconds before the effect begins.
			infinite: false, // Will the effect run infinitely?
			begin: function () {}, // Function to execute before the effect starts. Doesn't care about delay.
			end: function () {} // Function to execute when the effect ends. Won't run if infinite:true!
		}, options);

		_settings.begin();
		this.addClass("animated " + _settings.effect + ((_settings.infinite) ? " infinite" : ""))
			.css("animation-duration", _settings.duration)
			.css("animation-delay", _settings.delay)
			.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
				$(this).removeClass("animated " + _settings.effect).css("animation-duration", "").css("animation-delay", "");
				_settings.end();
			});
		return this;
	};
}(jQuery));