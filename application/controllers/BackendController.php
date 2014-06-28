<?php

class BackendController extends Zend_Controller_Action
{
    public function init(){
		
		$this->_helper->getHelper('Layout')->setLayout( 'back' );
		
		$this->auth = new Zend_Session_Namespace('cleeck_admin');
		$this->view->assign("id",$this->auth->id);
		$this->view->assign("user",$this->auth->user);

    }

    //--> Secciones
    public function indexAction(){
        $this->checkSession();
	}

	public function adminpAction() {
		$this->auth = new Zend_Session_Namespace('cleeck_admin');
		$msg="";
		$this->view->seccion = "home";	

		if(!$this->_request->isPost()) return;
		if(trim($this->_request->getPost('admin_pass1')) =="" || trim($this->_request->getPost('admin_pass2'))=="") return;
		if(trim($this->_request->getPost('admin_pass1')) != trim($this->_request->getPost('admin_pass2'))) $msg="¡Las clave debe ser igual en los 2 campos!";
		$clave=trim($this->_request->getPost('admin_pass1'));
// ------ Comprobar clave, comentar los campos que no se utilicen.
		if(strlen($clave) < 6){
	      $msg = "¡La clave debe tener al menos 6 caracteres!";
	    }
	
	  	// if(strlen($clave) > 16){
	   //    $msg = "¡La clave no puede tener más de 16 caracteres!";
	   //  }
	   //  if (!preg_match('`[a-z]`',$clave)){
	   //    $msg = "¡La clave debe tener al menos una letra minúscula!";
	   //  }
	   //  if (!preg_match('`[A-Z]`',$clave)){
	   //    $msg = "¡La clave debe tener al menos una letra mayúscula!";
	   //  }
	   //  if (!preg_match('`[0-9]`',$clave)){
	   //    $msg = "¡La clave debe tener al menos un caracter numérico!";
	   //  }

	    if($msg==""){
			if((int)$this->_request->getPost('admin_id')!=$this->auth->id){
				return;
			}
			$ida=$this->auth->id;
			$modela = new Model_Administradores();
			$affected = $modela->update(array(
				"pass"=>md5($clave)),
				"id = ".$ida);
			$msg="¡Se cambio la contraseña con exito!";

			if(!$affected) {
				$msg="¡Ha ocurrido un error al guardar los datos. Intente nuevamente!";
			}
	    }

		$this->view->assign("msg",$msg);
	}

	public function inicioAction() {
		$this->checkSession();

		$this->view->headScript()->prependFile('/js/backend/backend.inicio.js', $type = 'text/javascript' );
		$this->view->seccion = "home";	
	}

	public function novedadesAction() {
		$this->checkSession();

		$this->view->headScript()->prependFile('/js/backend/backend.novedades.js', $type = 'text/javascript' );
		$this->view->seccion = "novedades";	

	}
	public function newsletterAction() {
		$this->checkSession();
	
		$this->view->headScript()->prependFile('/js/backend/backend.newsletter.js', $type = 'text/javascript' );
		$this->view->seccion = "newsletter";	

	}

	//--> End Secciones
	
	//-->Slides
	public function getcontenidoslidesAction() {
		$this->checkSessionAjax();

		$models = new Model_Slides();
		$data_slides = $models->getAll();

		die(json_encode(array("slides"=>$data_slides)));
	}

	public function guardarcontenidoslideAction() {
		$this->checkSessionAjax();
		$models = new Model_Slides();
		$id=trim($this->_request->getPost("id_tab"));
		$imagen_url="";
		if($_FILES){
	                foreach($_FILES as $k=>$v){
	                    $name = $v['name'];
	                    $size = $v['size'];
	                    list($txt, $ext) = explode(".", $name);
	                    $actual_image_name = "slide_".$id.".jpg";
	                    $tmp = $v['tmp_name'];
	                    if (move_uploaded_file($tmp, "uploadsfotos/slides/".$actual_image_name))
	                            {
	                                @chmod("./uploadsfotos/slides/".$actual_image_name, 0705);
	                                $imagen_url = "/uploadsfotos/slides/".$actual_image_name;
	                            }else{
			                     		die(json_encode(array("msg"=>"¡No se pudo subir la archivo!")));
			                     }
	                     }
	                }
	    $activado = (trim($this->_request->getPost("option".$id))=="")?"desactivado":trim($this->_request->getPost("option".$id));


		$data = array(
			"titulo_slide" =>trim($this->_request->getPost("tab".$id."_tit")),
			"titulo_slide2" =>trim($this->_request->getPost("tab".$id."_tit2")),
			"activar" => $activado,
			"desc_slide"  => trim($this->_request->getPost("tab".$id."_desc"))

			);
			
		if($imagen_url!=""){
			$data["url_img"] = $imagen_url;
		}else{
			$imagen_url="/uploadsfotos/slides/predeterminada.jpg";
			$data["url_img"] = $imagen_url;
		}
	    	
	    $models->update($data,"id = ".$id);

		die(json_encode(array("msg"=>"Los datos se han guardado con exito!")));
		
	}

