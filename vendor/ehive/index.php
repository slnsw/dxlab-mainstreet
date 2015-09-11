<?php

/**
 * Sample API calls to eHive.
 */

// Define constants
define('CLIENT_ID', 'ee452629fdd8429e8947a157bc5dcd8f');
define('CLIENT_SECRET', '0a5ba3bd2d3a4bb3a80d6c77f9d50edb');
define('TRACKING_ID', 'af8c26c760dc49c3a5869d4a390c6407');
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