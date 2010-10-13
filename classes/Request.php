<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Request class parses action and signup id from HTTP request
 *
 * @author mikko
 */
class Request {

    private $isAdmin = false;
    private $action;
    private $signupId;

    public function Request($request) {
        
        $requestParts = explode("?", $request);

        $requestBody = $requestParts[0];

        if(count($requestParts) > 1){
            // TODO Do something with the query part?
            $requestQuery = $requestParts[1];
        }

        $requestBodyParts = explode("/", $requestBody);

        $firstPart = $requestBodyParts[0];

        if($firstPart === "admin"){
            $this->isAdmin = true;

            if(count($requestBodyParts) > 1){
                $this->action = $requestBodyParts[1];
            }

            if(count($requestBodyParts) > 2){
                $this->signupId = $requestBodyParts[2];
            }

        } else {
            $this->action = $firstPart;

            if(count($requestBodyParts) > 1){
                $this->signupId = $requestBodyParts[1];
            }
        }
    }

    public function isAdmin() {
        return $this->isAdmin;
    }

    public function getAction() {
        return $this->action;
    }

    public function getSignupId() {
        return $this->signupId;
    }
}
