<?php

namespace tdt4237\webapp;

use Exception;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;

class Auth
{

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, Hash $hash)
    {
        $this->userRepository = $userRepository;
        $this->hash           = $hash;
    }

    public function checkCredentials($username, $password)
    {
        $user = $this->userRepository->findByUser($username);

        if ($user === false) {
            return false;
        }

        return $this->hash->check($password, $user->getHash());
    }

    public function checkLastTimeFailed($username)
    {
        $time = $this->userRepository->getTimebyUsername($username, time());
        $isdoctor = $this->userRepository->checkDoctor($username);
        $hasBank= $this->userRepository->checkBank($username);


        if(isset($isdoctor)){
           $_SESSION['isdoctor'] = 1;
        }
        else{
            $_SESSION['isdoctor'] = 0;
        }

        if(isset($hasBank)){
            $_SESSION['hasbank'] = 1;
        }
        else{
            $_SESSION['hasbank'] = 0;
        }

        if ($time === false) {
            return false;
        }
        $attempts = $this->userRepository->getfailed_attempts($username);
        if($attempts>3){
            $this->userRepository->reset_failed_attempts($username);
            $this->userRepository->updateDbTime($username);
            return false;
        }
        $this->userRepository->setfailed_attempts($username);

        return true;
    }

    public function checkBalance($username){
        $balance = $this->userRepository->checkBalance($username);
        return $balance;
    }


    /**
     * Check if is logged in.
     */
    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function getUsername() {
        if(isset($_SESSION['user'])){
        return $_SESSION['user'];
        }
    }

    /**
     * Check if the person is a guest.
     */
    public function guest()
    {
        return $this->check() === false;
    }

    /**
     * Get currently logged in user.
     */
    public function user()
    {
        if ($this->check()) {
            return $this->userRepository->findByUser($_SESSION['user']);
        }

        throw new Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    public function isAdmin()
    {
        if ($this->check()) {
            return $_SESSION['isadmin'] === 'yes';
        }

        throw new Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    public function logout()
    {
        session_destroy();
    }

}
