<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public static function create($id, $author, $title, $content, $date, $payed, $answered)
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date)
            ->setpayed($payed)
            ->setAnswered($answered);
    }

    public function find($postId)
    {
        $sql  = "SELECT * FROM posts WHERE postId = $postId";
        $result = $this->db->query($sql);
        $row = $result->fetch();

        if($row === false) {
            return false;
        }


        return $this->makeFromRow($row);
    }

    public function all()
    {
        $sql   = "SELECT * FROM posts";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();

        if(count($fetch) == 0) {
            return false;
        }

        print_r(array_map([$this, 'makeFromRow'], $fetch));
        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date'],
            $row['ispayed'],
            $row['isanswered']
        );
       //  $this->db = $db;
    }

    public function deleteByPostid($postId)
    {
        return $this->db->exec(
            sprintf("DELETE FROM posts WHERE postid='%s';", $postId));
    

    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $author = $post->getAuthor();
        $content = $post->getContent();
        $date    = $post->getDate();
        $payed = $post->getpayed();
        $answered = $post->getanswered();

        if ($post->getPostId() === null) {
            $query = "INSERT INTO posts (title, author, content, date, payed, answered) "
                . "VALUES ('$title', '$author', '$content', '$date','$payed', '$answered')";
        }

        $this->db->exec($query);
        return $this->db->lastInsertId();
    }
}
