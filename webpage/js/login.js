// Other functions, specific to the file.
$(document).on("ready",function(){
	if(isNullData("SpkLogin")){
		setData("SpkLogin","");
		$("#inputName").focus();
	} else {
		$("#inputName").val( getData("SpkLogin") );
		$("#inputPassword").focus();
	}
});

function usernameKeyUp(e){
	setData("SpkLogin",e.target.value);
}

function passwordKeyDownEvent(e){
	keyCode = ('which' in e) ? e.which : e.keyCode;
	if (keyCode==13) login();
}


function login(){
	if($.trim($("#inputName").val())!=""&&$.trim($("#inputPassword").val())!=""){
		$.ajax({
			method: 'POST',
			url: 'api.php',
			data: {
				op:'weblogin',
				u:$("#inputName").val(),
				p:$("#inputPassword").val()
			},
			timeout: 10000,
			beforeSend: function(jqXHR, settings) {
				console.log(settings);
				spawnSpinner();
			},
			success: function (data, textStatus, jqXHR) {
				console.log(data);
				if(data.status=="ok"){
					window.location = 'index.php?s='+data.session;
				} else {
					spawnTopAlert(data.reason,"danger");
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.responseText);	
			},
			complete: function(jqXHR, textStatus) {
				console.log(textStatus);
				removeSpinner();
			}
		});
	} else {
		spawnTopAlert("El nombre de Minecraft y la contraseña no pueden quedar en blanco.","warning")
	}
}

function recoverPass(){
	spawnModal("Recuperar contraseña","Lorem Ipsum","Cerrar")
}