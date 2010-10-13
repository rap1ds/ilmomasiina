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
        $requestParts = explode("/", $request);

        $firstPart = $requestParts[0];

        if($firstPart === "admin"){
            $this->isAdmin = true;

            if(count($requestParts) > 1){
                $this->action = $requestParts[1];
            }

            if(count($requestParts) > 2){
                $this->signupId = $requestParts[2];
            }

        } else {
            $this->action = $firstPart;

            if(count($requestParts) > 1){
                $this->signupId = $requestParts[1];
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
