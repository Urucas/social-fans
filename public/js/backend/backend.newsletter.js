var backend = backend || {}

backend.newsletter = {


	dellNw: function(nid) {
		if(nid == undefined) return;

		if(confirm("¿Esta seguro que desea eliminar el Email?")) {
			$.post("/backend/eliminarnewsletter/",{"id":nid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);
				document.location.reload();
			}, 'json');
		}
	},

	xlsDownload: function() {
		$.post("/backend/getnewslettersxls", {}, function(response){
			if(response.error) { alert(response.error); return; }
	//		alert(response.xls);
			document.location.href = response.xls;
		},'json');
	},
	
	cargarContenido: function(pa) {
		$.post("/backend/getnewsletter", {pa:pa}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.newsletters == false) return;
			// alert(response.total_pages);
			var html = "";
			for(var i=0;i<response.newsletters.length;i++){
						html+= "<tr draggable='true' id='newsletter_"+response.newsletters[i].id+"'><td>"+response.newsletters[i].id+"</td>";
                        html+= "<td>"+response.newsletters[i].email+"</td>";
                        html+= '<td><a href="#" onclick="backend.newsletter.dellNw('+response.newsletters[i].id+')">Eliminar</a></td></tr>';
					}
			var htmlp='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.newsletter.cargarContenido(1)">« Primero</a>';
			for(var p=0;p<response.total_pages;p++){
				if(pa==(p+1)){
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.newsletter.cargarContenido('+(p+1)+')"><b>'+(p+1)+'</b></a>';
				}else{
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.newsletter.cargarContenido('+(p+1)+')">'+(p+1)+'</a>';
				}
			}
			htmlp+='<a  href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.newsletter.cargarContenido('+response.total_pages+')">Ultimo »</a>'
			$("#lst_nw").html(html);
			$("#pagination").html(htmlp);

		}, "json");
	}
}

$(document).ready(function(){
		backend.newsletter.cargarContenido();
});

