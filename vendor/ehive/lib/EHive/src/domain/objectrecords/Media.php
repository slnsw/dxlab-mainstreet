<?php
/*
	Copyright (C) 2012 Vernon Systems Limited
	
	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
	to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
require_once EHIVE_API_ROOT_DIR.'/domain/objectrecords/Attribute.php';

class Media {

	public $identifier;
	public $attributes = array();
	
	public function __construct($json = null){
		if (isset($json)) {
			$this->identifier = isset($json->identifier) ? $json->identifier : "";
			
			if (isset($json->attributes)) {
				foreach($json->attributes as $attributeJson) {
					$attribute = new Attribute($attributeJson);
					$this->attributes[$attribute->key] = $attribute;
				}
			}		
		}
	}	
	
	
	public function getMediaAttribute( $attributeKey ) {
		if (array_key_exists( $attributeKey, $this->attributes)) {
			$attribute = $this->attributes[ $attributeKey ];
			return $attribute->value;
		} else {
			return false;
		}		
	}
}
?>