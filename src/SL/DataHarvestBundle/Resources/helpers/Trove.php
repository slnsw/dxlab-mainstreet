<?php

namespace SL\DataHarvestBundle\Resources\Helpers;

class Trove
{

    private $_data;

    const TROVE_SEARCH_API_PATH = 'http://api.trove.nla.gov.au/result?';

    public function __construct(){
    }

    public function getObjects($params)
    {
        $zebra = new \Zebra_Zebra();
        $this->preparePaths($params['paths']);
        $zebra->get($params['paths'], array($this, 'processObjects'));
        while(count($this->_data) != count($params['paths'])){
            // Delay another iteration for 1 millisecond, in order to allow time for the data to propagate.
            usleep(10000);
        }
        // Once we have the complete data set containing the Zebra_cURL response body's, return.
        return $this->_data['results'];
    }

    /**
     *  A method to replace single/multiple spaces with the encoded equivalent for cURL request paths, and prepend the Trove search API path.
     */

    public function preparePaths(&$paths)
    {
        foreach($paths as &$path){
            $path = self::TROVE_SEARCH_API_PATH . preg_replace('#\s+#', '%20', $path);
        }
    }

    /**
     * A method acting as a per request callback for the Zebra_cURL library.
     */

    public function processObjects($result)
    {
        $this->_data['results'][] = $result->body;
    }

}