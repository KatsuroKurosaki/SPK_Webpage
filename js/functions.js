// Other functions, specific to the file.
$(document).on("ready",function(){
	if(isNullData("cookies-accepted")){
		setData("cookies-accepted","no");
	}
	checkCookie();
});

function checkCookie(){
	if(getData("cookies-accepted")=="no"){
		$("#cookies").slideDown();
	}
}

function acceptCookie(){
	setData("cookies-accepted","yes");
	$("#cookies").slideUp();
}

function getOnlineUsers(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'onlineusers',
			srv:1
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			var datohtml="";
			if(data.status == "ok"){
				$("#onlineNum").text("( "+data.info.Players+' / '+data.info.MaxPlayers+" )");
				datohtml += '<table class="table table-striped table-hover table-condensed">';
					for ( var item in data.players ){
						datohtml += '<tr><td class="small">'+data.players[item]+'</td></tr>';
					}
				datohtml += '</table>';
			} else {
				datohtml += '<div style="padding:1em;">¿Sabes cuándo pides una cita y te rechazan? Esto me ha pasado ahora al preguntar quién está online... <i class="fa fa-frown-o fa-lg"></i></div>';
			}
			$("#onlineUsers").html(datohtml);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$("#onlineUsers").html('<div>No se quién está online, he sido rechazado en el amor... <i class="fa fa-frown-o fa-lg"></i></div>');
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function getLastRanks(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'lastranks'
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status == "ok"){
				$("#lastRanks").html("");
				datohtml="";
				for ( var item in data.data ){
					datohtml += '<tr class="small"><td>'+data.data[item].playername+'</td><td>'+data.data[item].rank+'</td><td>'+moment(data.data[item].tsreturn*1000).locale("es").fromNow()+'</td></tr>';
				}
				$("#lastRanks").append(datohtml);
				delete datohtml;
			} else {
				$("#lastRanks").append('<tr class="small"><td>&nbsp;</td><td>ERROR</td><td>&nbsp;</td></tr>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$("#lastRanks").append('<tr class="small"><td>&nbsp;</td><td>ERROR</td><td>&nbsp;</td></tr>');
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function getTopOnline(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'toponline'
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status == "ok"){
				$("#topOnline").html("");
				datohtml="";
				for ( var item in data.data ){
					//console.log( data.players[item] );
					datohtml += '<tr class="small"><td>'+item+'</td><td>'+data.data[item].name+'</td><td>'+data.data[item].timeon+'</td></tr>';
				}
				$("#topOnline").append(datohtml);
				delete datohtml;
			} else {
				$("#topOnline").append('<tr class="small"><td>&nbsp;</td><td>ERROR</td><td>&nbsp;</td></tr>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$("#topOnline").append('<tr class="small"><td>&nbsp;</td><td>ERROR</td><td>&nbsp;</td></tr>');
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function getGraphHistPlayers(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'onlinedatagraph',
			srv:1
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status == "ok"){
				$('#activelast').highcharts({
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					chart:{
						backgroundColor: null
					},
					xAxis: {
						labels: {
							enabled: false
						}
					},
					yAxis: {
						title: {
							text: ''
						},
						min:0
					},
					tooltip: {
						formatter: function() {
							return '<b>' + this.y + '</b> '+ this.series.name;
						}
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Jugadores',
						data: data.onlinegraph
					}]
				});
				$("#activeinfo").text('Máximo: '+data.maxgraph+' / Mínimo: '+data.mingraph);
			} else {
				$("#activelast").html('<div style="padding:1em;">No he conseguido dilatar el espacio-tiempo para ver el historico de jugadores online... <i class="fa fa-frown-o fa-lg"></i></div>');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$("#activelast").html('<div>No he encontrado la maquina del tiempo en ningún lugar... <i class="fa fa-frown-o fa-lg"></i></div>');
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function getGraphMinecraftVersions(){
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'minecraftversions'
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function (data, textStatus, jqXHR) {
			
			graphdata = [];
			
			for(var item in data.mcversions){
				
				graphdata.push({name:item, y:data.mcversions[item]});
			}
			
			$('#minever').highcharts({
				credits: {
					enabled: false
				},
				chart: {
					type: 'pie',
					backgroundColor:null
				},
				title: {
					text: ''
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %'
						}
					}
				},
				series: [{
					name: 'Porcentaje',
					data: graphdata
				}]
			});
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$("#activelast").html('<div>No he encontrado la maquina del tiempo en ningún lugar... <i class="fa fa-frown-o fa-lg"></i></div>');
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}

function checkConnectivity(mode,div){
	htmloff = '<span style="color:red;"><i class="fa fa-exclamation-triangle fa-lg"></i> Offline</span>';
	htmlman = '<span style="color:orange;"><i class="fa fa-cogs fa-lg"></i> Mantenimiento</span>';
	htmlon = '<span style="color:green;"><i class="fa fa fa-check fa-lg"></i> Online</span>';
	$.ajax({
		method: 'POST',
		url: 'api.php',
		data: {
			op:'checkconnectivity',
			mode:mode
		},
		timeout: 10000,
		beforeSend: function(jqXHR, settings) {
			//console.log(settings);
		},
		success: function(data, textStatus, jqXHR) {
			//console.log(data);
			if(data.status=="ok"){
				$(div).html(htmlon);
			} else if(data.status=="no") {
				$(div).html(htmlman);
			} else {
				$(div).html(htmloff);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR.responseText);
			$(div).html(htmloff);
		},
		complete: function(jqXHR, textStatus) {
			//console.log(textStatus);
		}
	});
}