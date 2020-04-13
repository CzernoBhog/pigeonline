<?php

namespace models;

class DOUser
{

    private $userId;
    private $name;
    private $surname;
    private $email;
    private $mood;
    private $username;
    private $password;
    private $pathProfilePicture;
    private $activated;
    private $token;

    public function __construct($userId = null, $name = null, $surname = null, $email = null, $mood = null, $username = null, $password = null, $pathProfilePicture = null, $activated = null, $token = null)
    {
        if (func_get_args() != null) {
            $this->userId = $userId;
            $this->name = $name;
            $this->surname = $surname;
            $this->email = $email;
            $this->mood = $mood;
            $this->username = $username;
            $this->password = $password;
            $this->pathProfilePicture = $pathProfilePicture;
            $this->token = $token;
        }
    }

    function setUserId($userId)
    {
        $this->userId = $userId;
    }

    function getUserId()
    {
        return $this->userId;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function getName()
    {
        return $this->name;
    }

    function setSurname($surname)
    {
        $this->surname = $surname;
    }

    function getSurname()
    {  
        return $this->surname;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function getEmail()
    {
        return $this->email;
    }

    function setMood($mood)
    {
        $this->mood = $mood;
    }

    function getMood()
    {
        return $this->mood;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function getUsername()
    {
        return $this->username;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function getPassword()
    {
        return $this->password;
    }

    function setPathProfilePicture($pathProfilePicture)
    {
        $this->pathProfilePicture = $pathProfilePicture;
    }

    function getPathProfilePicture()
    {
        return $this->pathProfilePicture;
    }

    function setActivated($activated)
    {
        $this->activated = $activated;
    }

    function getActivated()
    {
        return $this->activated;
    }

    function setToken($token)
    {
        $this->token = $token;
    }
    
    function getToken()
    {
        return $this->token;
    }
}
