function spawnSpinner(){
	if($("#spinner").length==0){
		var spinner = '<div id="spinner">';
			spinner += '<div class="cog">';
			spinner += '<div><i class="fa fa-2x fa-cog fa-spin"></i></div>';
			spinner += '<div>Cargando...</div>';
			spinner += '</div></div>';
		$(spinner).appendTo("body");
		$("body").addClass("cursor-wait");
	}
}
function removeSpinner(){
	$("#spinner").fadeOut("fast",function(){
		$(this).remove();
		$("body").removeClass("cursor-wait");
	});
}

function setData(id,data){
	if(typeof(Storage) !== "undefined") {
		localStorage.setItem(id,data);
	} else {
		Cookies.set(id,data);
	}
}
function getData(id){
	if(typeof(Storage) !== "undefined") {
		return localStorage.getItem(id);
	} else {
		return Cookies.get(id);
	}
}
function removeData(id){
	if(typeof(Storage) !== "undefined") {
		localStorage.removeItem(id);
	} else {
		Cookies.remove(id);
	}
}
function isNullData(id){
	if(typeof(Storage) !== "undefined") {
		return (localStorage.getItem(id) === null);
	} else {
		return (Cookies.get(id)==undefined);
	}
}

function spawnModal(title,body,btnlabel,preventclose){
	if($("#modal").length==0){
		if(btnlabel==undefined){btnlabel="Aceptar";}
		if(preventclose==undefined){preventclose=true;}
		var modal = '<div class="modal fade" id="modal" role="dialog" aria-labelledby="Modal Popup">'+
			'<div class="modal-dialog" role="document">'+
				'<div class="modal-content">'+
					'<div class="modal-header">'+
						'<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>'+
						'<h4 class="modal-title">'+title+'</h4>'+
					'</div>'+
					'<div class="modal-body">'+body+'</div>'+
					'<div class="modal-footer">'+
						'<button type="button" class="btn btn-default" data-dismiss="modal">'+btnlabel+'</button>'+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div>';
		$(modal).appendTo('body');
		if(preventclose){
			$('#modal').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		$('#modal').modal('show');
		$('#modal').on( 'hidden.bs.modal', function ( e ){
			$('#modal').remove();
			$('body').removeClass("modal-open");
		} );
	} else {
		console.error("Ya existe un modal popup.");
	}
}
function spawnConfirmModal(title,body,funcOk,btnOk,btnCanc,funcCanc,preventclose){
	if($("#modal").length==0){
		if(btnOk==undefined){btnOk="Aceptar";}
		if(btnCanc==undefined){btnCanc="Cancelar";}
		if(preventclose==undefined){preventclose=true;}
		var modal = '<div class="modal fade" id="modal" role="dialog" aria-labelledby="Modal Popup">'+
			'<div class="modal-dialog" role="document">'+
				'<div class="modal-content">'+
					'<div class="modal-header">'+
						'<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>'+
						'<h4 class="modal-title">'+title+'</h4>'+
					'</div>'+
					'<div class="modal-body">'+body+'</div>'+
					'<div class="modal-footer">'+
						'<button type="button" class="btn btn-warning" data-dismiss="modal" name="ko">'+btnCanc+'</button>'+
						'<button type="button" class="btn btn-success" data-dismiss="modal" name="ok">'+btnOk+'</button>'+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div>';
		
		$(modal).appendTo('body');
		if(preventclose){
			$('#modal').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		$('#modal').modal('show');
		$("#modal button[name='ok']").on("click",funcOk);
		$("#modal button[name='ko']").on("click",funcCanc);
		$('#modal').on( 'hidden.bs.modal', function ( e ){
			$('#modal').remove();
			$('body').removeClass("modal-open");
		} );
	} else {
		console.error("Ya existe un modal popup.");
	}
}
function spawnRemoteModal(url,data,preventclose){
	if($("#modal").length==0){
		if(preventclose==undefined){preventclose=true;}
		var modal = '<div class="modal fade" id="modal" role="dialog" aria-labelledby="Modal Popup">'+
			'<div class="modal-dialog" role="document">'+
				'<div class="modal-content"></div>'+
			'</div>'+
		'</div>';
		/*
		// Remaining code
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span>
			</button><h4 class="modal-title">a</h4>
		</div>
		<div class="modal-body"></div>
		<div class="modal-footer"></div>
		*/
		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			timeout: 10000,
			beforeSend: function() {
				spawnSpinner();
			},
			success: function (data) {
				$(modal).appendTo('body');
				$("#modal .modal-content").html(data);
				if(preventclose){
					$('#modal').modal({
						backdrop: 'static',
						keyboard: false
					});
				}
				$('#modal').modal('show');
				$('#modal').on( 'hidden.bs.modal', function ( e ){
					$('#modal').remove();
					$('body').removeClass("modal-open");
				} );
				$(".navbar-collapse").collapse('hide');
			},
			error: function(request, status, error) {
				console.log(request.responseText);
				spawnModal("Error de comunicación","Se ha producido un error de comuniación. Vuelva a intentarlo o contacte con el administrador.","Cerrar");
			},
			complete: function(jqXHR, textStatus) {
				removeSpinner();
			}
		});
	} else {
		console.error("Ya existe un modal popup.");
	}
}
function removeModal(){
	if($("#modal").length>=1){
		$('#modal').modal('hide');
	} else {
		console.error("No existe un modal.");
	}
}

