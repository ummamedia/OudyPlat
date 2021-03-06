<?php

namespace OudyPlat;

/**
 * 
 *
 * @author Ayoub Oudmane <ayoub at oudmane.me>
 */
class Object {
    
    /**
	 * Initialate the object
	 * @param array|object $data Data to assing to this Object
	 * @param array|object|string $allowedProperties Filter properties in $data
	 * @param bool $forceAll Force all properties
	 */
    public function __construct($data = null, $allowedProperties = null, $forceAll = false) {
        
        // stop if there's nothing to assing to this object
		if(empty($data)) return;
        
		// convert $data to array if it's an object
		if(gettype($data) == 'array') $data = (object) $data; 
        
		if($allowedProperties) {
            
			// convert the allowed types to arrays if it's a string
			if(gettype($allowedProperties) == 'string') {
                
				// $allowedProperties = explode(',', $allowedProperties);
				preg_match_all('/\w+(:\w+(\([A-z0-9,]+\))?)?(\.(\(((?>[^()]+)|(?-2))*\)))?/', $allowedProperties, $allowedProperties);
				$allowedProperties = $allowedProperties[0];
                
			}
            
			// convert the allowed types to arrays if it's an object
			else if(gettype($allowedProperties) == 'object')
                $allowedProperties = array_keys(get_object_vars($allowedProperties));
            
			// convert the allowed types to arrays if it's an associative array
			else if(array_keys($allowedProperties) !== range(0, count($allowedProperties) - 1))
                $allowedProperties = array_keys($allowedProperties);
            
		} else
            $allowedProperties = array_keys(get_object_vars($data));
        
		// assing properties
		foreach($allowedProperties as $property) {
            
			if(!preg_match('/^\w+$/', $property)) {
                
				// get the key
				preg_match('/^\w+/', $property, $key);
				$key = $key[0];
                
				// if it is a function
				if(preg_match('/^\w+:\w+(\([A-z0-9,]+\))?/', $property, $function)) {
                    
					// get the function
					preg_match_all('/\w+/', $function[0], $functionName);
					$functionName = $functionName[0][1];
                    
					if(preg_match('/^\w+:\w+\([A-z0-9,]+\)/', $function[0])) {
						$params = preg_replace('/^\w+:\w+\(|\)$/', '', $function[0]);
						$data->$key = $data->$functionName($params);
					} else
                        $data->$key = $data->$functionName();
				}
				if(preg_match('/\w+(:\w+(\([A-z0-9,]+\))?)?(\.(\(((?>[^()]+)|(?-2))*\)))/', $property)) {
					$columns = preg_replace('/^\w+(:\w+(\([A-z0-9,]+\))?)?\.\(|\)$/', '', $property);
					if(gettype($data->$key) == 'array') {
						$keydata = $data->$key;
						$data->$key = array();
						for($i = 0; $i < count($keydata); $i++)
							array_push($data->$key, new Object($keydata[$i], $columns));
					} else
						$data->$key = new Object($data->$key, $columns);
				}
				$property = $key;
			}
            
			if($forceAll)
				$this->$property = isset($data->$property) ? $data->$property : null;
            
			else if(isset($data->$property))
				$this->$property = $data->$property;
		}
    }
	/**
	 * to convert the object to string of JSON
	 * @return string
	 */
	public function __toString() {
		return json_encode($this);
	}
    public function returnObject($allowedProperties) {
		return new Object($this, $allowedProperties);
	}
    public function encode($key) {
        return htmlspecialchars($this->$key, ENT_QUOTES, 'UTF-8');
    }
    public static function returnKey($objects, $key) {
        $array = array();
        foreach($objects as $object)
            array_push($array, $object->$key);
        return $array;
    }
}