<?php

require_once 'LearnUponAPIBase.php';

class Enrollments extends LearnUponAPIBase {

    // API Connection
    public function __construct($api_key, $domain)
    {
        parent::__construct($api_key, $domain); // Call the parent constructor
    }

    // Used to create an individual enrollment in LearnUpon
    public function create_enrollment($email, $course_id)
    {
        try
        {
            $url = $this->api_url . "/enrollments";
            $header = array(
                "Authorization" => "Basic " . $this->auth_key, 
                "Content-Type" => "application/json"
            );
            $body = json_encode(array(
                'Enrollment' => array(
                        "email" => $email, 
                        "course_id" => $course_id
                    )
            ));
            $response = $this->client->post($url, array("headers" => $header, "body" => $body));
            $result = json_decode($response->getBody()->getContents());
            return $result;
        }
        catch (RequestException $e)
        {
            return $this->StatusCodeHandling($e);
        }
    }

    // Checks for a enrollments (by the user's Email)
    Public function get_enrollments_by_email($email)
    {
        try
        {
            $url = $this->api_url . "/enrollments/search?email=". urlencode($email);
            $header = array("Authorization"=>"Basic " . $this->auth_key);
            $response = $this->client->get($url, array("headers" => $header));
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