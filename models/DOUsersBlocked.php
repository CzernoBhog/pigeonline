<?php
namespace models;

class DOUsersBlocked{

    private $blockedBy;
	private $userBlocked;
	private $lastModify;

    public function __construct($blockedBy = null, $userBlocked = null, $lastModify = null)
    {
        if (func_get_args() != null) {
            $this->blockedBy = $blockedBy;
			$this->userBlocked = $userBlocked;
			$this->lastModify = $lastModify;
        }
    }

    public function getBlockedBy(){
		return $this->blockedBy;
	}

	public function setBlockedBy($blockedBy){
		$this->blockedBy = $blockedBy;
	}

	public function getUserBlocked(){
		return $this->userBlocked;
	}

	public function setUserBlocked($userBlocked){
		$this->userBlocked = $userBlocked;
	}

	public function getLastModify(){
		return $this->lastModify;
	}

	public function setLastModify($lastModify){
		$this->lastModify = $lastModify;
	}

}

?>