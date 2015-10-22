<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const INSERT_QUERY   = "INSERT INTO users(user, pass, email, age, bio, isadmin, fullname, address, postcode) VALUES('%s', '%s', '%s' , '%s' , '%s', '%s', '%s', '%s', '%s')";
    const UPDATE_QUERY   = "UPDATE users SET email='%s', age='%s', bio='%s', isadmin='%s', fullname ='%s', address = '%s', postcode = '%s', bank = '%d' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";
    const UPDATE_TIME  = "UPDATE users SET UNIX_TIMESTAMP='%d'WHERE user='%s'";
    const COMPARE_TIME   = "SELECT * FROM users WHERE user='%s' and UNIX_TIMESTAMP<'%d'";
    const FAILED_ATTEMPTS  = "select * from users where user='%s'";// and FAILED_ATTEMPTS>3";
    const UPDATE_ATTEMPTS  = "UPDATE users SET FAILED_ATTEMPTS= FAILED_ATTEMPTS + 1 WHERE user='%s'";
    const RESET_ATTEMPTS  = "UPDATE users SET FAILED_ATTEMPTS=0 WHERE user='%s'";
    const CHECK_IS_DOCTOR   = "SELECT * FROM users WHERE user='%s' and isdoctor=1";
    const UPDATE_ISANSWERED  = "UPDATE posts SET isanswered = 1 WHERE postId='%d'";
    const CHECK_IS_BANK   = "SELECT * FROM users WHERE user='%s' and bank>1";
    const CHECK_BALANCE   = "SELECT * FROM users WHERE user='%s'";
    const UPDATE_BALANCE  = "UPDATE users SET balance = balance + 10 WHERE user='%s'";
    const UPDATE_BALANCE2  = "UPDATE users SET balance = balance - 10 WHERE user='%s'";
    const FIND_POST_AUTHOR  = "SELECT * FROM posts WHERE postId='%d'";




    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {
        $query = sprintf(self::FIND_FULL_NAME, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['fullname'];
    }

    public function getPostAuthor($postId)
    {
        $query = sprintf(self::FIND_POST_AUTHOR, $postId);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['author'];
    }

    public function findByUser($username)
    {
        $query  = sprintf(self::FIND_BY_NAME, $username);
        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        
        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function getTimebyUsername($username, $time)
    {
        $query = sprintf(self::COMPARE_TIME, $username, $time);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();

        if($row === 0){
            return false;
        }
        return true;
    }

    public function getfailed_attempts($username)
    {
        $query = sprintf(self::FIND_FULL_NAME, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['FAILED_ATTEMPTS'];

    }

    public function setfailed_attempts($username)
    {
        $query = sprintf(
            self::UPDATE_ATTEMPTS, $username
        );
        $this->pdo->exec($query);
    }

    public function reset_failed_attempts($username)
    {
        $query = sprintf(
            self::RESET_ATTEMPTS, $username
        );
        $this->pdo->exec($query);
    }

    public function setIsanswered($postId)
    {
        $query = sprintf(
            self::UPDATE_ISANSWERED, $postId
        );
        $this->pdo->exec($query);

    }

    public function setbalance($username)
    {
        $query = sprintf(
            self::UPDATE_BALANCE, $username
        );
        $this->pdo->exec($query);

    }

    public function setAuthorbalance($username)
    {
        $query = sprintf(
            self::UPDATE_BALANCE2, $username
        );
        $this->pdo->exec($query);

    }

    public function updateDbTime($username)
    {
        $time = time()+30;
        $query = sprintf(
            self::UPDATE_TIME, $time, $username
        );
        $this->pdo->exec($query);
    }

    public function deleteByUsername($username)
    {
        return $this->pdo->exec(
            sprintf(self::DELETE_BY_NAME, $username)
        );
    }

    public function setDoctor($username)
    {
        return $this->pdo->exec(
            sprintf("UPDATE users SET isdoctor = '1' WHERE user = '%s';", $username));
    }

    public function checkDoctor($username)
    {
        $query = sprintf(self::CHECK_IS_DOCTOR, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['isdoctor'];
    }

    public function all()
    {
        $rows = $this->pdo->query(self::SELECT_ALL);
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $query = sprintf(
            self::INSERT_QUERY, $user->getUsername(), $user->getHash(), $user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode()
        );

        return $this->pdo->exec($query);
    }

    public function saveExistingUser(User $user)
    {
        $query = sprintf(
            self::UPDATE_QUERY, $user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode(), $user->getBank(), $user->getUserId()
        );

        return $this->pdo->exec($query);
    }

    public function checkBank($username){
        $query = sprintf(self::CHECK_IS_BANK, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['bank'];
    }

    public function checkBalance($username){
        $query = sprintf(self::FIND_FULL_NAME, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        return $row['balance'];
    }

}
