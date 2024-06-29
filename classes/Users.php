<?php

require_once 'LearnUponAPIBase.php';

class Users extends LearnUponAPIBase {

    // API Connection
    public function __construct($api_key, $domain)
    {
        parent::__construct($api_key, $domain); // Call the parent constructor
    }

    // Gets all users in a portal
    Public function get_users()
    {
        
        try
        {
            $url = $this->api_url . "/users";
            //$option = array("exceptions" => false);
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
    // Checks for a particular user (by Email)
    Public function get_user_by_email($email)
    {
        
        try
        {
            $url = $this->api_url . "/users/search?email=". urlencode($email);
            //$option = array("exceptions" => false);
            $header = array("Authorization"=>"Basic " . $this->auth_key);
            $response = $this->client->get($url, array("headers" => $header));
            $result = json_decode($response->getBody()->getContents());
            //var_dump($result); // Debug output
            return $result;
        }
        catch (RequestException $e)
        {
            $response = $this->StatusCodeHandling($e);
            return $response;
        }

    }
    // Creates the user
    public function create_user($email) {

        try {
            // User does not exist, proceed with creating the user
            $url = $this->api_url . "/users";
            $header = array(
                "Authorization" => "Basic " . $this->auth_key,
                "Content-Type" => "application/json"
            );
            $body = json_encode(array(
                'User' => array(
                    'email' => $email,
                    'password' => 'Welcome1%' // Ensure required fields are populated
                )
            ));

            $response = $this->client->post($url, array("headers" => $header, "body" => $body));
            $result = json_decode($response->getBody()->getContents());
            return $result;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $message = $response->getBody()->getContents();
                throw new Exception("Error $statusCode: $message");
            } else {
                throw new Exception("Request failed: " . $e->getMessage());
            }
        }
    }

    // the rest of my functions

}

?>