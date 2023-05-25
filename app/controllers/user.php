<?php

require_once realpath(dirname(__FILE__) . '/../db-config.php');

class user
{

    private $DBpdo;
    private $DBtablename = 'users';

    private $id;
    private $username;
    private $email;
    private $passHash;

    public function __construct()
    {
        $this->DBpdo = connectDB();
    }

    public function __toString()
    {
        return "{
            id: $this->id,
            username: $this->username,
            email: $this->email,
            passHash: $this->passHash
        }";
    }

    public function getUserById($id): bool {
        $DBpdo = connectDB();
        $DBtablename = 'users';

        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `id` = :id");
        $query->bindParam(':id', $id);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user === false) {
            return false;
        }

        $this->id = $user['id'];
        $this->setUsername($user['username']);
        $this->setEmail($user['email']);

        return true;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassHash()
    {
        return $this->passHash;
    }

    /**
     * @param mixed $passHash
     */
    public function setPassHash($passHash): void
    {
        $this->passHash = $passHash;
    }
}