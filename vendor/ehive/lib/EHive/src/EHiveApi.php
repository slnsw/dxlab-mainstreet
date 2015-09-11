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
define ("EHIVE_API_ROOT_DIR", dirname(__FILE__) );

define ("VERSION_ID", "/v2");

require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveApiException.php';
// require_once EHIVE_API_ROOT_DIR.'/transport/Transport.php';
require_once EHIVE_API_ROOT_DIR. '/overrides/Transport.php';

class SL_EHiveApi {

	private $transport;

	public function __construct( $clientId=null, $clientSecret=null, $trackingId=null ) {
		if ( is_null($clientId) && is_null($clientSecret) && is_null($trackingId) ) {

			if (file_exists( dirname(__FILE__).'/EHiveApi.ini') == false) {
				throw new EHiveApiException('Could not find EHiveApi.ini file.');
			}

			$configuration = parse_ini_file( dirname(__FILE__).'/EHiveApi.ini');
			if ($this->configuration == false) {
				throw new EHiveApiException('Could not read contents of '.dirname(__FILE__).'/EHiveApi.ini');
			}

			if (key_exists('client_id', $configuration) == false) {
				throw new EHiveApiException('Could not find "client_id" in EHiveApi.ini.');
			}
			$clientId = $configuration['client_id'];

			if (key_exists('client_secret', $configuration) == false) {
				throw new EHiveApiException('Could not find "client_secret" in EHiveApi.ini.');
			}
			$clientSecret = $configuration['client_secret'];

			if (key_exists('tracking_id', $configuration) == false) {
				throw new EHiveApiException('Could not find "tracking_id" in EHiveApi.ini.');
			}
			$trackingId = $configuration['tracking_id'];

		} else {

			if ( is_null($clientId) || is_null($clientSecret) || is_null($trackingId) ) {
				throw new EHiveApiException('All three constructor parameters must be set. clientId:'.$clientId.', clientSecret:'.$clientSecret.', trackingId:'.$trackingId);
			}
		}

		$this->transport = new Multi_Transport($clientId, $clientSecret, $trackingId);
	}

	function getConfiguration() {
		return $this->configuration;
	}


	//
	// Get Accounts
	//
	public function getAccount($accountId) {
		require_once EHIVE_API_ROOT_DIR.'/dao/accounts/AccountsDao.php';
		$accountsDao = new AccountsDao($this->transport);
		$account = $accountsDao->getAccount($accountId);
		return $account;
	}

	public function getAccountInCommunity($communityId, $accountId) {
		require_once EHIVE_API_ROOT_DIR.'/dao/accounts/AccountsDao.php';
		$accountsDao = new AccountsDao($this->transport);
		$account = $accountsDao->getAccountInCommunity($communityId, $accountId);
		return $account;
	}

	//
	//	Get ObjectRecords
	//

