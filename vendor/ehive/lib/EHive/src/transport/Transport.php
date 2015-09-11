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

require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveFatalServerException.php';
require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveNotFoundException.php';
require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveForbiddenException.php';
require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveUnauthorizedException.php';

class Transport {

	const OAUTH_TOKEN_ENDPOINT_PATH = '/oauth2/v2/token';
	const OAUTH_AUTHORIZATION_ENDPOINT_PATH = '/oauth2/v2/authorize';
	const OAUTH_TOKEN_FILE = '/transport/oauth/oauth.tok';
	const HTTPS_PROTOCOL = 'https://';

	protected $apiUrl;
	protected $clientId;
	protected $clientSecret;
	protected $trackingId;

	protected $retryAttempts = 0;

	public function __construct($clientId, $clientSecret, $trackingId) {
		if (file_exists(EHIVE_API_ROOT_DIR.'/transport/oauth/endpoints.ini') == false) {
			throw new EHiveApiException('Could not find the "/ehive/transport/oauth/endpoints.ini" file. Please check that the file exists and has read permissions enabled.');
		}

		$configuration = parse_ini_file(EHIVE_API_ROOT_DIR.'/transport/oauth/endpoints.ini');

		if ($configuration == false) {
			throw new EHiveApiException('Could not read contents of the "/ehive/transport/oauthe/endpoints.ini" file. Please check that the file has read permissions enabled.');
		}

		$this->apiUrl = $configuration['api_url'];
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->trackingId = $trackingId;
	}

