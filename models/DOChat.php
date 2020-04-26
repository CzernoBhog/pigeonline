<?php

namespace models;

class DOChat{

    private $chatId;
    private $chatType;
    private $title;
    private $description;

    public function __construct($chatId = NULL, $chatType = NULL, $title = NULL, $description = NULL){
        if(func_get_args() != null) {
            $this->chatId = $chatId;
            $this->chatType = $chatType;
            $this->title = $title;
            $this->description = $description;
        }
    }

    public function getChatId(){
		return $this->chatId;
	}

	public function setChatId($chatId){
		$this->chatId = $chatId;
	}

	public function getChatType(){
		return $this->chatType;
	}

	public function setChatType($chatType){
		$this->chatType = $chatType;
	}

	public function getTitle(){
		return $this->title;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

}

?>