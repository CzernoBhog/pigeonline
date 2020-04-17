<?php

namespace models;

class DOBlockedIp
{

    private $ip;
    private $timestamp;
	private $userId;
	private $injId;
    private $typeVuln;

    public function __construct($ip = null, $userId = null, $injId = null, $typeVuln = null, $timestamp = null)
    {
        if (func_get_args() != null) {
            $this->ip = $ip;
            $this->timestamp = $timestamp;
			$this->userId = $userId;
			$this->injId = $injId;
            $this->typeVuln = $typeVuln;
        }
    }

    public function getIp(){
		return $this->ip;
	}

	public function setIp($ip){
		$this->ip = $ip;
	}

	public function getTimestamp(){
		return $this->timestamp;
	}

	public function setTimestamp($timestamp){
		$this->timestamp = $timestamp;
	}

	public function getUserId(){
		return $this->userId;
	}

	public function setUserId($userId){
		$this->userId = $userId;
	}

	public function getInjId(){
		return $this->injId;
	}

	public function setInjId($injId){
		$this->injId = $injId;
	}

	public function getTypeVuln(){
		return $this->typeVuln;
	}

	public function setTypeVuln($typeVuln){
		$this->typeVuln = $typeVuln;
	}
}
