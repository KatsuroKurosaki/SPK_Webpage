"use strict";

(function ($) {

	// Generates a thumbnail
	$.fn.imageThumbnailer = function (options) {
		var _settings = $.extend({
			width: 1024,
			height: 768,
			rotation: 0,
			imgFile: null,
			container: this
		}, options);
		//alert(JSON.stringify(_settings, null, 4));

		var image = new Image();
		image.onload = function (e) {
			var newSize = $.calculateAspectRatio(image.width, image.height, _settings.width, _settings.height);
			var canvas = document.createElement("canvas");
			var context = canvas.getContext("2d");

			switch (_settings.rotation) {
				/*case 2:
					context.transform(-1, 0, 0, 1, newSize.width, 0);
					break;*/
				case 3:
					canvas.width = newSize.width;
					canvas.height = newSize.height;
					context.transform(-1, 0, 0, -1, newSize.width, newSize.height);
					break;
					/*case 4:
						context.transform(1, 0, 0, -1, 0, newSize.height);
						break;*/
					/*case 5:
						context.transform(0, 1, 1, 0, 0, 0);
						break;*/
				case 6:
					canvas.width = newSize.height;
					canvas.height = newSize.width;
					context.transform(0, 1, -1, 0, newSize.height, 0);
					break;
					/*case 7:
						context.transform(0, -1, -1, 0, newSize.height, newSize.width);
						break;*/
				case 8:
					canvas.width = newSize.height;
					canvas.height = newSize.width;
					context.transform(0, -1, 1, 0, 0, newSize.width);
					break;
				default:
					canvas.width = newSize.width;
					canvas.height = newSize.height;
					break;
			}

			context.drawImage(image, 0, 0, newSize.width, newSize.height);
			switch (_settings.imgFile.type) {
				case "image/jpeg":
					_settings.container.trigger("thumbnailGenerated", [{
						success: true,
						file: _settings.imgFile,
						type: "image/jpeg",
						image: canvas.toDataURL("image/jpeg")
					}]);
					break;

				case "image/png":
				case "image/svg+xml":
					_settings.container.trigger("thumbnailGenerated", [{
						success: true,
						file: _settings.imgFile,
						type: "image/png",
						image: canvas.toDataURL("image/png")
					}]);
					break;

				default:
					console.warn("Can't process: " + _settings.imgFile.type);
					_settings.container.trigger("thumbnailGenerated", [{
						success: false,
						file: _settings.imgFile
					}]);
			}
		}
		image.src = URL.createObjectURL(_settings.imgFile);
	};

	$.fn.imageThumbRotater = function (options) {
		var _settings = $.extend({
			imgFile: null,
			container: this
		}, options);


		var fileReader = new FileReader();
		fileReader.onloadend = function () {
			//var base64img = "data:" + file.type + ";base64," + _arrayBufferToBase64(fileReader.result);
			var scanner = new DataView(fileReader.result);
			var idx = 0;
			/*
			rotation ={
				1: 'rotate(0deg)',
				3: 'rotate(180deg)',
				6: 'rotate(90deg)',
				8: 'rotate(270deg)'
			};
			*/
			var value = 1; // Non-rotated is the default
			if (fileReader.result.length < 2 || scanner.getUint16(idx) != 0xFFD8) {
				// Not a JPEG
				_settings.container.imageThumbnailer({
					imgFile: _settings.imgFile,
					container: _settings.container,
					rotation: value
				});
				return;
			}
			idx += 2;
			var maxBytes = scanner.byteLength;
			while (idx < maxBytes - 2) {
				var uint16 = scanner.getUint16(idx);
				idx += 2;
				switch (uint16) {
					case 0xFFE1: // Start of EXIF
						var exifLength = scanner.getUint16(idx);
						maxBytes = exifLength - idx;
						idx += 2;
						break;
					case 0x0112: // Orientation tag
						// Read the value, its 6 bytes further out
						// See page 102 at the following URL
						// http://www.kodak.com/global/plugins/acrobat/en/service/digCam/exifStandard2.pdf
						value = scanner.getUint16(idx + 6, false);
						maxBytes = 0; // Stop scanning
						break;
				}
			}
			/*if (callback) {
				callback(base64img, value);
			}*/
			_settings.container.imageThumbnailer({
				imgFile: _settings.imgFile,
				container: _settings.container,
				rotation: value
			});
		}
		fileReader.readAsArrayBuffer(_settings.imgFile);
	}

}(jQuery));