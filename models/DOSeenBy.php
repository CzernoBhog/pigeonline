<?php
namespace models;

class DOSeenBy{

    private $userId;
    private $messageId;

    public function __construct($userId = null, $messageId = null)
    {
        if (func_get_args() != null) {
            $this->userId = $userId;
            $this->messageId = $messageId;
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

}

?>