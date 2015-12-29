<?php

namespace WorldGuard;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class PluginSession{

	public function __construct(){
		$this->plugin = false;
	}

	public function Enabled(){
		foreach(Server::getInstance()->getPluginManager()->getPlugins() as $plugin){
			switch($plugin->getName()){
				case "WorldEditor":
					if($plugin->getDescription()->getVersion() >= "1.0.3"){
						$this->plugin = $plugin;
					}else{
						return false;
					}
				break;
				case "WEdit":
					if($plugin->getDescription()->getVersion() >= "2.0.1"){
						$this->plugin = $plugin;
					}else{
						return false;
					}
				break;
			}
		}
		if(!$this->plugin instanceof Plugin){
			return false;
		}
		return true;
	}

	public function getPluginName(){
		if($this->plugin instanceof Plugin){
			return $this->plugin->getName();
		}
		return "";
	}

	public function getSession($player){
		switch($this->getPluginName()){
			case "WorldEditor":
				$session = $this->plugin->session($player)["selection"];
				if(is_array($session)){
					if(isset($session[0][0]) and isset($session[0][1]) and $session[0][3]->getName() === $session[1][3]->getName()){
						$data[0][0] = min($session[0][0], $session[1][0]);
						$data[0][1] = min($session[0][1], $session[1][1]);
						$data[0][2] = min($session[0][2], $session[1][2]);
						$data[1][0] = max($session[0][0], $session[1][0]);
						$data[1][1] = max($session[0][1], $session[1][1]);
						$data[1][2] = max($session[0][2], $session[1][2]);
						return $data;
					}else{
						return false;
					}
				}else{
					return false;
				}
			break;
			case "WEdit":
				$session = $this->plugin->getSession($player->getName());
				if(is_array($session)){
					$data[0][0] = min($session[1][0], $session[2][0]);
					$data[0][1] = min($session[1][1], $session[2][1]);
					$data[0][2] = min($session[1][2], $session[2][2]);
					$data[1][0] = max($session[1][0], $session[2][0]);
					$data[1][1] = max($session[1][1], $session[2][1]);
					$data[1][2] = max($session[1][2], $session[2][2]);
					return $session;
				}else{
					return false;
				}
			break;
		}
	}

}