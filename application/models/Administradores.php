<?php

class Model_Administradores extends Zend_Db_Table {
    protected $_name	=	'admins';
    protected $_primary	=	'id';
    protected $_order	=	'user';
    protected $_limit	=	0;
    protected $_offset	=	5;
    
    public function validaUser( $user, $pass ){

    	$rs	=	$this->fetchAll( $this->select()->where( "user = ? ", $user )->where( 'pass = ? ', md5( $pass ) ) );
    	return ( !is_null( $rs ) )	?	$rs->toArray()	:	null;
    	
    }
    
	public function getAll(){
		$rs = $this->fetchAll($this->select())->toArray();
		return($rs);
	}
	
	
}

?>
