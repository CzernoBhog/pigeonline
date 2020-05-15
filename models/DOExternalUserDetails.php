<?php
namespace models;

    class DOExternalUserDetails {

        private $userId;
        private $username;
        private $isOnline;
        private $mood;
        private $pathProfilePicture;
        private $lastActivity;
        private $privacyLevel;
        private $lastModify;
        
        public function __construct($userId  = NULL, $username = NULL, $mood = NULL, $isOnline = NULL, $lastActivity = NULL, $privacyLevel = NULL, $pathProfilePicture = NULL, $lastModify = null) {
            if (func_get_args() != null) {
                $this->userId = $userId;
                $this->username = $username;
                $this->isOnline = $isOnline;
                $this->mood = $mood;
                $this->privacyLevel = $privacyLevel;
                $this->lastActivity = $lastActivity;
                $this->pathProfilePicture = $pathProfilePicture;
                $this->lastModify = $lastModify;
            }
        }

        public function getUserId(){
            return $this->userId;
        }
    
        public function setUserId($userId){
            $this->userId = $userId;
        }
    
        public function getUsername(){
            return $this->username;
        }
    
        public function setUsername($username){
            $this->username = $username;
        }
    
        public function getIsOnline(){
            return $this->isOnline;
        }
    
        public function setIsOnline($isOnline){
            $this->isOnline = $isOnline;
        }
    
        public function getMood(){
            return $this->mood;
        }
    
        public function setMood($mood){
            $this->mood = $mood;
        }
    
        public function getPathProfilePicture(){
            return $this->pathProfilePicture;
        }
    
        public function setPathProfilePicture($pathProfilePicture){
            $this->pathProfilePicture = $pathProfilePicture;
        }
    
        public function getLastActivity(){
            return $this->lastActivity;
        }
    
        public function setLastActivity($lastActivity){
            $this->lastActivity = $lastActivity;
        }
    
        public function getPrivacyLevel(){
            return $this->privacyLevel;
        }
    
        public function setPrivacyLevel($privacyLevel){
            $this->privacyLevel = $privacyLevel;
        }

        public function getLastModify(){
            return $this->lastModify;
        }
    
        public function setLastModify($lastModify){
            $this->lastModify = $lastModify;
        }
    }
