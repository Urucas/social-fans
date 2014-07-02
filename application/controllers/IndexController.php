<?php

class IndexController extends Zend_Controller_Action
{
	public function init(){
		header("Content-type: text/html; charset=utf-8");
		$this->_helper->getHelper('Layout')->setLayout( 'front' );

 	}

  public function indexAction(){

	}
      
}
