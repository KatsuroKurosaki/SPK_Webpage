// Other functions, specific to the file.

function login() {
	if ($.trim($("#username").val()) != "" && $.trim($("#password").val()) != "" && $.trim($("#secret").val()) != "") {
		$.ajax({
			method: 'POST',
			url: 'api.php',
			data: {
				op: 'login',
				u: $("#username").val(),
				p: $("#password").val(),
				c: $("#secret").val()
			},
			timeout: 10000,
			beforeSend: function (jqXHR, settings) {
				console.log(settings);
				spawnSpinner();
			},
			success: function (data, textStatus, jqXHR) {
				console.log(data);
				if (data.status == "ok") {
					window.location = 'index.php?s=' + data.session + "&welcome=true";
				} else {
					spawnTopAlert(data.reason, "danger");
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.responseText);
				console.log(JSON.stringify(jqXHR));
			},
			complete: function (jqXHR, textStatus) {
				console.log(textStatus);
				removeSpinner();
			}
		});
	} else {
		spawnTopAlert("No pueden quedar campos en blanco en el inicio de sesión, por favor, rellena todos los campos.", "warning")
	}
}