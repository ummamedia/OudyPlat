<?php

namespace OudyPlat;

class ConstantEntity extends \OudyPlat\Object {
    public function __construct($data = null, $allowedProperties = null, $forceAll = false) {
        if($data)
            switch(gettype($data)) {
                case 'array':
                case 'object':
                    parent::__construct($data, $allowedProperties, $forceAll);
                    break;
                default:
                    return $this->load($data);
                    break;
            }
        $class = get_class($this);
        if(defined($class.'::types')) {
            $types = explode(';', $class::types);
            foreach($types as $type) {
                list($key, $value) = explode(':', $type);
                $this->$key = new $value($this->$key);
            }
        }
    }
    public function load($key) {
        $class = get_class($this);
        if($class::$data)
            if(array_key_exists($key, $class::$data))
                $this->__construct($class::$data[$key]);
    }
}