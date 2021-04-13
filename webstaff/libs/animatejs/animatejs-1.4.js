"use strict";
// jQuery function for https://github.com/daneden/animate.css
(function ($) {
	$.fn.animateCss = function (options) {
		var _settings = $.extend({}, $.fn.animateCss.defaults, options);

		_settings.begin.call(this);
		this.addClass("animate__animated animate__" + _settings.effect);
		this.css("animation-duration", _settings.duration);
		this.css("animation-delay", _settings.delay);
		this.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
			$(this).removeClass("animate__animated animate__" + _settings.effect);
			$(this).css("animation-duration", "");
			$(this).css("animation-delay", "");
			_settings.end.call(this);
		});

		return this;
	};

	$.fn.animateCss.defaults = {
		effect: "bounce", // The animation effect.
		duration: "1s", // Amount of seconds that the effect will be active.
		delay: "0s", // Amount of seconds before the effect begins.
		begin: function () {}, // Function to execute before the effect starts. Doesn't care about delay.
		end: function () {} // Function to execute when the effect ends. Won't run if infinite:true!
	}
}(jQuery));