	public function get($path, $queryString='') {
		/**
		 * Initialize the cURL session
 		 */
		$ch = curl_init();

		$uri = $this->apiUrl . $path;

		$completeUrl = $this->createUrl($uri, str_replace(" ", "%20", $queryString));

		$apiAuthorization = $this->getApiAuthorization();
		curl_setopt($ch, CURLOPT_URL, $completeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$headers = array(
							'Content-Type: application/json',
							'Authorization: Basic ' . $apiAuthorization->oauthToken,
							'Client-Id: '. $apiAuthorization->clientId,
							'Grant-Type: '. $apiAuthorization->grantType
						);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		/**
		 * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
		 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		/**
		 * Execute the cURL session
		 */
		$response = curl_exec ($ch);

		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		switch ($httpResponseCode) {
			case 500:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveFatalServerException($ehiveStatusMessage->toString());
				break;
			case 404:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveNotFoundException($ehiveStatusMessage->toString());
				break;
			case 403:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveForbiddenException($ehiveStatusMessage->toString());
				break;
			case 401:
				curl_close ($ch);

				if ($this->retryAttempts < 3) {
					$apiAuthorization = $this->getAuthenticated();

					if (is_null($apiAuthorization->oauthToken)) throw new EHiveApiException('OAuth Token is missing after the server said it has vended it. This is a fatal error and should be reported at http://forum.ehive.com.');

					$this->retryAttempts = $this->retryAttempts + 1;
					$json = $this->get($path, $queryString);
				} else {
					$json = json_decode($response);
					curl_close ($ch);

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
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveApiException($ehiveStatusMessage->toString());
				break;
		}
		return $json;
	}

	public function post($path, $content) {
		/**
		 * Initialize the cURL session
		 */
		$ch = curl_init();

		$uri = $this->apiUrl . $path;

		$completeUrl = $this->createUrl($uri);

		$apiAuthorization = $this->getApiAuthorization();

		$content = json_encode($content);

		curl_setopt($ch, CURLOPT_URL, $completeUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$headers = array(
							'Content-Type: application/json',
							'Authorization: Basic ' . $apiAuthorization->oauthToken,
							'Client-Id: '. $apiAuthorization->clientId,
							'Grant-Type: '. $apiAuthorization->grantType
						);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		/**
		 * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
		 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		/**
		 * Execute the cURL session
		 */
		$response = curl_exec ($ch);

		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		switch ($httpResponseCode) {
			case 500:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveFatalServerException($ehiveStatusMessage->toString());
				break;
			case 404:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveNotFoundException($ehiveStatusMessage->toString());
				break;
			case 403:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveForbiddenException($ehiveStatusMessage->toString());
				break;
			case 401:
				curl_close ($ch);

				if ($this->retryAttempts < 3) {
					$apiAuthorization = $this->getAuthenticated();

					if (is_null($apiAuthorization->oauthToken)) throw new EHiveApiException('OAuth Token is missing after the server said it has vended it. This is a fatal error and should be reported at http://forum.ehive.com.');

					$this->retryAttempts = $this->retryAttempts + 1;
					$json = $this->post($path, $content);
				} else {
					$json = json_decode($response);
					curl_close ($ch);

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
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveApiException('An invalid HTTP error code has been returned by the server-side API. This message has originated from the Transport.php file. This is likely to be due to an invalid URL to which the "POST" request has been sent. Please check that the URLs in the /ehive/transport/oauth/endpoints.ini file and the version of the client API you are using are up to date. These can be checked on http://forum.ehive.com. HTTP Response Code: ' . $httpResponseCode);
				break;
		}

		return $json;
	}

	public function delete($path, $queryString='') {
		/**
		 * Initialize the cURL session
		 */
		$ch = curl_init();

		$uri = $this->apiUrl . $path;

		$completeUrl = $this->createUrl($uri, $queryString);

		$apiAuthorization = $this->getApiAuthorization();

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_URL, $completeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$headers = array(
							'Content-Type: application/json',
							'Authorization: Basic ' . $apiAuthorization->oauthToken,
							'Client-Id: '. $apiAuthorization->clientId,
							'Grant-Type: '. $apiAuthorization->grantType
						);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		/**
		 * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
		 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		/**
		 * Execute the cURL session
		 */
		$response = curl_exec ($ch);

		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		switch ($httpResponseCode) {
			case 500:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveFatalServerException($ehiveStatusMessage->toString());
				break;
			case 404:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);
				throw new EHiveNotFoundException($ehiveStatusMessage->toString());
				break;
			case 403:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveForbiddenException($ehiveStatusMessage->toString());
				break;
			case 401:
				curl_close ($ch);

				if ($this->retryAttempts < 3) {
					$apiAuthorization = $this->getAuthenticated();

					if (is_null($apiAuthorization->oauthToken)) throw new EHiveApiException('OAuth Token is missing after the server said it has vended it. This is a fatal error and should be reported at http://forum.ehive.com.');

					$this->retryAttempts = $this->retryAttempts + 1;
					$json = $this->delete($path, $queryString);
				} else {
					$json = json_decode($response);
					curl_close ($ch);

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
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveApiException('An invalid HTTP error code has been returned by the server-side API. This message has originated from the Transport.php file. This is likely to be due to an invalid URL to which the "DELETE" request has been sent. Please check that the URLs in the "/ehive/transport/oauth/endpoints.ini" file and the version of the client API you are using are up to date. These can be checked on http://forum.ehive.com. HTTP Response Code: ' . $httpResponseCode );
				break;
		}

		return $json;
	}

	protected function getAuthenticated() {
		$tokenEndpointUrl = $this->apiUrl . Transport::OAUTH_TOKEN_ENDPOINT_PATH;
		$authorizaitonEndpointUrl = $this->apiUrl . Transport::OAUTH_AUTHORIZATION_ENDPOINT_PATH;

		/**
		 * Initialize the cURL session
		 */
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $authorizaitonEndpointUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		$headers = array(
							'Content-Type: application/x-www-form-urlencoded',
							'Authorization: OAuth',
							'Client-Id: '.  $this->clientId,
							'Client-Secret: '.  $this->clientSecret,
							'Grant-Type: client_credentials'
						);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		/**
		 * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
		 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		/**
		 * Execute the cURL session
		 */
		$response = curl_exec ($ch);

		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		switch($httpResponseCode) {
			case 500:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveFatalServerException($ehiveStatusMessage->toString());
				break;
			case 404:
				throw new EHiveNotFoundException("Resource Not Found. Check that your request URL is valid.");
				break;
			case 403:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveForbiddenException($ehiveStatusMessage->toString());
			case 401:
				$json = json_decode($response);
					curl_close ($ch);

					require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

					$ehiveStatusMessage = new EHiveStatusMessage($json);

					throw new EHiveUnauthorizedException($ehiveStatusMessage->toString());

			case 303:
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$header = substr($response, 0, $header_size);

				$headersArray = explode("\n", $header);
				$headers = array();

				foreach ($headersArray as $header) {
					$headerParts = explode(": ", $header);
					$headers[$headerParts[0]] = $header;
				}

				curl_close ($ch);

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $tokenEndpointUrl);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
														"Content-Type: application/x-www-form-urlencoded",
														str_replace("\r", "", $headers["Access-Grant"]),
														str_replace("\r", "", $headers["Authorization"]),
														str_replace("\r", "", $headers["Client-Id"]),
														str_replace("\r", "", $headers["Grant-Type"])
													));

				/**
				 * Ask cURL to return the contents in a variable instead of simply echoing them to the browser.
				*/
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				/**
				 * Execute the cURL session
				*/
				$response = curl_exec ($ch);

				$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				switch ($httpResponseCode) {
					case 500:
						$json = json_decode($response);
						curl_close ($ch);

						require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

						$ehiveStatusMessage = new EHiveStatusMessage($json);

						throw new EHiveFatalServerException($ehiveStatusMessage->toString());
						break;
					case 404:
						curl_close ($ch);
						throw new EHiveNotFoundException("Resource Not Found. Check that your request URL is valid.");
						break;
					case 403:
						$json = json_decode($response);
						curl_close ($ch);

						require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

						$ehiveStatusMessage = new EHiveStatusMessage($json);

						throw new EHiveForbiddenException($ehiveStatusMessage->toString());
						break;
					case 401:
						$json = json_decode($response);
						curl_close ($ch);

						require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

						$ehiveStatusMessage = new EHiveStatusMessage($json);

						throw new EHiveUnauthorizedException($ehiveStatusMessage->toString());
						break;
					case 200:
						$json = json_decode($response);

						curl_close ($ch);

						$apiAthorization = $this->asApiAuthorization($json);

						$path = EHIVE_API_ROOT_DIR . Transport::OAUTH_TOKEN_FILE;

						if (!$handle = fopen($path, 'w')) {
							throw new EHiveApiException('Cannot open "{$path}" file after retrieving an oauth token from the server-side API. Please ensure that the file has read and write permissions enabled. MESSGAE FROM SERVER: '.$ehiveStatusMessage->errorMessage);
						}

						if (!fwrite($handle, $apiAthorization->oauthToken)) {
							fclose($handle);
							throw new EHiveApiException('Cannot write to the file "{$path}" after retrieving an oauth token from the server-side API. Please ensure that the file has write permissions enabled. MESSGAE FROM SERVER: '.$ehiveStatusMessage->errorMessage);
						}

						fclose($handle);

						return $apiAthorization;
					default:
						$json = json_decode($response);
						curl_close ($ch);

						require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

						$ehiveStatusMessage = new EHiveStatusMessage($json);

						throw new EHiveApiException($ehiveStatusMessage->toString());
						break;
				}

				return $json;
			default:
				$json = json_decode($response);
				curl_close ($ch);

				require_once EHIVE_API_ROOT_DIR.'/exceptions/EHiveStatusMessage.php';

				$ehiveStatusMessage = new EHiveStatusMessage($json);

				throw new EHiveApiException('An invalid HTTP error code has been returned by the server-side API. This message has originated from the Transport.php file. This is likely to be due to an invalid URL to which the "GET" request has been sent. Please check that the URLs in the "/ehive/transport/oauth/endpoints.ini" file and the version of the client API you are using are up to date. These can be checked on http://forum.ehive.com. HTTP Response Code: ' . $httpResponseCode);
				break;
		}
	}

	protected function getApiAuthorization() {

		// OAuth Token
		if (file_exists(EHIVE_API_ROOT_DIR.Transport::OAUTH_TOKEN_FILE) == false) {
			throw new EHiveApiException('Could not find "/ehive/transport/oauth/oauth.tok" file. Please ensure that the file exists and that it has read and write permissions enabled.');
		}

		$path = EHIVE_API_ROOT_DIR.Transport::OAUTH_TOKEN_FILE;

		if (!$handle = fopen($path, 'r')) {
			fclose($handle);
			throw new EHiveApiException('Cannot open "/ehive/transport/oauth/oauth.tok" file after retrieving an oauth token from the server-side API. Please ensure that the file exists and has read and write permissions enabled. Method: getAuthorized()');
		} else {
			$oauthToken = fgets($handle);
			fclose($handle);
		}

		require_once EHIVE_API_ROOT_DIR.'/transport/oauth/ApiAuthorization.php';

		$apiAuthorization = new ApiAuthorization();

		$apiAuthorization->clientId = $this->clientId;
		$apiAuthorization->oauthToken = $oauthToken;
		$apiAuthorization->grantType = 'authorization_code';

		return $apiAuthorization;
	}

	protected function createUrl($uri, $queryString='') {

		if (empty($queryString)) {
			$uri .=	'?trackingId=' . $this->trackingId;
		} else {
			$uri .=	'?' . $queryString . '&trackingId=' . $this->trackingId;
		}

		return self::HTTPS_PROTOCOL.$uri;
	}

	protected function asApiAuthorization($json){
		require_once EHIVE_API_ROOT_DIR.'/transport/oauth/ApiAuthorization.php';

		$apiAuthorization = new ApiAuthorization();

		$apiAuthorization->clientId 		= isset($json->clientId) 	? $json->clientId 		: null;
		$apiAuthorization->oauthToken  		= isset($json->oauthToken) 	? $json->oauthToken  	: null;
		$apiAuthorization->grantType   		= isset($json->grantType)  	? $json->grantType   	: null;

		return $apiAuthorization;
	}
}

?>