"use strict";
// jQuery function for https://github.com/daneden/animate.css
$.fn.extend({
	animateCss: function (animationName, callback) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		this.addClass('animated ' + animationName).one(animationEnd, function () {
			$(this).removeClass('animated ' + animationName);
			if (callback) {
				callback();
			}
		});
		return this;
	}
});