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

require_once EHIVE_API_ROOT_DIR.'/domain/objectrecords/MediaSet.php';

class Account {
	
	public $accountId = 0;
	
	public $publicProfileName = "";
	public $shortProfileName = "";
	
	public $physicalAddress = "";
	public $postalAddress = "";
	
	public $phoneNumber = "";
	public $emailAddress = "";
	public $facsimile = "";
	public $website = "";
	public $hoursOfOperation = "";
	public $admissionCharges = "";
	public $staffDetails = "";
	public $aboutCollection = "";
	
	public $wheelChairAccessVacility = false;
	public $cafeFacility = false;
	public $referenceLibraryFacility = false;
	public $parkingFacility = false;
	public $shopFacility = false;
	public $functionSpaceFacility = false;
	public $guidedTourFacility = false;
	public $publicProgrammesFacility = false;
	public $membershipClubFacility = false;
	public $toiletFacility = false;

	public $otherFacility = "";
	
	public $latitude = 0;
	public $longitude = 0;
	public $zoomLevel = 0;
	
	public $mediaSets = array();

	
	public function __construct($json = null){
		if (isset($json)) {
						
			$this->accountId				= isset($json->accountId)					? $json->accountId						: 0;
			
			$this->publicProfileName		= isset($json->publicProfileName)			? $json->publicProfileName				: "";
			$this->shortProfileName			= isset($json->shortProfileName)			? $json->shortProfileName				: "";
			
			$this->physicalAddress			= isset($json->physicalAddress)				? $json->physicalAddress				: "";
			$this->postalAddress			= isset($json->postalAddress)				? $json->postalAddress					: "";
			
			$this->phoneNumber				= isset($json->phoneNumber)					? $json->phoneNumber					: "";
			$this->emailAddress				= isset($json->emailAddress)				? $json->emailAddress					: "";
			$this->facsimile				= isset($json->facsimile)					? $json->facsimile						: "";
			$this->website					= isset($json->website)						? $json->website						: "";
			$this->hoursOfOperation			= isset($json->hoursOfOperation)			? $json->hoursOfOperation				: "";
			$this->admissionCharges			= isset($json->admissionCharges)			? $json->admissionCharges				: "";
			$this->staffDetails				= isset($json->staffDetails)				? $json->staffDetails					: "";
			$this->aboutCollection			= isset($json->aboutCollection)				? $json->aboutCollection				: "";
			
			$this->wheelChairAccessFacility	= isset($json->wheelChairAccessFacility)	? $json->wheelChairAccessFacility		: false;
			$this->cafeFacility 			= isset($json->cafeFacility)				? $json->cafeFacility					: false;
			$this->referenceLibraryFacility = isset($json->referenceLibraryFacility)	? $json->referenceLibraryFacility		: false;
			$this->parkingFacility			= isset($json->parkingFacility)				? $json->parkingFacility				: false;
			$this->shopFacility				= isset($json->shopFacility)				? $json->shopFacility					: false;
			$this->functionSpaceFacility	= isset($json->functionSpaceFacility)		? $json->functionSpaceFacility			: false;
			$this->guidedTourFacility		= isset($json->guidedTourFacility)			? $json->guidedTourFacility				: false;
			$this->publicProgrammesFacility	= isset($json->publicProgrammesFacility)	? $json->publicProgrammesFacility		: false;
			$this->membershipClubFacility	= isset($json->membershipClubFacility)		? $json->membershipClubFacility			: false;
			$this->toiletFacility			= isset($json->toiletFacility)				? $json->toiletFacility					: false;
			
			$this->otherFacility			= isset($json->otherFacility)				? $json->otherFacility					: "";						
			   
			$this->latitude					= isset($json->latitude)					? $json->latitude						: 0;
			$this->longitude				= isset($json->longitude)					? $json->longitude						: 0;
			$this->zoomLevel				= isset($json->zoomLevel)					? $json->zoomLevel						: 0;
				

			if (isset($json->mediaSets)) {
				foreach ($json->mediaSets as $mediaSetJson) {
					$mediaSet = new MediaSet($mediaSetJson);
			
					$this->mediaSets[$mediaSet->identifier] = $mediaSet;
				}
			}
				
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