<?php
namespace models;

class DOSeenBy{

    private $userId;
	private $messageId;
	private $lastModify;

    public function __construct($userId = null, $messageId = null, $lastModify = null)
    {
        if (func_get_args() != null) {
            $this->userId = $userId;
			$this->messageId = $messageId;
			$this->lastModify = $lastModify;
        }
    }

    public function getUserId(){
		return $this->userId;
	}

	public function setUserId($userId){
		$this->userId = $userId;
	}

	public function getMessageId(){
		return $this->messageId;
	}

	public function setMessageId($messageId){
		$this->messageId = $messageId;
	}

	public function getLastModify(){
		return $this->lastModify;
	}

	public function setLastModify($lastModify){
		$this->lastModify = $lastModify;
	}

}

?>