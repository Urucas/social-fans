var backend = backend || {}

backend.usuarios = {

	guardar: function() {
		$("#form_user").ajaxForm({
			success:function(rta){
				console.log(rta);
				rta = JSON.parse(rta);
				if(rta.error) {
					alert(rta.error);
					return;
				}
				alert(rta.msg);
				document.location.reload();
			}
		}).submit();
	},

	nuevo: function() {
	
		$('#form_user')[0].reset();
		$("#user_id").val(0);
		$("#editor_user").show();

	},

	editUser: function(uid) {
		$('#form_user')[0].reset();
		if(!uid) return;
		$.post("/backend/getusuario/",{"uid":uid}, function(response){
			if(response.error) {
				alert(response.error);
				return;
			}
			console.log(response.usuario);
			var u = response.usuario;
			$("#user_id").val(u.id);
			$("#user_nombre").val(u.nombre);
			$("#user_email").val(u.email);
			$("#user_telefono").val(u.tel);
		
			$("#editor_user").show();
		}, 'json');
	},

   	cargarContenido: function(pa) {

		$.post("/backend/getusuarios", {pa:pa}, function(response){
			if(response.error) { alert(response.error); return; }
			var html = "";
			$("#list_usuarios").html(response.list);
			var htmlp='<a href="#" class="paginador-1" style="cursor:pointer;" onclick="backend.usuarios.cargarContenido(1)">« Primero</a>';
			for(var p=0;p<response.total_pages;p++){
				if(pa==(p+1)){
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.usuarios.cargarContenido('+(p+1)+')"><b>'+(p+1)+'</b></a>';
				}else{
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.usuarios.cargarContenido('+(p+1)+')">'+(p+1)+'</a>';
				}
			}
			htmlp+='<a  href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.usuarios.cargarContenido('+response.total_pages+')">Ultimo »</a>';
			$("#pagination").html(htmlp);
		}, "json");
	}
}

$(document).ready(function(){
	backend.usuarios.cargarContenido();
	$( "#evento_fecha" ).datepicker();
	$( "#evento_fecha_limite" ).datepicker();
});

