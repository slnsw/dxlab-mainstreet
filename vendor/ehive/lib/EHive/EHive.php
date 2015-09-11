<?php

# vendor/ehive/lib/EHive/EHive.php

define('EHIVE_API_ROOT_DIR', __DIR__ . '/src');
define('EHIVE_VERSION_ID', '/v2');

require_once(__DIR__ . '/src/EHive.php');

class EHive_EHive extends EHive
{

    private $_cache = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return data about a single object from an account.
     *
     * @return object
     */

    public function getObjectFromAccount($params)
    {
        if(empty($params['account'])){
            return false;
        }
        $params = array_merge($this->_configuration['defaults'], $params);
        extract($params);
        $query = DaoHelper::getObjectsQueryString($query, $hasImages, $sort, $direction, $offset, $limit);
        unset($params);
        $path = EHIVE_VERSION_ID . "/accounts/" . $account . "/objectrecords";
        $params = array(
            'path' => $path,
            'context' => $query,
        );
        return $this->_execute('get', $params);
    }

    /**
     *  A method to load objects from a specified eHive account, with additional filtering by tag.
     *
     * @param array $params
     * @return object
     */

    public function getObjectsFromAccount($params)
    {
        if(empty($params['account'])){
            return false;
        }
        $params = array_merge($this->_configuration['defaults'], $params);
        // Perform an initial query in order to get the total number of objects under the account. This will provide the upper limit to the number of iterations when fetching a batch of objects.
        $response = $this->getObjectFromAccount($params);
        $responses = array();
        if(!$response
            || !is_object($response)){
            return false;
        }
        $params['total'] = $response->totalObjects;
        $params['offset'] += $params['limit'];
        $responses = array_merge(array_merge(array($response), $responses), $this->_getObjectsFromAccount($params));
        $objects = array();
        if(isset($params['tag'])
            && !empty($params['tag'])){
            do{
                $ids = array();
                if(is_array($responses)
                    && !empty($responses)){
                    foreach($responses as $response){
                        $ids = array_merge($ids, $this->_getObjectIds($response->objectRecords));
                    }
                    if($ids = ($this->getObjectsRecordTags($ids, $params['tag']))){
                        $addParams = array(
                            'context' => array(),
                            'path' => array(),
                        );
                        foreach($ids as $id){
                            $path = EHIVE_VERSION_ID . "/objectrecords/" . $id;
                            $addParams['path'][] = $path;
                        }
                        if(!empty(array_filter($addParams))){
                            $objects = array_merge($objects, $this->_execute('getMultiple', $addParams));
                        }
                    }
                    if(count($objects) < $params['max']){
                        $params['offset'] += $params['limit'];
                        $responses = $this->_getObjectsFromAccount($params);
                    }
                }
            }while(count($objects) < $params['max']
                    && ($params['offset'] + $params['limit']) <= $params['total']);
        }
        return array_slice($objects, 0, $params['max']);
    }

    /**
     *  A method to load objects from the eHive API using the parameters specified in the @getObjectsFromAccount method, the query is built and executed using a multiple cURL request handler.
     *
     * @param array $params
     * @return array $response
     */

    private function _getObjectsFromAccount(&$params)
    {
        extract($params);
        $params['offset'] =& $offset;
        $queries = array();
        for($i = 0; $i < 10; $i++){
            $queries[] = DaoHelper::getObjectsQueryString($query, $hasImages, $sort, $direction, $offset, $limit);
            $offset += $limit;
        }
        // Endpoints should be in an ini file or helper class.
        $path = EHIVE_VERSION_ID . "/accounts/" . $account . "/objectrecords";
        $query = array(
            'path' => $path,
            'context' => $queries,
        );
        $response = $this->_execute('getMultiple', $query);
        return ($response
                  && is_array($response) ? $response : array());
    }

    /**
     *  A method to load object tags based on a selection of ids, with a post filter process for filtering the objects returned by a specified tag.
     *
     * @param array $ids
     * @param string $tag
     * @return array $ids
     */

    public function getObjectsRecordTags($ids, $tag)
    {
        $params = array(
            'context' => array(),
        );
        foreach($ids as $k => $v){
            $params['path'][] = EHIVE_VERSION_ID . "/objectrecords/{$v}/tags";
        }
        unset($ids);
        $ids = array();
        $response = $this->_execute('getMultiple', $params);
        if(is_array($response)){
            foreach($response as $object){
                foreach($object->objectRecordTags as $v){
                    if(stristr($v->cleanTagName, $tag)){
                        $ids[] = $object->objectRecordId;
                    }
                }
            }
        }
        return $ids;
    }
}