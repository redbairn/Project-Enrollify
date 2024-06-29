<?php

require_once 'LearnUponAPIBase.php';

class Courses extends LearnUponAPIBase {

    // API Connection
    public function __construct($api_key, $domain)
    {
        parent::__construct($api_key, $domain); // Call the parent constructor
    }

    Public function get_courses()
    {
        
        try
        {
            $url = $this->api_url . "/courses";
            //$option = array("exceptions" => false);
            $header = array("Authorization"=>"Basic " . $this->auth_key);
            $response = $this->client->get($url, array("headers" => $header));
            //$result = json_decode($response->getBody()->getContents());
            $result = json_decode($response->getBody()->getContents());
            return $result;
        }
        catch (RequestException $e)
        {
            $response = $this->StatusCodeHandling($e);
            return $response;
        }

    }

    // the rest of my functions

}

?>