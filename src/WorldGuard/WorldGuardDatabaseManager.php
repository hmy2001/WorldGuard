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

	public function getNameArea($playername,$name){
                foreach($this->config->getAll() as $playername => $areadataall){
                        foreach($areadataall as $areaname => $areadata){
                                if($areaname === $name){
                                        return true;
                                }
                        }
                }
                return false;
	}

	public function setNameArea($player,$name,$area){
                foreach($this->config->getAll() as $playername => $areadataall){
                        if($playername != $player->getName()){
                                foreach($areadataall as $areaname => $areadata){
                                        for($x = $areadata["min"][0]; $x <= $areadata["max"][0]; $x++){
                                                for($y = $areadata["min"][1]; $y <= $areadata["max"][1]; $y++){
                                                        for($z = $areadata["min"][1]; $z <= $areadata["max"][1]; $z++){
                                                                if($area[0] === $x or $area[3] === $x and $area[1] === $y or $area[4] === $y and $area[2] === $z or $area[5] === $z){
                                                                        return false;
                                                                }
                                                        }
                                                }
                                        }
                                }
                        }
                }
                $data = $this->config->get($player->getName());
                $data[$name]["min"][0] = $area[0];
                $data[$name]["min"][1] = $area[1];
                $data[$name]["min"][2] = $area[2];
                $data[$name]["max"][0] = $area[3];
                $data[$name]["max"][1] = $area[4];
                $data[$name]["max"][2] = $area[5];
                $data[$name]["level"] = $area[6];
                $data[$name]["time"] = date("Y-n-j H:i:s");
                $this->config->set($player->getName(),$data);
                $this->config->save();
                return true;
	}

	public function getList($player){
                $output = "";
	}

	public function getAreaes(){
                return $this->config->getAll();
	}

	public function getArea(){
                
	}

	public function getAreaName(){
                
	}

	public function saveData(){
                $this->config->save();
	}

}