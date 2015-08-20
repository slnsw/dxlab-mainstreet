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
        // Append the maximum number of results to be returned to the query string, this is calculated by dividing the number of paths by the maximum number of results by the number of paths, whilst ensuring an absolute number is appended.
        foreach($params['paths'] as &$path){
          $path = $path . '&n=' . abs(round($params['max'] / count($params['paths'])));
        }
        $zebra->get($params['paths'], array($this, 'processObjects'));
        while(count($this->_data['results']) != count($params['paths'])){
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

    /**
     * A static method to extract common words from a string, including the ability to filter stop words.
     *
     * @author Philip Norton - http://www.hashbangcode.com/
     * @param string | $string
     * @param array
     */

    public static function extractCommonWords($string)
    {
      $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www');

      $string = strip_tags($string);
      $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
      $string = trim($string); // trim the string
      $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
      $string = strtolower($string); // make it lowercase

      preg_match_all('/\b.*?\b/i', $string, $matchWords);
      $matchWords = $matchWords[0];

      foreach ( $matchWords as $key=>$item ) {
          if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {
              unset($matchWords[$key]);
          }
      }
      $wordCountArr = array();
      if ( is_array($matchWords) ) {
          foreach ( $matchWords as $key => $val ) {
              $val = strtolower($val);
              if ( isset($wordCountArr[$val]) ) {
                  $wordCountArr[$val]++;
              } else {
                  $wordCountArr[$val] = 1;
              }
          }
      }
      arsort($wordCountArr);
      $wordCountArr = array_slice($wordCountArr, 0, 10);
      return $wordCountArr;
    }

}