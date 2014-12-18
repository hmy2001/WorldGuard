<?php

namespace WorldGuard;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
//use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;

class WorldGuard extends PluginBase implements Listener, CommandExecutor{

	public function onEnable(){//Œã‚Å–|–ó
		$this->getLogger()->info("WorldGuard loaded!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
                @mkdir($this->getDataFolder());
		//$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array());
		//$this->config->save();
                //$this->dbManager = new WorldGuardDatabaseManager($this->getDataFolder());
	}
















	public function onDisable(){
                //save function
		$this->getLogger()->info("WorldGuard was to save the area information!");
	}

}