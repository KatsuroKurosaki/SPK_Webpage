// Other functions, specific to the file.
function helpopReq(id){
		$.ajax({
		method: 'POST',
		url: 'api.php?s='+qs("s"),
		data: {
			op:'helpop',
			id:id
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status=="ok"){
				for(var item in data.messages){
					var options = {
						body: data.messages[item],
						icon: '../favicon.png',
					}
					n = new Notification("Recibido HelpOP",options);
					setTimeout(n.close.bind(n), 10000);
					delete n;
				}
				
				setTimeout(function(){helpopReq(data.id);},20000);
			} else {
				setTimeout(function(){helpopReq(id);},20000);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			//$(".console").html("Se ha perdido la comunicación con la consola. Revisa tu conexión a Internet.");
			setTimeout(function(){helpopReq(id);},20000);
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function webconsoleview(){
	$.ajax({
		method: 'POST',
		url: 'api.php?s='+qs("s"),
		data: {
			op:'consoleview',
			server:qs("server")
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status=="ok"){
				$(".console").html(data.consoleout);
				$("input[name='command']").focus();
			} else {
				$(".console").html("Error en el servidor al leer los datos de la consola");
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$(".console").html("Se ha perdido la comunicación con la consola. Revisa tu conexión a Internet.");
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
			setTimeout(function(){webconsoleview();},3000);
		}
	});
}

function webconsolecmd(){
	$.ajax({
		method: 'POST',
		url: 'api.php?s='+qs("s"),
		data: {
			op:'consolecmd',
			c:$("input[name='command']").val(),
			server:qs("server")
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			console.log(data);
			if(data.status=="ok"){
				$("input[name='command']").val("");
			} else {
				spawnTopAlert("Se ha producido un error en el servidor. Vuelve a intentarlo o contacta con el Admin.","warning");
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			spawnTopAlert("Se ha producido un error de comunicación. Revisa la conexión a Internet.","danger");
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}






function spawnCalendar(cal){
	//$(".datetimepicker").remove();
	
	$(cal).datetimepicker({
        format: "dd-mm-yyyy",
		autoclose:true,
		minView: 2,
		todayHighlight: true,
		language:'es',
		weekStart:1
    });
}
function spawnCalendars(cal1,cal2){
	//$(".datetimepicker").remove();
	
	var antes = new Date();
	antes.setFullYear(antes.getFullYear()-1);
	
	var despues = new Date();
	//despues.setFullYear(despues.getFullYear()+1);
	
	$(cal1+", "+cal2).datetimepicker({
        format: "dd-mm-yyyy",
		autoclose:true,
		minView: 2,
		todayHighlight: true,
		language:'es',
		weekStart:1,
		startDate:antes,
		endDate:despues
    });
}

function perfectAjaxQuery(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'hello'
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			console.log(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR.responseText);
		},
		complete: function(jqXHR, textStatus) {
			console.log(textStatus);
		}
	});
}

function passwordKeyDownEvent(e){
	keyCode = ('which' in e) ? e.which : e.keyCode;
	if (keyCode==13) alert("Intro pressed");
}

/*
<form id="imgInput"><input type="file" class="form-control" accept="image/*" onchange="javascript:imageProcess(event);"></form>

<div id="imagesDiv" class="form-control text-center" style="width:100%;height:175px;overflow-y:scroll;">
	<img src="data:image/jpeg;base64,[.........]" style="display:inline-block;width:125px;margin:.5em;">
</div>
*/
function imageProcess(e) {
	spawnSpinner();
	var width=800;
	var height=600;
	image = new Image();
	image.src = URL.createObjectURL(e.target.files[0]);
	image.onload = function() {
		newSize = calculateAspectRatioFit(image.width,image.height,width,height)
		canvas = document.createElement("canvas");
		canvas.width = newSize.width;
		canvas.height = newSize.height;
		context = canvas.getContext("2d");
		context.drawImage(image, 0,0,newSize.width,newSize.height);
		//context.scale(newSize.width, newSize.height);
		
		$("#imagesDiv").append('<img src="'+canvas.toDataURL("image/jpeg")+'"/>');
		
		delete canvas, context, image, newSize;
		$("#imgInput").replaceWith($("#imgInput").clone());
		removeSpinner();
	}
}

function calculateAspectRatioFit(srcWidth, srcHeight, maxWidth, maxHeight) {
	ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight);
	return { width: srcWidth*ratio, height: srcHeight*ratio };
}