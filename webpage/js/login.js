function submitLogin() {
	$.api({
		data: $("form.form-signin").serializeForm(),
		success: function (data) {
			console.log(data);
		}
	});
}