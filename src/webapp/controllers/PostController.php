<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;
use tdt4237\webapp\validation\sqlValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $posts = $this->postRepository->all();

        $posts->sortByDate();
        $this->render('posts.twig', ['posts' => $posts]);
    }

    public function show($postId)
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view this post.");
            $this->app->redirect('/');
        }

        $post = $this->postRepository->find($postId);
        $comments = $this->commentRepository->findByPostId($postId);
        $request = $this->app->request;
        $message = htmlentities($request->get('msg'));
        if(sqlValidation::whiteBlackListSQL($message) === false)
        {
            $this->app->flash('info', 'Malicious code spotted in the in URL!');
            $this->app->redirect('/post');
        }
        $variables = [];


        if($message) {
            $variables['msg'] = $message;

        }


        $this->render('showpost.twig', [
            'post' => $post,
            'comments' => $comments,
            'flash' => $variables
        ]);

    }

    public function addComment($postId)
    {

        if(!$this->auth->guest()) {

            $textComment = htmlentities($this->app->request->post("text"));
            if(sqlValidation::whiteBlackListSQL($textComment) === false)
            {
                $this->app->redirect('/posts/' . $postId);            
            }

            $comment = new Comment();
            $comment->setAuthor($_SESSION['user']);
            $comment->setText($textComment);
            $comment->setDate(date("dmY"));
            $comment->setPost($postId);
            $this->commentRepository->save($comment);
            $this->app->redirect('/posts/' . $postId);
        }
        else {
            $this->app->redirect('/login');
            $this->app->flash('info', 'you must log in to do that');
        }

    }

    public function showNewPostForm()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            $this->render('createpost.twig', ['username' => $username]);
        } else {

            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $title = $request->post('title');
            $titleValidation = sqlValidation::whiteBlackListSQL($title);
            $content = $request->post('content');
            $contentValidation = sqlValidation::whiteBlackListSQL($content);
            $author = $request->post('author');
            $authorValidation = sqlValidation::whiteBlackListSQL($author);
            if($titleValidation === false || $contentValidation === false || $authorValidation === false)
            {
                $this->app->flash('info', 'Malicious code in the input fields!');
                $this->app->redirect('/posts/new');
            }
            $date = date("dmY");

            $validation = new PostValidation($title, $author, $content);
            if ($validation->isGoodToGo()) {
                echo '<script type="text/javascript">', 
                'document.getElementById("author").readOnly = true;',
                '</script>';
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg="Post succesfully posted');
            }
        }

            $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
            $this->app->render('createpost.twig');
            // RENDER HERE

    }
}

