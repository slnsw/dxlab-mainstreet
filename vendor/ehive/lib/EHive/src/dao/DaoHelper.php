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
class DaoHelper {

	public static function urlWithCatalogueType( $path, $catalogueType ) {
		switch ($catalogueType) {
			case 'archives':
			case 'archaeology':
			case 'art':
			case 'history':
			case 'library':
			case 'natural_science':
			case 'photography':
			case '':
				return $path.'/'.$catalogueType;
				break;

			default:
				throw new EHiveApiException('Invalid catalogue type: "'.$catalogueType.'"');
		}
	}

	public static function getObjectsQueryString($query, $hasImages, $sort, $direction, $offset, $limit) {


		$queryString = "";


		if ( !is_null($query) && strlen(trim($query)) > 0 ) {
			$queryString = "query=".trim($query);
		}

		if ( !is_null($hasImages) && $hasImages == true  ) {
			$i = "hasImages=true";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$i;
			} else {
				$queryString = $queryString.$i;
			}

		} elseif (!is_null($hasImages) && $hasImages == false) {
			$i = "hasImages=false";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$i;
			} else {
				$queryString = $queryString.$i;
			}

		} elseif ($is_null) {
			throw new EHiveApiException('Invalid parameter value, hasImage cannot be null.');
		}

		if (!is_null($sort)) {
			$s = "sort={$sort}";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$s;
			} else {
				$queryString = $queryString.$s;
			}
		}

		if (!is_null($direction)) {
			$d = "direction={$direction}";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$d;
			} else {
				$queryString = $queryString.$d;
			}
		}

		if (!is_null($offset)) {
			$o = "offset={$offset}";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$o;
			} else {
				$queryString = $queryString.$o;
			}
		}

		if (!is_null($limit)) {
			$l = "limit={$limit}";
			if(strlen($queryString) > 0) {
				$queryString = $queryString."&".$l;
			} else {
				$queryString = $queryString.$l;
			}
		}

		return $queryString;
	}

}?>