<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 26.08.2015
 * Time: 01:04
 */

namespace tdt4237\webapp\models;

class Post
{
    protected $postId;
    protected $author;
    protected $title;
    protected $content;
    protected $date;
    protected $ispayed;
    protected $isanswered;



    public function getPostId() {
        return $this->postId;

    }

    public function setPostId($postId) {
        $this->postId = $postId;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setPayed($ispayed){
        $this->ispayed = $ispayed;
        return $this;
    }

    public function setAnswered($isanswered){
        $this->isanswered = $isanswered;
        return $this;
    }

    public function getPayed(){
        return $this->ispayed;
    }

    public function getAnswered(){
        return $this->isanswered;
    }










}