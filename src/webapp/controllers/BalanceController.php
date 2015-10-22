<?php
/**
 * Created by IntelliJ IDEA.
 * User: salahuddin
 * Date: 22.10.15
 * Time: 22:31
 */
namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class BalanceController extends Controller
{

    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the balance page.");
            $this->app->redirect('/');
        }


        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('balance.twig', $variables);
    }



}
