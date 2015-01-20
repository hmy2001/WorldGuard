<?php

namespace WorldGuard;

use pocketmine\utils\Config;

class WorldGuardDatabaseManager{

    /** @var Config */
    private $config;

    public function __construct($path){
		$this->config = new Config($path. "WorldGuard.yml",Config::YAML);
                $this->config->save();
	}

	public function getNameArea($player,$name){
                //foreach($this->config->getAll() as $playername => $){
                //return $data;
	}

	public function setNameArea($player,$name,$areadata){
                
                
	}

	public function getList($player){
                
                
	}

	public function saveData(){
                $this->config->save();
	}

}