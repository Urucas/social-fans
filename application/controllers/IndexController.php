<?php

class IndexController extends Zend_Controller_Action
{
	public function init(){
		header("Content-type: text/html; charset=utf-8");
		$this->_helper->getHelper('Layout')->setLayout( 'front' );
		$this->view->modulo = "inicio";

    $modeln = new Model_Novedades();
    $data_novedades = $modeln->getAll(1, 10,1);
    $this->view->assign("novedadesnew",$data_novedades);

 	}

  //--> Secciones
  public function indexAction(){
     $this->view->headScript()->prependFile('/js/frontend/frontend.misc.js', $type = 'text/javascript' );

     $models = new Model_Slides();
     $data_slides = $models->getAll();
     $this->view->assign("slides",$data_slides);

    	$this->view->modulo = "inicio";

	}

  public function novedadesAction(){

		$this->view->modulo = "inicio";
    $modeln = new Model_Novedades();

    if(!$this->_request->getParam("id")){
      $data_novedad = $modeln->getNew();
      $img_novedad = $modeln->ImgGetAll($data_novedad[0]["id"]);

    }else{
      $id  = (int) $this->_request->getParam("id");
      $data_novedad = $modeln->getNovedad($id);
      $img_novedad = $modeln->ImgGetAll($id);

    }

   
    $data_novedades = $modeln->getAll(1, 10,1);
    

    if($data_novedad){
      $this->view->assign("novedad",$data_novedad);
      $this->view->assign("nov_img",$img_novedad);
      $this->view->assign("novedadesnew",$data_novedades);
    }
    else{
      $this->_redirect('/index');
    }
  }

  public function staffifaAction(){
		$this->view->modulo = "instituto";
    }

  public function serviciosAction(){
		$this->view->modulo = "servicios";
    }

  public function fertilidadAction(){
		$this->view->modulo = "fertilidad";
    }

  public function avantialabAction(){
		$this->view->modulo = "avantialab";
      }

  public function contactoAction(){
		$this->view->modulo = "contacto";
    $this->view->headScript()->prependFile('/js/frontend/frontend.misc.js', $type = 'text/javascript' );
    }
  //--> Contacto

	public function infografiaAction(){
		$this->_helper->layout->disableLayout();
	}

	public function vinculosAction(){
		$this->view->modulo = 'instituto';
	}

	public function ginecoAction() {
			$this->view->modulo = "servicios";
  
	}


	public function andrologiaAction(){
			$this->view->modulo = "servicios";
  
	}

	public function endocrinologiaAction(){
			$this->view->modulo = "servicios";
 	}
	
	public function pediatriaAction(){
			$this->view->modulo = "servicios";
 
	}

  public function enviarcontenidocontactoAction(){

    $data = array(
      "nomyape" => trim($this->_request->getPost("nomyape")),
      "tel" => trim($this->_request->getPost("tel")),
      "mail"  => trim($this->_request->getPost("email")),
      "comentario"  => trim($this->_request->getPost("comentario"))
      
    );
    
    // var_dump($data);
    if($data["nomyape"] == "" || strtolower($data["nomyape"]) == "Su nombre...") {
      die(Zend_Json::encode(array("error"=>"El campo Nombre y Apellido no puede quedar vacio")));
    }

    if($data["tel"]=="" || strtolower($data["tel"]) == "Su teléfono...") {
      die(Zend_Json::encode(array("error"=>"El campo Teléfono no puede quedar vacio")));
    }

    $validate = new Zend_Validate_EmailAddress();
    if(!$validate->isValid($this->_request->getPost('email'))) {
      die(Zend_Json::encode(array("error"=>"El campo Email no es un email valido")));
    }

    if($data["comentario"] == "" || strtolower($data["comentario"]) == "Su mensaje...") {
      die(Zend_Json::encode(array("error"=>"El campo Consulta no puede quedar vacio")));
    }

    
    // 
      $mailContent = '<html>
        <head>
        <title>Consulta</title>
        </head>
        <body>
        <div style="width:700px;height:400px;background:#E6E6E6">
        <div style="width:500px;height:220px;background-color:white;border-radius:15px;margin-left:90px;padding:20px;font-family:\'Arial\';font-size:10pt;">
        ';

      $mailContent.= '<p>Nombre: '.utf8_decode($data["nomyape"]).'</p>';
      $mailContent.= '<p>Tel: '.$data["tel"].'</p>';
      $mailContent.= '<p>Email: '.utf8_decode($data["mail"]).'</p>';
      $mailContent.= '<p>Consulta: '.utf8_decode($data["comentario"]).'</p>';

      $mailContent.='<p>VIA WEB</p>
        </div>
        </div>
        </body>
        </html>';

    try {
      $mail = new Zend_Mail();
      $mail->setBodyHtml($mailContent);
      $mail->setFrom($data["mail"]);//mail de la persona que lo envia.
      // var_dump($email[0]["val_contacto"]);
      // $mail->addTo("info@yedro.com.ar");
      $mail->addTo("matias@urucas.com");
      $mail->setSubject('Contacto WEB');
      $mail->send();

    }catch(Exception $e) {
    // var_dump($e);
      };


    die(json_encode(array("msg"=>"El formulario se ha enviado con exito! Le responderemos a la brevedad!")));
    
  }
  //--> END Contacto
  //--> Newsletter
  public function addnewsAction(){

      if(!$this->_request->isPost()) {
        die(Zend_Json::encode(array("error"=>"¡Ha ocurrido un error al suscribirse. Intente nuevamente!")));
      }
      $email = trim($this->_request->getPost("email"));
    
      $validate = new Zend_Validate_EmailAddress();
      if(!$validate->isValid($email)) {
        die(json_encode(array("error"=>"El email no es valido")));
      }

      $modeln = new Model_Newsletter();
    
      $modeln->insert(array(
          "email"=>$email
        ));
      
      die(json_encode(array("msg"=>"Gracias por suscribirte!")));
    }
      
}
