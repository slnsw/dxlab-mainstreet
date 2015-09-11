<?php

require_once(EHIVE_API_ROOT_DIR . '/transport/Transport.php');

class Multi_Transport extends Transport
{

    public function __construct($clientId, $clientSecret, $trackingId)
    {
        parent::__construct($clientId, $clientSecret, $trackingId);
    }

    public function getMultiple($path, array $queries)
    {
        if(empty($path)){
            return false;
        }
        /**
         * Initialize the cURL session
         */
        $ch = array();
        $mh = curl_multi_init();
        $responses = array();

        // This ensures that both the path and queries parameters exists as arrays in order to provide a form of alignment when iterating through the list of queries. It also allows for path and query pairing.
        if(is_array($path)
            && empty($queries)){
            $queries = array_pad(array(), count($path), '');
        }elseif(!is_array($path)
                    && !empty($queries)){
            $path = array_pad(array(), count($queries), $path);
        }

        $apiAuthorization = $this->getApiAuthorization();

        foreach($queries as $k => $query){

            $url = $this->apiUrl . $path[$k];
            $ch[$k] = curl_init();
            $query = $this->createUrl($url, str_replace(" ", "%20", $query));

            curl_setopt($ch[$k], CURLOPT_URL, $query);
            curl_setopt($ch[$k], CURLOPT_HEADER, 0);

            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic ' . $apiAuthorization->oauthToken,
                'Client-Id: '. $apiAuthorization->clientId,
                'Grant-Type: '. $apiAuthorization->grantType
            );

            curl_setopt($ch[$k], CURLOPT_HTTPHEADER, $headers);

            /**
             * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
             */
            curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $ch[$k]);
        }

        // Initialise a variable to store a boolean status for activity for the current cURL request
        $active = NULL;
        do{
            $result = curl_multi_exec($mh, $active);
        }while($result == CURLM_CALL_MULTI_PERFORM);

        while($active
            && $result == CURLM_OK){
            // The curl_multi_select is tempremental, and it's purpose is to wait until the request is complete for a child handle, however delaying program execution is a viable and recommended solution.
            // if(curl_multi_select($mh) != -1){
                usleep(10000);
                do{
                    $mrc = curl_multi_exec($mh, $active);
                }while($mrc == CURLM_CALL_MULTI_PERFORM);
            // }
        }

        foreach($ch as $k => $v){
            $httpResponseCode = curl_getinfo($v, CURLINFO_HTTP_CODE);
            $response = curl_multi_getcontent($v);
            switch ($httpResponseCode) {
                case 500:
                    $json = json_decode($response);

                    require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

                    $ehiveStatusMessage = new EHiveStatusMessage($json);

                    throw new EHiveFatalServerException($ehiveStatusMessage->toString());
                    break;
                case 404:
                    $json = json_decode($response);

                    require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

                    $ehiveStatusMessage = new EHiveStatusMessage($json);

                    throw new EHiveNotFoundException($ehiveStatusMessage->toString());
                    break;
                case 403:
                    $json = json_decode($response);

                    require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

                    $ehiveStatusMessage = new EHiveStatusMessage($json);

                    throw new EHiveForbiddenException($ehiveStatusMessage->toString());
                    break;
                case 401:

                    if ($this->retryAttempts < 3) {
                        $apiAuthorization = $this->getAuthenticated();

                        if (is_null($apiAuthorization->oauthToken)) throw new EHiveApiException('OAuth Token is missing after the server said it has vended it. This is a fatal error and should be reported at http://forum.ehive.com.');

                        $this->retryAttempts = $this->retryAttempts + 1;
                        $json = $this->get($path, $queries[$k]);
                    } else {
                        $json = json_decode($response);

                        require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

                        $ehiveStatusMessage = new EHiveStatusMessage($json);

                        throw new EHiveUnauthorizedException($ehiveStatusMessage->toString());
                    }
                    break;
                case 200:
                    $json = json_decode($response);
                    break;
                default:
                    $json = json_decode($response);

                    require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

                    $ehiveStatusMessage = new EHiveStatusMessage($json);

                    throw new EHiveApiException($ehiveStatusMessage->toString());
                    break;
            }
            $responses[$k] = $json;
            curl_multi_remove_handle($mh, $v);
        }
        curl_multi_close($mh);
        return $responses;
    }
}