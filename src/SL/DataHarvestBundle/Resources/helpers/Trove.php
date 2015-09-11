<?php

namespace SL\DataHarvestBundle\Resources\helpers;

class Trove
{

    private $_data;

    const TROVE_SEARCH_API_PATH = 'http://api.trove.nla.gov.au/result';
    const TROVE_SEARCH_NEWSPAPER_PATH = 'http://trove.nla.gov.au/newspaper/result';
    const TROVE_SEARCH_API_KEY = '***REMOVED***';

    public function __construct(){
    }

    public function getObjects($params)
    {
        $zebra = new \Zebra_Zebra();
        $this->prepareAPIPaths($params['paths']);
        // Append the maximum number of results to be returned to the query string, this is calculated by dividing the number of paths by the maximum number of results by the number of paths, whilst ensuring an absolute number is appended.
        $iterate = ($params['max'] > 100 ? $params['max'] / 100 : 1);
        $paths = array();
        foreach($params['paths'] as $path){
          for($i = 0; $i < abs(round($iterate / count($params['paths']))); $i++){
            $paths[] = $path . '&n=100&s=' . ($i * 100);
          }
        }
        // Increase the overall timeout for the connection, due to the large number of concurrent and recursive requests this is advisable.
        $zebra->option(array(
          CURLOPT_CONNECTTIMEOUT => 30,
          CURLOPT_TIMEOUT => 40,
          ));
        // Group the paths into chunks.
        $paths = array_chunk($paths, 20);
        $exp = 0;
        // Iterate through the groups and process the paths contained in each, recording the total number of paths for cross-reference.
        foreach($paths as $set){
          $exp += count($set);
          $zebra->get($set, array($this, 'processObjects'));
          usleep(100000);
        }
        // If the number of results does not reflect the total number of paths continue to loop.
        while(count($this->_data['results']) < $exp){
          continue;
        }
        // Once we have the complete data set containing the Zebra_cURL response body's, return ensuring any failed responses are filtered out.
        return array_filter($this->_data['results']);
    }

    /**
     *  A method to replace single/multiple spaces with the encoded equivalent for cURL request paths, and prepend the Trove search API path.
     */

    public function prepareAPIPaths(&$paths)
    {
        foreach($paths as &$path){
            $path = self::TROVE_SEARCH_API_PATH . '?key=' . self::TROVE_SEARCH_API_KEY . '&' . preg_replace('#\s+#', '%20', $path);
        }
    }

    /**
     * A method acting as a per request callback for the Zebra_cURL library.
     */

    public function processObjects($result)
    {
        if(!$result->body){
          $this->_errors[] = implode(', ', $result->response);
        }
        $this->_data['results'][] = $result->body;
    }


    /**
     * A method to generate a relevant search link referencing Trove, with a tag as the primary search criteria.
     *
     * @param $tag | string
     * @param $zone | string
     * @param $args | array
     *
     * @return string
     */

    public static function generateTagPath($tag, $zone, $args)
    {
      if(!$tag
        || empty($tag)){
        return FALSE;
      }
      $path = constant('self::TROVE_SEARCH_' . strtoupper($zone) . '_PATH') . '?q=' . $tag;
      if(!empty($args)){
        foreach($args as $k => $v){
          $path .= '&' . $k . '=' . $v;
        }
      }
      return preg_replace('#\s+#', '%20', $path);
    }

    /**
     * A static method to extract common words from a string, including the ability to filter stop words.
     *
     * @author Philip Norton - http://www.hashbangcode.com/
     * @param string | $string
     * @param array
     */

    public static function extractCommonWords($string, $limit = 10)
    {
      $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www');

      $string = strip_tags($string);
      $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
      $string = trim($string); // trim the string
      $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
      $string = strtolower($string); // make it lowercase

      preg_match_all('/\b[a-zA-Z]*?\b/i', $string, $matchWords);
      $matchWords = $matchWords[0];

      foreach($matchWords as $key => $item){
          if($item == '' || in_array(strtolower($item), $stopWords)
            || strlen($item) <= 3 ){
              unset($matchWords[$key]);
          }
      }
      $wordCountArr = array();
      if(is_array($matchWords)){
          foreach($matchWords as $key => $val){
              $val = strtolower($val);
              if(isset($wordCountArr[$val])){
                  $wordCountArr[$val]++;
              }else{
                  $wordCountArr[$val] = 1;
              }
          }
      }
      arsort($wordCountArr);
      $wordCountArr = array_slice($wordCountArr, 0, $limit);
      return $wordCountArr;
    }

}