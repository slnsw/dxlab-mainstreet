<?php

/**
 * Sample API calls to eHive.
 */

// Define constants
define('CLIENT_ID', '***REMOVED***');
define('CLIENT_SECRET', '***REMOVED***');
define('TRACKING_ID', '***REMOVED***');
define('ACCOUNT', 5051);
define('QUERY', '');
define('INCRE', 10);

// Include the bootstrap file for the eHive API.
require_once dirname(__FILE__) . '/lib/SL_EHiveAPI.php';

// Establish a connection to the eHive API.
require_once EHIVE_API_ROOT_DIR. '/overrides/Transport.php';

require_once EHIVE_API_ROOT_DIR.'/dao/DAOHelper.php';
require_once EHIVE_API_ROOT_DIR.'/domain/objectrecords/ObjectRecordsCollection.php';


// The path for this demo.
$path = VERSION_ID . "/accounts/" . ACCOUNT . "/objectrecords";

$query = DaoHelper::getObjectsQueryString(QUERY, TRUE, 'name', 'desc', 0, INCRE);
$transport = new Multi_Transport(CLIENT_ID, CLIENT_SECRET, TRACKING_ID);

$responses = array();
$response = $transport->get($path, $query);

$queries = array();
if(is_object($response)){
    $responses = array_merge($responses, array($response));
    $total = $response->totalObjects;
    $range = array_chunk(range(0, $total), 10);
    $offset = INCRE;
    for($i = 0; $i < count($range); $i++){
        $queries[] = DaoHelper::getObjectsQueryString(QUERY, TRUE, 'name', 'desc', $offset, INCRE);
        $offset+=INCRE;
        if($offset == 100){
            break;
        }
    }
}
$response = $transport->getMultiple($path, $queries);
$responses = array_merge($responses, $response);
unset($response);

if(is_array($responses)
    && !empty($responses)){
    $objects['objects'] = array();
    foreach($responses as $response){
        $objects['objects'] = array_merge($objects['objects'], $response->objectRecords);
    }
    print '<pre>';
    print_r($objects);
    print '</pre>';
    exit;
}

// if(is_object($objects)
//     && !empty($objects->objectRecords)){
//     foreach($objects->objectRecords as $record){
//         // print '<pre>';
//         // print_r($record);
//         // print '</pre>';
//         $id = $record->objectRecordId;
//         $tags = $api->getObjectRecordTags($id);
//     }
// }