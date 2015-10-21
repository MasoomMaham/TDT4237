<?php
namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;
use tdt4237\webapp\validation\sqlValidation;
use tdt4237\webapp;


class LoginController extends Controller
{



    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('login.twig', []);
    }


    public function login()
    {
        if (!$this->auth->guest()) {
            $this->app->flash('info', "You are already logged in");
            $this->app->redirect('/');
        }
        $request = $this->app->request;

        $user    = strtolower($request->post('user'));
        $pass    = $request->post('pass');
        if(sqlValidation::whiteBlackListSQL($user) === false)
        {
            $this->app->flash('info', 'Illegal characters in the input fields!');
            $this->app->redirect('/login');
        }


        if (!$this->auth->checkLastTimeFailed($user)) {
            $this->render('banned.twig', []);
            return;
        }


        if ($this->auth->checkCredentials($user, $pass)) {
                $_SESSION['user'] = $user;
                $isAdmin = $this->auth->user()->isAdmin();

                if ($isAdmin) {
                    $_SESSION['isadmin'] = "yes";
                } else {
                    $_SESSION['isadmin'] = "no";
                }

                $this->app->flash('info', "You are now successfully logged in as $user.");
                $this->app->redirect('/');
                return;
        }

            $this->app->flashNow('error', 'Incorrect user/pass combination.');
            $this->render('login.twig', []);
        }
}
