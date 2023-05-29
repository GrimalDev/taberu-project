<?php

require_once(realpath(dirname(__FILE__) . '/../db-config.php'));

class user
{

    private $DBpdo;
    private $DBtablename = 'users';

    private $id;
    private $username;
    private $email;
    private $passHash;
    private $role;

    public function __construct()
    {
        $this->DBpdo = connectDB();
    }

    public function __toString()
    {
        $user = [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'passHash' => $this->passHash,
            'role' => $this->role
        ];

        $user = json_encode($user);

        return $user;
    }

    public function getUserById($id): bool {
        $DBpdo = connectDB();
        $DBtablename = 'users';

        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `id` = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user === false) {
            return false;
        }

        $this->setId($user['id']);
        $this->setUsername($user['username']);
        $this->setEmail($user['email']);
        $this->setPassHash($user['password']);
        $this->setRole($user['role']);

        return true;
    }

    public function getUserByEmail($email): bool
    {
        $DBpdo = connectDB();
        $DBtablename = 'users';

        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `email` = :email");
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user === false) {
            throw new PDOException("Aucun compte n'existe à cette addresse mail ou nom d'utilisateur");
        }

        $this->setId($user['id']);
        $this->setUsername($user['username']);
        $this->setEmail($user['email']);
        $this->setPassHash($user['password']);
        $this->setRole($user['role']);

        return true;
    }

    public function getUserByUsername($username): bool
    {
        $DBpdo = connectDB();
        $DBtablename = 'users';

        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `username` = :username");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user === false) {
            throw new PDOException("Aucun compte n'existe à cette addresse mail ou nom d'utilisateur");
        }

        $this->setId($user['id']);
        $this->setUsername($user['username']);
        $this->setEmail($user['email']);
        $this->setPassHash($user['password']);
        $this->setRole($user['role']);

        return true;
    }

    public function verifyPassword($password): bool
    {
        //verify passsword hash is set
        if ($this->passHash === null) {
            return false;
        }
        return password_verify($password, $this->passHash);
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $passHash
     */
    public function setPassHash($passHash): void
    {
        $this->passHash = $passHash;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }



    public static function logConnection($user_id): void
    {
        $DBpdo = connectDB();

        //if connection null
        if ($DBpdo === null) {
            return;
        }

        $action = [
            'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'], //behind proxy, use HTTP_X_FORWARDED_FOR instead of REMOTE_ADDR
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'message' => 'Connection'
        ];

        $action = json_encode($action);

        $query = $DBpdo->prepare("INSERT INTO `user_logs` (`user_id`, `action`) VALUES (:user_id, :action)");
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':action', $action);
        $query->execute();
    }
}