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
class CommentsDao {

	private $transport;

	public function __construct($trasport) {
		$this->transport = $trasport;
	}
	
	public function getObjectRecordComments($objectRecordId, $offset=0, $limit=50) {
		require_once EHIVE_API_ROOT_DIR.'/domain/comments/CommentsCollection.php';
		$path = VERSION_ID . "/objectrecords/{$objectRecordId}/comments";
		$queryString = "offset={$offset}&limit={$limit}";
		$json = $this->transport->get($path, $queryString);
		$responseCommentsCollection = new CommentsCollection($json);
		return $responseCommentsCollection;
	}
	
	public function addObjectRecordComment($objectRecordId, $comment) {
		require_once EHIVE_API_ROOT_DIR.'/domain/comments/Comment.php';
		$path = VERSION_ID . "/objectrecords/{$objectRecordId}/comment";
		$json = $this->transport->post($path, $comment);
		$responseComment = new Comment($json);
		return $responseComment;
	}
	
}?>