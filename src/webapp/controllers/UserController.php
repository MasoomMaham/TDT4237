<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;
use tdt4237\webapp\validation\sqlValidation;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            return $this->render('newUserForm.twig', []);
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request  = $this->app->request;
        $username = strtolower($request->post('user'));
        $usernameValidation = sqlValidation::whiteBlackListSQL($username);
        $password = $request->post('pass');
        $passwordValidation = sqlValidation::whiteBlackListSQL($password);
        $fullname = $request->post('fullname');
        $fullnameValidation = sqlValidation::whiteBlackListSQL($fullname);
        $address = $request->post('address');
        $addressValidation = sqlValidation::whiteBlackListSQL($address);
        $postcode = $request->post('postcode');
        $postcodeValidation = sqlValidation::whiteBlackListSQL($postcode);

        $d_user = $this->userRepository->getNameByUsername($username);

        if($usernameValidation === false || $passwordValidation === false || $fullnameValidation === false || $addressValidation === false || $postcodeValidation === false)
        {
            $this->app->flash('info', 'Malicious code spotted in the input fields!');
            $this->app->redirect('/user/new');
        }

        if(isset($d_user)){
            $this->app->flash('info', 'Username has already been taken');
            $this->app->redirect('/user/new');
        }


        $validation = new RegistrationFormValidation($username, $password, $fullname, $address, $postcode);

        if ($validation->isGoodToGo()) {
            $password = $password;
            $password = $this->hash->make($password);
            $user = new User($username, $password, $fullname, $address, $postcode);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = join("<br>\n", $validation->getValidationErrors());
        $this->app->flashNow('error', $errors);
        $this->render('newUserForm.twig', ['username' => $username]);
    }

    public function all()
    {
        $this->render('users.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->app->redirect('/');
    }

    public function show($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");

        } else {
            $user = $this->userRepository->findByUser($username);

            if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

                $this->render('showuser.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            } else if ($this->auth->check()) {

                $this->render('showuserlite.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            }
        }
    }

    public function showUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();

        $this->render('edituser.twig', [
            'user' => $this->auth->user()
        ]);
    }

    public function receiveUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $email   = $request->post('email');
        $emailValidation = sqlValidation::whiteBlackListSQL($email);
        $bio     = $request->post('bio');
        $bioValidation = sqlValidation::whiteBlackListSQL($bio);
        $age     = $request->post('age');
        $ageValidation = sqlValidation::whiteBlackListSQL($age);
        $fullname = $request->post('fullname');
        $fullnameValidation = sqlValidation::whiteBlackListSQL($fullname);
        $address = $request->post('address');
        $addressValidation = sqlValidation::whiteBlackListSQL($address);
        $postcode = $request->post('postcode');
        $postcodeValidation = sqlValidation::whiteBlackListSQL($postcode);
        $bank = $request->post('bank');

        if($emailValidation === false || $bioValidation === false || $fullnameValidation === false || $addressValidation === false || $postcodeValidation === false)
        {
           $this->app->flash('info', 'Malicious code in the input fields!');
           $this->app->redirect('/user/edit');
        }

        $validation = new EditUserFormValidation($email, $bio, $age);
        
        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setBio($bio);
            $user->setAge(new Age($age));
            $user->setFullname($fullname);
            $user->setAddress($address);
            $user->setPostcode($postcode);
            $user->setBank($bank);
            $this->userRepository->save($user);

            $this->app->flashNow('info', 'Your profile was successfully saved.');
            return $this->render('edituser.twig', ['user' => $user]);
        }

        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        $this->render('edituser.twig', ['user' => $user]);
    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }
}
