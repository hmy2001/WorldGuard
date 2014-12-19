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
			if($plugin->getName() === "WorldEditor" or $plugin->getName() === "WEdit"){
				$this->plugin = $plugin;
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