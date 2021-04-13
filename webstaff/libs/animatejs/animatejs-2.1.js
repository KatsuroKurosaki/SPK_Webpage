"use strict";
/*
//Run it:
animateCSS('.my-element', 'bounce');
animateCSS('.my-element', ['fadeInDown', 'faster']);

//Run it and run a function once it finishes
animateCSS('.my-element', 'bounce').then((message) => {
	// Do something after the animation
});
*/
const animateCSS = (element, animation, prefix = 'animate__') =>
	// We create a Promise and return it
	new Promise((resolve, reject) => {
		let animationName = [`${prefix}animated`];

		const node = document.querySelector(element);

		if (typeof animation === "array" || typeof animation === "object") {
			animation.forEach((element, index) => {
				animation[index] = `${prefix}${element}`;
			});
			animationName = animationName.concat(animation);
		} else {
			animationName = animationName.concat([`${prefix}${animation}`]);
		}

		node.classList.add(...animationName);
		// When the animation ends, we clean the classes and resolve the Promise
		function handleAnimationEnd() {
			node.classList.remove(...animationName);
			node.removeEventListener('animationend', handleAnimationEnd);
			resolve('Animation ended');
		}

		node.addEventListener('animationend', handleAnimationEnd);
	});