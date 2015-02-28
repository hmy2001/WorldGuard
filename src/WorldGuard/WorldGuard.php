<?php

namespace WorldGuard;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;

class WorldGuard extends PluginBase implements Listener, CommandExecutor{

	public function onEnable(){
		$this->getLogger()->info("WorldGuardを読み込みました。");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
                @mkdir($this->getDataFolder());
                $this->pluginsession = new PluginSession($this->getDataFolder());
                if(!$this->pluginsession->Enabled()){
                	$this->getLogger()->info(TextFormat::RED."WorldEditor".TextFormat::YELLOW."か".TextFormat::RED."WEdit".TextFormat::YELLOW."が読み込みされていません！！");
                	$this->getServer()->shutdown();
                }else{
                	$this->getLogger()->info(TextFormat::GREEN."".$this->pluginsession->getPluginName()."".TextFormat::WHITE."を読み込みました。");
                }
	        $this->dbManager = new WorldGuardDatabaseManager($this->getDataFolder());
	}

	public function onDisable(){
                $this->dbManager->saveData();
		$this->getLogger()->info("WorldGuardのエリア情報を保存しました。");
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        	$username = $sender->getName();
                if(!($sender instanceof Player)){
                $sender->sendMessage("このコマンドはゲーム内で実行してください。");
                return true;
                break;
                }
        	switch($command->getName()){
       		case "/region":
                	if(!isset($args[0]) or $args[0] === ""){
                                $sender->sendMessage("使用法: //region claim [名前]\n".
                                                     "使用法: //region remove [名前]\n".
                                                     "使用法: //region info [名前]\n".
                                                     "使用法: //region select [名前]\n".
                                                     "使用法: //region list [ページ番号]"
                                                     );
                	return true;
                	break;
                	}
                	switch($args[0]){
                	case "claim":
				if(!isset($args[1]) or $args[1] === ""){
				$sender->sendMessage("使用法: //region claim [名前]");
				break;
				}
                                if($this->dbManager->getNameArea($sender,$name = $args[1])){
					$sender->sendMessage("".$name."のエリアは存在しています。");
                                break;
                                }
                                $session = $this->pluginsession->getSession($sender);
                                if(is_array($session)){
                                        $this->WG_claim($sender,$name,$session);
                                }else{
                                	$sender->sendMessage("範囲指定がされておりません。");
                                }
                	break;
                	case "remove":
				
				
                        break;
                	case "info":
				
				
                        break;
                	case "select":
				
				
                        break;
                	case "list":
				$this->dbManager->getList($sender);
                        break;
                        default:
                                $sender->sendMessage("使用法: //region claim [名前]\n".
                                                     "使用法: //region remove [名前]\n".
                                                     "使用法: //region info [名前]\n".
                                                     "使用法: //region select [名前]\n".
                                                     "使用法: //region list [ページ番号]"
                                                     );
                        break;
                	}
                break;
                }
                return true;
	}

	public function WG_claim($player,$name,$session){
		$minx = min($session[1][0], $session[2][0]);
		$miny = min($session[1][1], $session[2][1]);
		$minz = min($session[1][2], $session[2][2]);
		$maxx = max($session[1][0], $session[2][0]);
		$maxy = max($session[1][1], $session[2][1]);
		$maxz = max($session[1][2], $session[2][2]);
                $level = $session[3];
                $areadata = [$minx,$miny,$minz,$maxx,$maxy,$maxz,$level];
                $result = $this->dbManager->setNameArea($player,$name,$areadata);
                if($result){
			$player->sendMessage("範囲登録に成功しました。");
                }else{
			$player->sendMessage("範囲登録に失敗しました。競合が原因だと考えられます。");
                }
        }

	public function onBlockPlace(BlockPlaceEvent $event){
                $player = $event->getPlayer();
                $username = $player->getName();
                $block = $event->getBlock();
                $areaes = $this->dbManager->getAreaes();
                foreach($areaes as $playername => $areadataall){
                        foreach($areadataall as $areaname => $areadata){
                                for($x = $areadata["min"][0]; $x <= $areadata["max"][0]; $x++){
                                        for($y = $areadata["min"][1]; $y <= $areadata["max"][1]; $y++){
                                                for($z = $areadata["min"][1]; $z <= $areadata["max"][1]; $z++){
                                                        if($block->x === $x and $block->y === $y and $block->z === $z and $playername != $username and $block->getLevel()->getName() === $areadata["level"]){
                                                                $event->setCancelled();
                                                                $player->sendMessage("このエリアは保護されています。(".$playername.")さんのエリア。");
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }

	public function onBlockBreak(BlockBreakEvent $event){
                $player = $event->getPlayer();
                $username = $player->getName();
                $block = $event->getBlock();
                $areaes = $this->dbManager->getAreaes();
                foreach($areaes as $playername => $areadataall){
                        foreach($areadataall as $areaname => $areadata){
                                for($x = $areadata["min"][0]; $x <= $areadata["max"][0]; $x++){
                                        for($y = $areadata["min"][1]; $y <= $areadata["max"][1]; $y++){
                                                for($z = $areadata["min"][1]; $z <= $areadata["max"][1]; $z++){
                                                        if($block->x === $x and $block->y === $y and $block->z === $z and $playername != $username){
                                                                $event->setCancelled();
                                                                $player->sendMessage("このエリアは保護されています。(".$playername.")さんのエリア。");
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }

	public function onPlayerTouch(PlayerInteractEvent $event){
                $player = $event->getPlayer();
                $username = $player->getName();
                $block = $event->getBlock();
                $areaes = $this->dbManager->getAreaes();
                foreach($areaes as $playername => $areadataall){
                        foreach($areadataall as $areaname => $areadata){
                                for($x = $areadata["min"][0]; $x <= $areadata["max"][0]; $x++){
                                        for($y = $areadata["min"][1]; $y <= $areadata["max"][1]; $y++){
                                                for($z = $areadata["min"][1]; $z <= $areadata["max"][1]; $z++){
                                                        if($block->x === $x and $block->y === $y and $block->z === $z and $playername != $username){
                                                                $event->setCancelled();
                                                                $player->sendMessage("このエリアは保護されています。(".$playername.")さんのエリア。");
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }

}