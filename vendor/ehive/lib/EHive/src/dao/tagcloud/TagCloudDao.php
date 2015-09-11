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
class TagCloudDao {

	private $transport;

	public function __construct($trasport) {
		require_once EHIVE_API_ROOT_DIR.'/dao/DaoHelper.php';
		$this->transport = $trasport;
	}

	public function getTagCloudInEHive($limit) {
		require_once EHIVE_API_ROOT_DIR.'/domain/tagcloud/TagCloud.php';
		$path = VERSION_ID . "/objectrecords/tagcloud";
		$queryString = isset($limit) ? "limit={$limit}" : "";
		$json = $this->transport->get($path, $queryString);
		$responseTagCloud = new TagCloud($json);
		return $responseTagCloud;
	}
	
	public function getTagCloudInAccount($accountId, $limit) {
		require_once EHIVE_API_ROOT_DIR.'/domain/tagcloud/TagCloud.php';
		$path = VERSION_ID . "/accounts/{$accountId}/objectrecords/tagcloud";
		$queryString = isset($limit) ? "limit={$limit}" : "";
		$json = $this->transport->get($path, $queryString);
		$responseTagCloud = new TagCloud($json);
		return $responseTagCloud;
	}
	
	public function getTagCloudInCommunity($communityId, $limit) {
		require_once EHIVE_API_ROOT_DIR.'/domain/tagcloud/TagCloud.php';
		$path = VERSION_ID . "/communities/{$communityId}/objectrecords/tagcloud";
		$queryString = isset($limit) ? "limit={$limit}" : "";
		$json = $this->transport->get($path, $queryString);
		$responseTagCloud = new TagCloud($json);
		return $responseTagCloud;
	}
	
}?>