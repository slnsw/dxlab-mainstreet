<?php

# vendor/ehive/lib/EHive/src/EHive.php

define('CLIENT_ID', '***REMOVED***');
define('CLIENT_SECRET', '***REMOVED***');
define('TRACKING_ID', '***REMOVED***');

require_once(EHIVE_API_ROOT_DIR . '/sl/Transport.php');
require_once(EHIVE_API_ROOT_DIR . '/dao/DaoHelper.php');

class EHive
{

    private $_transport;
    protected $_configuration;

    /**
     * A constructor method instantiating the transport class responsible for obtaining and sustaining the connection to the eHive API.
     */

    public function __construct()
    {
        // An extension of the default Transport class provided by the native eHive API SDK.
        // Instantiate the Transport class supporting multiple cURL requests through a single connection.
        $this->_transport = new Multi_Transport(CLIENT_ID, CLIENT_SECRET, TRACKING_ID);
        // Provide defaults for methods executed against the API to ensure low risk of failure.
        $this->_configuration = parse_ini_file( EHIVE_API_ROOT_DIR .'/EHiveApi.ini');
    }

    /**
     * A primary method to execute all API calls to eHive.
     *
     * @return mixed
     */

    protected function _execute($method, $params)
    {
        return $this->_transport->$method($params['path'], $params['context']);
    }

    /**
     *
     * @param  [type] $objects [description]
     * @return [type]          [description]
     */

    protected function _getObjectIds($objects)
    {
        $ids = array();
        if(is_array($objects)){
            foreach($objects as $object){
                $ids[] = $object->objectRecordId;
            }
        }
        return $ids;
    }
}