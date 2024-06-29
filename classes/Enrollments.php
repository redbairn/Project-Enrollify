<?php

require_once 'LearnUponAPIBase.php';

class Enrollments extends LearnUponAPIBase {

    // API Connection
    public function __construct($api_key, $domain)
    {
        parent::__construct($api_key, $domain); // Call the parent constructor
    }

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

    // the rest of my functions

}

?>