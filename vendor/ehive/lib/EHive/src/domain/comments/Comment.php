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

class Comment {
	
	public $commentId = 0;
	public $commentorName = "";
	public $commentorEmailAddress = "";
	public $commentText = "";
	public $whenCreated = "";
	
	public function __construct($json = null) {
		if (isset($json)) {
			$this->commentId 				= isset($json->commentId) 				? $json->commentId 				: 0;
			$this->commentorName 			= isset($json->commentorName) 			? $json->commentorName 			: "";
			$this->commentorEmailAddress 	= isset($json->commentorEmailAddress)	? $json->commentorEmailAddress 	: "";
			$this->commentText 				= isset($json->commentText) 			? $json->commentText 			: "";
			$this->whenCreated				= isset($json->whenCreated)				? $json->whenCreated			: "";			
		}
	}
}?>