function spawnAlert(text,cssclass,showBefore,timeout){
	if(timeout==undefined){timeout=7000;}
	var alertId = new Date().getTime();
	$('<div id="alert-'+alertId+'" class="alert alert-'+cssclass+' alert-dismissible fade in" style="display:none;" role="alert">'+text+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').insertBefore(showBefore);
	$('#alert-'+alertId).slideDown("slow",function(){ setTimeout(function(){ $('#alert-'+alertId).slideUp("slow",function(){ $('#alert-'+alertId).remove(); }); },timeout); });
}
function spawnTopAlert(text,cssclass,timeout){
	if(timeout==undefined){timeout=7000;}
	var alertId = new Date().getTime();
	$('<div id="alert-'+alertId+'" class="alert alert-'+cssclass+' alert-dismissible fade in alertclass" style="display:none;" role="alert">'+text+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').appendTo("body");
	$('#alert-'+alertId).slideDown("slow",function(){ setTimeout(function(){ $('#alert-'+alertId).slideUp("slow",function(){ $('#alert-'+alertId).remove(); }); },timeout); });
}

function qs(key) {
    key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx control chars
    var match = location.search.match(new RegExp("[?&]" + key + "=([^&]+)(&|$)"));
    return match && decodeURIComponent(match[1].replace(/\+/g, " "));
}

function uts2dt(ts) {
	if(ts!=undefined){
		var date = new Date(ts*1000);
		return date.getFullYear() + "/"
			+ (((date.getMonth()+1)<10)	?	"0"+(date.getMonth()+1)	:	(date.getMonth()+1)) + "/"
			+ ((date.getDate()<10)			?	"0"+date.getDate()			:	date.getDate()) + " "
			+ ((date.getHours()<10)			?	"0"+date.getHours()			:	date.getHours()) + ":"
			+ ((date.getMinutes()<10)		?	"0"+date.getMinutes()		:	date.getMinutes()) + ":"
			+ ((date.getSeconds()<10)		?	"0"+date.getSeconds()		:	date.getSeconds()) ;
	} else { return ""; }
}
function isValidDate(d,m,y) {
	var date = new Date(y,m-1,d);
	return ( date.getFullYear() == y && (date.getMonth() + 1) == m && date.getDate() == d );
}

function runNumber(container, from, to, decimalpos, duration){
	if(duration==undefined){duration=3000;}
	if(decimalpos==undefined){decimalpos=0;}
	$({someValue: from}).animate({someValue: to}, {
		duration: duration,
		step: function() {
			$(container).text( this.someValue.toFixed(decimalpos) );
		},
		complete: function() {
			$(container).text( this.someValue );
		}
	});
}

function letterTypingEffect(element,text,duration){
	if(duration==undefined){duration=500;}
	$(element).text("");
	setTimeout(function(){
		letterTypingEffectStep(0,element,text,duration);
	},duration);
}
function letterTypingEffectStep(letter,element,text,duration){
	$(element).text( $(element).text()+text[letter] );
	if(text[letter+1] != undefined){
		setTimeout(function(){
			letterTypingEffectStep(letter+1,element,text,duration);
		},duration);
	}
}

function spawnPrinter(elem, head) {
	if(elem!=undefined){
		var winprint = window.open('', 'Print', 'width=800,height=600');
		winprint.document.open();
		winprint.document.write('<html>');
		if(head==undefined){
			winprint.document.write('<head><title></title><style> * {font-family:sans-serif;} </style></head>');
		} else {
			winprint.document.write('<head>'+$(head).html()+'</head>');
		}
		winprint.document.write('<body>'+$(elem).html()+'</body>');
		winprint.document.write('</html>');
		winprint.document.close();
		winprint.focus();
		setTimeout(function(){
			winprint.print();
			winprint.close();
			return true;
		},500);
	} else {
		console.error("You haven't sent anything to print.");
	}
}

function json2html(json) {
	var i, ret = "";
	ret += "<ul>";
	for( i in json) {
		ret += "<li>"+i+": ";
		if( typeof json[i] === "object") ret += json2html(json[i]);
		else ret += json[i];
		ret += "</li>";
	}
	ret += "</ul>";
	return ret;
}