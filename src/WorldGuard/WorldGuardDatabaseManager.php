<?php

namespace WorldGuard;

use pocketmine\level\Position;

class WorldGuardDatabaseManager{

    /** @var Config */
    private $config;

    public function __construct($path){
		$this->config = new Config($path. "WorldGuard.yml",CONFIG::YAML);
                $this->config->save();
	}

	public function getName($player,$name){
                foreach($this->config->getAll() as $ => $){
                return $data;
	}

}