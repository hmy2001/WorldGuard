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
                if(!$this->pluginsession = new PluginSession($this->getDataFolder())){
                	$this->getLogger()->info(TextFormat::RED."WorldEditor".TextFormat::YELLOW."か".TextFormat::RED."WEdit".TextFormat::YELLOW."が読み込みされていません！！");
                	$this->getServer()->shutdown();
                }else{
                	$this->getLogger()->info(TextFormat::GREEN."".$this->pluginsession->getPluginName()."".TextFormat::WHITE."を読み込みました。");
                }
	        $this->dbManager = new WorldGuardDatabaseManager($this->getDataFolder());
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
				//if(!$this->dbManager->getName($sender,$name = $args[1])->fetchArray(SQLITE3_ASSOC)){
                                if(!$this->dbManager->getName($sender,$name = $args[1])){
					$sender->sendMessage("".$name."の名前のエリアは存在しています。");
                                break;
                                }
                                $session = $this->pluginsession->getSession($sender)["selection"];
                                if(!$session){
                                        $this->WG_claim($sender,$name,$session);
                                }else{
                                	$sender->sendMessage("範囲指定がされておりません。");
                                }
                		//$sender->sendMessage("Test Message");
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
		$minx = 
		$maxx = 
		$miny = 
		$maxy = 
		$minz = 
		$maxz = 
		//TODO




        }

	public function onDisable(){
                //save function TODO
		$this->getLogger()->info("WorldGuardのエリア情報を保存しました。");
	}

}