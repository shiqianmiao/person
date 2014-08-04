<?php
class Test {
	public function __construct($params) {
		echo 'welcome';
	}

   public function getPa($params) {
       return array();
   }

   public function getDo() {
        $params = array(1,2,3,4);
        foreach ($params as $k => $v) {
            echo $v;
        }
   }
}
