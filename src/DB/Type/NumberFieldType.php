<?php

namespace DB\Type;    

class NumberFieldType extends LookUpFieldType {
	public function __construct(&$params, $entity = null) {
		parent::__construct($params, $entity);
	}
	
	public function getGroupInput() {
		return $this->getInput('', $this->getName().$this->dbId, 'span1');
	}
	
	public function getSQLValue($name='') {
		return intval(preg_replace('/\s+/', '', preg_replace('/\,/', '.', $this->getValue($name))));
	}
}