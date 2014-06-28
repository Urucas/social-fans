<?php

class ErrorController extends Zend_Controller_Action {

	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		$controller = $errors->request->getControllerName();

		$error		=	$errors->exception->getMessage();
		$error		=	$errors->exception;

		header("Content-type: text/html; charset=utf-8");
		$this->_helper->getHelper('Layout')->setLayout( 'front' );

		//print_r( $errors );
		//  die("<br> Error"); 
	}
}

