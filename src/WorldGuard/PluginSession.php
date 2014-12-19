<?php

namespace WorldGuard;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
//use pocketmine\utils\Config;

use WorldEditor\WorldEditor;
use WEdit\WEdit;

class PluginSession{

	public function __construct($dataFolder){
		$this->DataFolder = $dataFolder;
                $this->plugin = false;
		foreach(Server::getInstance()->getPluginManager()->getPlugins() as $plugin){
                        switch($plugin->getName()){
                        case "WorldEditor":
                        if($plugin->getDescription()->getVersion() === "1.0.3"){
			$this->plugin = $plugin;
                        }else{
                	return false;
                        }
                        break;
                        case "WEdit":
                        if($plugin->getDescription()->getVersion() === "2.0.0"){
			$this->plugin = $plugin;
                        }else{
                	return false;
                        }
                        break;
		        }
                }
                if(!$this->plugin){
                	return false;
                }
                return true;
	}

	public function getPluginName(){
		return $this->plugin->getName();
	}





















}