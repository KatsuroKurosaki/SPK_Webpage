// Other functions, specific to the file.
function helpopReq(id) {
	$.ajax({
		method: 'POST',
		url: 'api.php?s=' + qs("s"),
		data: {
			op: 'helpop',
			id: id
		},
		timeout: 10000,
		beforeSend: function (jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if (data.status == "ok") {
				for (var item in data.messages) {
					var options = {
						body: data.messages[item],
						icon: '../favicon.png',
					}
					n = new Notification("Recibido HelpOP", options);
					setTimeout(n.close.bind(n), 10000);
					delete n;
				}

				setTimeout(function () {
					helpopReq(data.id);
				}, 20000);
			} else {
				setTimeout(function () {
					helpopReq(id);
				}, 20000);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			//$(".console").html("Se ha perdido la comunicación con la consola. Revisa tu conexión a Internet.");
			setTimeout(function () {
				helpopReq(id);
			}, 20000);
		},
		complete: function (jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function webconsoleview() {
	$.ajax({
		method: 'POST',
		url: 'api.php?s=' + qs("s"),
		data: {
			op: 'consoleview',
			server: qs("server")
		},
		timeout: 10000,
		beforeSend: function (jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if (data.status == "ok") {
				$(".console").html(data.consoleout);
				$("input[name='command']").focus();
			} else {
				$(".console").html("Error en el servidor al leer los datos de la consola");
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$(".console").html("Se ha perdido la comunicación con la consola. Revisa tu conexión a Internet.");
		},
		complete: function (jqXHR, textStatus) {
			//console.log(textStatus);
			setTimeout(function () {
				webconsoleview();
			}, 3000);
		}
	});
}

function webconsolecmd() {
	$.ajax({
		method: 'POST',
		url: 'api.php?s=' + qs("s"),
		data: {
			op: 'consolecmd',
			c: $("input[name='command']").val(),
			server: qs("server")
		},
		timeout: 10000,
		beforeSend: function (jqXHR, settings) {
			console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			console.log(data);
			if (data.status == "ok") {
				$("input[name='command']").val("");
			} else {
				spawnTopAlert("Se ha producido un error en el servidor. Vuelve a intentarlo o contacta con el Admin.", "warning");
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			spawnTopAlert("Se ha producido un error de comunicación. Revisa la conexión a Internet.", "danger");
		},
		complete: function (jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}