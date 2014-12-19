<?php

namespace WorldGuard;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
//use pocketmine\utils\Config;
use pocketmine\Player;

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

	public function getSession(Player $player){
		switch($this->getPluginName()){
		case "WorldEditor":
                $session = $this->plugin->session($player);
                if(if(!is_array($session) or $session[0][0] === false or $session[0][1] === false or $session[0][0][3] !== $session[0][1][3]){){
		return $session;
                }else{
                return false;
                }
		break;
		case "WEdit":
		return ;
		break;
		}
        }















}