	//--> End Slides

	//--> Novedades
	public function getnovedadesAction() {
		$this->checkSessionAjax();
		if(!$this->_request->isPost()) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}

		$page = (int) $this->_request->getPost("pa");

		$modeln = new Model_Novedades();
		$data_novedades = $modeln->getAll($page, 15);

		$total_resultados = $modeln->getFoundRows();
		$total_pages = (int)ceil($total_resultados / 15);

		die(json_encode(array(
			"novedades"=>$data_novedades,
			"total_resultados" => $total_resultados,
			"total_pages" => $total_pages,
			"current_page" => $page
		)));
	}

	public function getcontenidonovedadAction() {
		$this->checkSessionAjax();

		if(!$this->_request->isPost()) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}
		$nid = (int) $this->_request->getPost("nid");

		$modeln = new Model_Novedades();
		$data_seccion = $modeln->getNovedadById($nid);
		$data_seccion["contenido"] = stripslashes($data_seccion["contenido"]);

		$imgs_novedad = $modeln->ImgGetAll((int)$this->_request->getPost("nid"));

		die(json_encode(array("novedad"=>$data_seccion, "imagenes"=>$imgs_novedad)));
	}

	public function guardarnovedadAction() {
		$this->checkSession();

		if(!$this->_request->isPost()) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}

		$modeln = new Model_Novedades();
		$id = (int) $this->_request->getPost("nov_id");

		$novedad = array(
			"titulo" => trim($this->_request->getPost("nov_tit")),
			"fecha" => trim($this->_request->getPost("nov_fec")),
			"contenido" => trim($this->_request->getPost("nov_con")),
		);
		
		// var_dump($novedad);

		if($novedad["titulo"] == "") {
			die(Zend_Json::encode(array("error"=>"El campo Titulo no puede quedar vacio.")));
		}
		if($novedad["fecha"] == "") {
			die(Zend_Json::encode(array("error"=>"El campo Fecha no puede quedar vacio.")));
		}
		if($novedad["contenido"] == "") {
			die(Zend_Json::encode(array("error"=>"El campo Contenido no puede quedar vacio.")));
		}

		if($id) {
			$modeln->update($novedad,"id = ".$id);
		}else {
			$id = $modeln->insert($novedad);
		}

		if($_FILES){
                foreach($_FILES as $k=>$v){
                    $name = $v['name'];
                    $size = $v['size'];
                    list($txt, $ext) = explode(".", $name);
                    $actual_image_name = date("dmY-Gis")."-".$k.".jpg"; 
					// die(Zend_Json::encode(array("msg"=>"que".$actual_image_name)));  
					$tmp = $v['tmp_name'];
		            if (!file_exists("./uploadsfotos/novedades/".$id."_novedad/")) {
                    	mkdir("./uploadsfotos/novedades/".$id."_novedad/",0777);
                    	chmod("./uploadsfotos/novedades/".$id."_novedad/",0777);
                	}
                    if (move_uploaded_file($tmp, "uploadsfotos/novedades/".$id."_novedad/".$actual_image_name))
                            {
                                chmod("./uploadsfotos/novedades/".$id."_novedad/".$actual_image_name, 0705);
                                $imagen_url = "/uploadsfotos/novedades/".$id."_novedad/".$actual_image_name;
                           
								$modeln->insertImg($id, $imagen_url);                      
						    }
                     }
                }            
		



		die(Zend_Json::encode(array("msg"=>"Los datos se han actualizado con exito!", "did"=>$id)));
	}

	public function eliminarnovedadAction(){
		$this->checkSessionAjax();

		$id = (int) $this->_request->getPost("id");
		if(!$id) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}
		$modeln = new Model_Novedades();
		$modeln->delete("id = ".$id);

		$path="uploadsfotos/novedades/".$id."_novedad/";

		foreach(glob($path . "/*") as $archivos_carpeta)
			{
			 	@unlink($archivos_carpeta);
			}			
			 
		@rmdir($path);
		
		$modeln->dellAllImgByNov($id);
		die(Zend_Json::encode(array("msg"=>"¡Se ha borrado con exito!")));
	}

	//--> End Novedades

	//--> Newsletter
	public function getnewsletterAction() {
		$this->checkSessionAjax();
		if(!$this->_request->isPost()) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}

		$page = (int) $this->_request->getPost("pa");

		$modeln = new Model_Newsletter();
		$data_newsletter = $modeln->getAll($page, 5);

		$total_resultados = $modeln->getFoundRows();
		$total_pages = (int)ceil($total_resultados / 5);

		die(json_encode(array(
			"newsletters"=>$data_newsletter,
			"total_resultados" => $total_resultados,
			"total_pages" => $total_pages,
			"current_page" => $page
		)));
	}

	public function eliminarnewsletterAction(){
		$this->checkSessionAjax();

		$id = (int) $this->_request->getPost("id");
		if(!$id) {
			die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al guardar los datos. Intente nuevamente!")));
		}
		$modeln = new Model_Newsletter();
		$modeln->delete("id = ".$id);
	
		die(Zend_Json::encode(array("msg"=>"¡Se ha borrado con exito!")));
	}

	public function getnewslettersxlsAction() {
	
		$this->checkSessionAjax();

		$xlsPath = "./newsletter/usuarios-newsletters-".date("Y-m-d").".xlsx";

		$model = new Model_Newsletter();
		$usuarios = $model->getAll();

		require_once("../library/PHPExcel-1.7.9/PHPExcel.php");
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("@urucas | Mobile, Web & Games development!");
		$objPHPExcel->getProperties()->setTitle("Colabianchi - Usuarios newsletter");
		$objPHPExcel->getProperties()->setSubject("Colabianchi - Ususarios Newsletter");
		$objPHPExcel->getProperties()->setDescription(
			"Usuarios inscriptos al newsletter de Colabianchi desde el Sitio Web"
		);
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getSecurity()->setLockWindows(false);
		$objPHPExcel->getSecurity()->setLockStructure(false);

		$objPHPExcel->getActiveSheet()->SetCellValue("B2", "Email");
		$objPHPExcel->getActiveSheet()->getStyle("B2")->getFill()->getStartColor()->setARGB('B7B7B7');

		$i = "3";
		foreach($usuarios as $usuario) {
			$objPHPExcel->getActiveSheet()->SetCellValue("B".$i, $usuario["email"]);
			$i++;
		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($xlsPath);

		$xlsPath = "http://".$_SERVER['HTTP_HOST'].preg_replace("/^\.\//","/",$xlsPath);
		die(json_encode(array("xls"=>$xlsPath)));
	}


	//--> End Newsletter

	public function loginAction(){
	
        $this->_helper->layout->disableLayout();
		if(!$this->_request->isPost()) return;	
		
		$user = trim($this->_request->getPost('user'));
		$pass = trim($this->_request->getPost('pass'));
		$this->model = new Model_Administradores();
		$datos = $this->model->validaUser($user,$pass);

		if(!sizeof($datos)){
			$this->view->assign("error","1");
			$this->view->assign("message","Usuario o contraseña incorrectos");
			return;
		}

		$sesion = new Zend_Session_Namespace("cleeck_admin");
		$sesion->id = $datos[0]["id"];
		$sesion->user 	= $datos[0]["user"];
		$sesion->nombre = $datos[0]["nombre"];
		$this->_redirect("/backend/");
	}

	public function logoutAction(){
		Zend_Session::destroy();
		$this->_redirect("/backend/login");
	}

    private function checkSession() {

		$sesion = new Zend_Session_Namespace("cleeck_admin");
		if($sesion->id) {
			return;
			$session;
		}
		$this->_redirect("/backend/login");
	}

	public function checkSessionAjax() {

		$sesion = new Zend_Session_Namespace("cleeck_admin");
		if($sesion->id) {
			return;
			$session;
		}
		die(json_encode(array("error"=>"Se ha perdido la sesion de usuario, debe loguearse nuevamente")));

	}

    
}
