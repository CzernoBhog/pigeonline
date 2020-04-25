<?php
namespace models;

class DOUsersBlocked{

    private $blockedBy;
    private $userBlocked;

    public function __construct($blockedBy = null, $userBlocked = null)
    {
        if (func_get_args() != null) {
            $this->blockedBy = $blockedBy;
            $this->userBlocked = $userBlocked;
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

}

?>