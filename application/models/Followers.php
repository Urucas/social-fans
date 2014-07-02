<?php

class Model_Followers extends Zend_Db_Table {
    protected $_name	=	'twitter_followers';
    protected $_primary	=	'id';
    protected $_order	=	'user';
    protected $_limit	=	0;
    protected $_offset	=	5;
    
	public function getAll($twuser_id){
		$rs = $this->fetchAll($this->select()->where("twitter_user = ".(int)$twuser_id))->toArray();
		return($rs);
	}
	
}

?>
