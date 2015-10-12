<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    
    private $validationErrors = [];
    
    public function __construct($username, $password, $fullname, $address, $postcode)
    {
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        if((strlen($password))<8){
            $this->validationErrors[] = 'Password does not meet the minimum length (8 characters)';
        }

        if((strlen($password))>32){
            $this->validationErrors[] = 'Password length can not exceed 32 characters';
        }

        if((strlen($username))>16){
            $this->validationErrors[] = 'Username length can not exceed 16 characters';
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != "4") {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
    }
}
