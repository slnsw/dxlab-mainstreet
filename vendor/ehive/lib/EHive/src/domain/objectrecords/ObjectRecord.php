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
require_once EHIVE_API_ROOT_DIR.'/domain/objectrecords/FieldSet.php';
require_once EHIVE_API_ROOT_DIR.'/domain/objectrecords/MediaSet.php';

class ObjectRecord {

	const IMAGE = 'image';
	
	public $objectRecordId = 0;	
	public $externalId = "";
	public $objectUrl = "";
	public $slug = "";
	public $catalogueType = "";
	public $metadataRights = "";
	public $accountId = 0;
	public $searchScore = 0;
	public $fieldSets = array();
	public $mediaSets = array();
	
	
	public function __construct($json = null){
		if (isset($json)) {

			$this->objectRecordId			= isset($json->objectRecordId)			? $json->objectRecordId				: 0;				
			$this->externalId				= isset($json->externalId)				? $json->externalId					: "";
			$this->objectUrl				= isset($json->objectUrl)				? $json->objectUrl					: "";
			$this->slug						= isset($json->slug)					? $json->slug						: "";			
			$this->catalogueType			= isset($json->catalogueType)			? $json->catalogueType				: "";
			$this->metadataRights			= isset($json->metadataRights)			? $json->metadataRights				: "";
			$this->accountId				= isset($json->accountId)				? $json->accountId					: 0;
			$this->searchScore				= isset($json->searchScore)				? $json->searchScore				: 0;
			
			if (isset($json->fieldSets)) {			
				foreach ($json->fieldSets as $fieldSetJson) {
					$fieldSet = new FieldSet($fieldSetJson);
					
					$this->fieldSets[$fieldSet->identifier] = $fieldSet;					
				}							
			}

			if (isset($json->mediaSets)) {
				foreach ($json->mediaSets as $mediaSetJson) {
					$mediaSet = new MediaSet($mediaSetJson);
						
					$this->mediaSets[$mediaSet->identifier] = $mediaSet;
				}
			}
				
		}
	}
		
	public function getFieldSetByIdentifier($fieldSetIdentifier) {
		
		if (isset($this->fieldSets)) {
			if ( array_key_exists( $fieldSetIdentifier, $this->fieldSets )) {
				return $this->fieldSets[ $fieldSetIdentifier ];				
			} else {
				return null;
			}				
			return null;		
		}		
	}	

	public function getMediaSetByIdentifier($mediaSetIdentifier) {
	
		if (isset($this->mediaSets)) {
			if ( array_key_exists( $mediaSetIdentifier, $this->mediaSets )) {
				return $this->mediaSets[ $mediaSetIdentifier ];
			} else {
				return null;
			}
			return null;
		}
	}
	
}?>