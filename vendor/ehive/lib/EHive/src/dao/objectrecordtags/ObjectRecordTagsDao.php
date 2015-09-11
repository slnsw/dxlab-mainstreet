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
class ObjectRecordTagsDao {

	private $transport;

	public function __construct($trasport) {
		require_once EHIVE_API_ROOT_DIR.'/dao/DaoHelper.php';
		$this->transport = $trasport;
	}
		
	public function getObjectRecordTags($objectRecordId) {
		require_once EHIVE_API_ROOT_DIR.'/domain/objectrecordtags/ObjectRecordTagsCollection.php';
		$path = VERSION_ID . "/objectrecords/{$objectRecordId}/tags";
		$json = $this->transport->get($path);
		$responseObjectTagsCollection = new ObjectRecordTagsCollection($json);
		return $responseObjectTagsCollection;
	}
	
	public function addObjectRecordTag($objectRecordId, $objectRecordTag) {
		require_once EHIVE_API_ROOT_DIR.'/domain/objectrecordtags/ObjectRecordTag.php';
		$path = VERSION_ID . "/objectrecords/{$objectRecordId}/tags";
		$json = $this->transport->post($path, $objectRecordTag);
		$responseObjectRecordTag = new ObjectRecordTag($json);
		return $responseObjectRecordTag;
	}
	
	public function deleteObjectRecordTag($objectRecordId, $objectRecordTag) {
		require_once EHIVE_API_ROOT_DIR.'/domain/objectrecordtags/ObjectRecordTag.php';
		$path = VERSION_ID . "/objectrecords/{$objectRecordId}/tags/{$objectRecordTag->rawTagName}";
		$json = $this->transport->delete($path);
		$responseObjectTag = new ObjectRecordTag($json);
		return $responseObjectTag;
	}	

}?>