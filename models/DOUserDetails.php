<?php

class DOUserDetails{

    private $userDetailsId;
    private $isOnline;
    private $lastActivity;
    private $privacyLevel;
    private $userId;

    public function __construct($userDetailsId = null, $isOnline = null, $lastActivity = null, $privacyLevel = null, $userId = null)
    {
        if (func_get_args() != null) {
            $this->userDetailsId = $userDetailsId;
            $this->isOnline = $isOnline;
            $this->lastActivity = $lastActivity;
            $this->privacyLevel = $privacyLevel;
            $this->userId = $userId;
        }
    }

    public function getUserDetailsId(){
		return $this->userDetailsId;
	}

	public function setUserDetailsId($userDetailsId){
		$this->userDetailsId = $userDetailsId;
	}

	public function getIsOnline(){
		return $this->isOnline;
	}

	public function setIsOnline($isOnline){
		$this->isOnline = $isOnline;
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

	public function getUserId(){
		return $this->userId;
	}

	public function setUserId($userId){
		$this->userId = $userId;
	}

}

?>