<?php

namespace models;

class DOMessage{

    private $messageId;
    private $ttl;
    private $seen;
    private $filePath;
    private $text;
    private $timeStamp;
    private $messageType;
    private $edited;
    private $pinned;
    private $sentBy;
    private $quotedMessage;
    private $chatId;
    private $inoltred;


    public function __construct($messageId = NULL, $ttl = NULL, $seen = NULL, $filePath = NULL, $text = NULL, $timeStamp = NULL, $messageType = NULL, $edited = NULL, $pinned = NULL, $sentBy = NULL, $quotedMessage = NULL, $chatId = NULL, $inoltred = NULL)
    {
        if(func_get_args() != null) {
            $this->messageId = $messageId;
            $this->ttl = $ttl;
            $this->seen = $seen;
            $this->filePath = $filePath;
            $this->text = $text;
            $this->timeStamp = $timeStamp;
            $this->messageType = $messageType;
            $this->edited = $edited;
            $this->pinned = $pinned;
            $this->sentBy = $sentBy;
            $this->quotedMessage = $quotedMessage;
            $this->chatId = $chatId;
            $this->inoltred = $inoltred;
        }
    }


    public function getMessageId(){
		return $this->messageId;
	}

	public function setMessageId($messageId){
		$this->messageId = $messageId;
	}

	public function getTtl(){
		return $this->ttl;
	}

	public function setTtl($ttl){
		$this->ttl = $ttl;
	}

	public function getSeen(){
		return $this->seen;
	}

	public function setSeen($seen){
		$this->seen = $seen;
	}

	public function getFilePath(){
		return $this->filePath;
	}

	public function setFilePath($filePath){
		$this->filePath = $filePath;
	}

	public function getText(){
		return $this->text;
	}

	public function setText($text){
		$this->text = $text;
	}

	public function getTimeStamp(){
		return $this->timeStamp;
	}

	public function setTimeStamp($timeStamp){
		$this->timeStamp = $timeStamp;
	}

	public function getMessageType(){
		return $this->messageType;
	}

	public function setMessageType($messageType){
		$this->messageType = $messageType;
	}

	public function getEdited(){
		return $this->edited;
	}

	public function setEdited($edited){
		$this->edited = $edited;
	}

	public function getPinned(){
		return $this->pinned;
	}

	public function setPinned($pinned){
		$this->pinned = $pinned;
	}

	public function getSentBy(){
		return $this->sentBy;
	}

	public function setSentBy($sentBy){
		$this->sentBy = $sentBy;
	}

	public function getQuotedMessage(){
		return $this->quotedMessage;
	}

	public function setQuotedMessage($quotedMessage){
		$this->quotedMessage = $quotedMessage;
	}

	public function getChatId(){
		return $this->chatId;
	}

	public function setChatId($chatId){
		$this->chatId = $chatId;
	}

	public function getInoltred(){
		return $this->inoltred;
	}

	public function setInoltred($inoltred){
		$this->inoltred = $inoltred;
    }
    
}

?>