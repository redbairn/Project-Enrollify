<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class LearnUponAPIBase {

    protected $client = null;
    protected $auth_key;
    protected $api_url;

    public function __construct($api_key, $domain)
    {
        $this->auth_key = base64_encode($api_key);
        $this->api_url = "https://{$domain}.learnupon.com/api/v1";
        $this->client = new Client();
    }

    protected function StatusCodeHandling($e) {
        $statusCode = $e->getResponse()->getStatusCode();
        $response = json_decode($e->getResponse()->getBody(true)->getContents());

        switch ($statusCode) {
            case 400:
                return $response;
            case 422:
                return $response;
            case 500:
                return $response;
            case 401:
                return $response;
            case 403:
                return $response;
            default:
                return $response;
        }
    }
}
?>