	public function getObjectRecord($objectRecordId) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecords/ObjectRecordsDao.php';
		$objectRecordsDao = new ObjectRecordsDao($this->transport);
		$objectRecords = $objectRecordsDao->getObjectRecord($objectRecordId);
		return $objectRecords;
	}

	public function getObjectRecordsInEHive( $query, $hasImages=false, $sort, $direction, $offset=0, $limit=12 ) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecords/ObjectRecordsDao.php';
		$objectRecordsDao = new ObjectRecordsDao($this->transport);
		$objectRecords = $objectRecordsDao->getObjectRecordsInEHive( $query, $hasImages, $sort, $direction, $offset, $limit );
		return $objectRecords;
	}

	public function getObjectRecordsInAccount( $accountId, $query, $hasImages=false, $sort, $direction, $offset=0, $limit=12 ) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecords/ObjectRecordsDao.php';
		$objectRecordsDao = new ObjectRecordsDao($this->transport);
		$objectRecords = $objectRecordsDao->getObjectRecordsInAccount( $accountId, $query, $hasImages, $sort, $direction, $offset, $limit );
		return $objectRecords;
	}

	public function getObjectRecordsInCommunity( $communityId, $query, $hasImages=false, $sort, $direction, $offset=0, $limit=12 ) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecords/ObjectRecordsDao.php';
		$objectRecordsDao = new ObjectRecordsDao($this->transport);
		$objectRecords = $objectRecordsDao->getObjectRecordsInCommunity( $communityId, $query, $hasImages, $sort, $direction, $offset, $limit );
		return $objectRecords;
	}

	public function getObjectRecordsInAccountInCommunity( $communityId, $accountId, $query, $hasImages=false, $sort, $direction, $offset=0, $limit=12 ) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecords/ObjectRecordsDao.php';
		$objectRecordsDao = new ObjectRecordsDao($this->transport);
		$objectRecords = $objectRecordsDao->getObjectRecordsInAccountInCommunity( $communityId, $accountId, $query, $hasImages, $sort, $direction, $offset, $limit );
		return $objectRecords;
	}


	//
	// Get Interesting Object Records
	//
	public function getInterestingObjectRecordsInEHive($hasImages=false, $catalogueType="", $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/interestingobjectrecords/InterestingObjectRecordsDao.php';
		$interestingObjectRecordsDao = new InterestingObjectRecordsDao($this->transport);
		$interestingObjectRecords = $interestingObjectRecordsDao->getInterestingObjectRecordsInEHive($hasImages, $catalogueType, $offset, $limit);
		return $interestingObjectRecords;
	}

	public function getInterestingObjectRecordsInAccount($accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/interestingobjectrecords/InterestingObjectRecordsDao.php';
		$interestingObjectRecordsDao = new InterestingObjectRecordsDao($this->transport);
		$interestingObjectRecords = $interestingObjectRecordsDao->getInterestingObjectRecordsInAccount($accountId, $catalogueType, $hasImages, $offset, $limit);
		return $interestingObjectRecords;
	}

	public function getInterestingObjectRecordsInCommunity($communityId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/interestingobjectrecords/InterestingObjectRecordsDao.php';
		$interestingObjectRecordsDao = new InterestingObjectRecordsDao($this->transport);
		$interestingObjectRecords = $interestingObjectRecordsDao->getInterestingObjectRecordsInCommunity($communityId, $catalogueType, $hasImages, $offset, $limit);
		return $interestingObjectRecords;
	}

	public function getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/interestingobjectrecords/InterestingObjectRecordsDao.php';
		$interestingObjectRecordsDao = new InterestingObjectRecordsDao($this->transport);
		$interestingObjectRecords = $interestingObjectRecordsDao->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType, $hasImages, $offset, $limit);
		return $interestingObjectRecords;
	}

	//
	// Get Popular Object Records
	//
	public function getPopularObjectRecordsInEHive($catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/popularobjectrecords/PopularObjectRecordsDao.php';
		$popularObjectRecordsDao = new PopularObjectRecordsDao($this->transport);
		$popularObjectRecords = $popularObjectRecordsDao->getPopularObjectRecordsInEHive($catalogueType, $hasImages, $offset, $limit);
		return $popularObjectRecords;
	}

	public function getPopularObjectRecordsInAccount($accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/popularobjectrecords/PopularObjectRecordsDao.php';
		$popularObjectRecordsDao = new PopularObjectRecordsDao($this->transport);
		$popularObjectRecords = $popularObjectRecordsDao->getPopularObjectRecordsInAccount($accountId, $catalogueType, $hasImages, $offset, $limit);
		return $popularObjectRecords;
	}

	public function getPopularObjectRecordsInCommunity($communityId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/popularobjectrecords/PopularObjectRecordsDao.php';
		$popularObjectRecordsDao = new PopularObjectRecordsDao($this->transport);
		$popularObjectRecords = $popularObjectRecordsDao->getPopularObjectRecordsInCommunity($communityId, $catalogueType, $hasImages, $offset, $limit);
		return $popularObjectRecords;
	}

	public function getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/popularobjectrecords/PopularObjectRecordsDao.php';
		$popularObjectRecordsDao = new PopularObjectRecordsDao($this->transport);
		$popularObjectRecords = $popularObjectRecordsDao->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType, $hasImages, $offset, $limit);
		return $popularObjectRecords;
	}

	//
	// Get Recent Object Records
	//
	public function getRecentObjectRecordsInEHive($catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/recentobjectrecords/RecentObjectRecordsDao.php';
		$recentObjectRecordsDao = new RecentObjectRecordsDao($this->transport);
		$recentObjectRecords = $recentObjectRecordsDao->getRecentObjectRecordsInEHive($catalogueType, $hasImages, $offset, $limit);
		return $recentObjectRecords;
	}

	public function getRecentObjectRecordsInAccount($accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/recentobjectrecords/RecentObjectRecordsDao.php';
		$recentObjectRecordsDao = new RecentObjectRecordsDao($this->transport);
		$recentObjectRecords = $recentObjectRecordsDao->getRecentObjectRecordsInAccount($accountId, $catalogueType, $hasImages, $offset, $limit);
		return $recentObjectRecords;
	}

	public function getRecentObjectRecordsInCommunity($communityId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/recentobjectrecords/RecentObjectRecordsDao.php';
		$recentObjectRecordsDao = new RecentObjectRecordsDao($this->transport);
		$recentObjectRecords = $recentObjectRecordsDao->getRecentObjectRecordsInCommunity($communityId, $catalogueType, $hasImages, $offset, $limit);
		return $recentObjectRecords;
	}

	public function getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType="", $hasImages=false, $offset=0, $limit=12){
		require_once EHIVE_API_ROOT_DIR.'/dao/recentobjectrecords/RecentObjectRecordsDao.php';
		$recentObjectRecordsDao = new RecentObjectRecordsDao($this->transport);
		$recentObjectRecords = $recentObjectRecordsDao->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, $catalogueType, $hasImages, $offset, $limit);
		return $recentObjectRecords;
	}


	//
	// Object Comments
	//
	public function getObjectRecordComments($objectRecordId, $offset, $limit) {
		require_once EHIVE_API_ROOT_DIR.'/dao/comments/CommentsDao.php';
		$commentsDao = new CommentsDao($this->transport);
		$comments = $commentsDao->getObjectRecordComments($objectRecordId, $offset, $limit);
		return $comments;
	}

	public function addObjectRecordComment($objectRecordId, $comment) {
		require_once EHIVE_API_ROOT_DIR.'/dao/comments/CommentsDao.php';
		$commentsDao = new CommentsDao($this->transport);
		$comment = $commentsDao->addObjectRecordComment($objectRecordId, $comment);
		return $comment;
	}


	//
	//	Object Record Tags
	//
	public function getObjectRecordTags($objectRecordId) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecordtags/ObjectRecordTagsDao.php';
		$objectRecordTagsDao = new ObjectRecordTagsDao($this->transport);
		$objectRecordTags = $objectRecordTagsDao->getObjectRecordTags($objectRecordId);
		return $objectRecordTags;
	}

	public function addObjectRecordTag($objectRecordId, $objectRecordTag) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecordtags/ObjectRecordTagsDao.php';
		$objectRecordTagsDao = new ObjectRecordTagsDao($this->transport);
		$objectRecordTag = $objectRecordTagsDao->addObjectRecordTag($objectRecordId, $objectRecordTag);
		return $objectRecordTag;
	}

	public function deleteObjectRecordTag($objectRecordId, $tag) {
		require_once EHIVE_API_ROOT_DIR.'/dao/objectrecordtags/ObjectRecordTagsDao.php';
		$objectRecordTagsDao = new ObjectRecordTagsDao($this->transport);
		$objectRecordTag = $objectRecordTagsDao->deleteObjectRecordTag($objectRecordId, $tag);
		return $objectRecordTag;
	}

	//
	// Tag Clouds
	//
	public function getTagCloudInEHive($limit) {
		require_once EHIVE_API_ROOT_DIR.'/dao/tagcloud/TagCloudDao.php';
		$tagCloudDao = new TagCloudDao($this->transport);
		$tagCloud = $tagCloudDao->getTagCloudInEHive($limit);
		return $tagCloud;
	}

	public function getTagCloudInAccount($accountId, $limit) {
		require_once EHIVE_API_ROOT_DIR.'/dao/tagcloud/TagCloudDao.php';
		$tagCloudDao = new TagCloudDao($this->transport);
		$tagCloud = $tagCloudDao->getTagCloudInAccount($accountId, $limit);
		return $tagCloud;
	}

	public function getTagCloudInCommunity($communityId, $limit) {
		require_once EHIVE_API_ROOT_DIR.'/dao/tagcloud/TagCloudDao.php';
		$tagCloudDao = new TagCloudDao($this->transport);
		$tagCloud = $tagCloudDao->getTagCloudInCommunity($communityId, $limit);
		return $tagCloud;
	}
}