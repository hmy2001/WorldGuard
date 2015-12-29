<?php

namespace WorldGuard;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\block\Block;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\player\PlayerInteractEvent;

class WorldGuard extends PluginBase implements Listener, CommandExecutor{

	public function onEnable(){
		$this->getLogger()->info("WorldGuardを読み込みました！");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder());
		$this->PluginSession = new PluginSession();
		if(!$this->PluginSession->Enabled()){
			if($this->PluginSession->getPluginName() === ""){
				$this->getLogger()->info(TextFormat::RED.$this->PluginSession->getPluginName().TextFormat::YELLOW."を最新版にしてください！！");
			}else{
				$this->getLogger()->info(TextFormat::RED."WorldEditor".TextFormat::YELLOW."か".TextFormat::RED."WEdit".TextFormat::YELLOW."が読み込みされていません！！");
			}
			$this->getServer()->shutdown();
		}else{
			$this->getLogger()->info(TextFormat::GREEN."".$this->PluginSession->getPluginName()."".TextFormat::WHITE."を読み込みました。");
		}
		$this->DataBaseManager = new DatabaseManager($this->getDataFolder());
	}

	public function onDisable(){
		$this->DataBaseManager->saveData();
		$this->getLogger()->info("WorldGuardのエリア情報を保存しました。");
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$username = $sender->getName();
		$cmd = $command->getName();
		if($cmd{0} === "/"){
			$cmd = substr($cmd, 1);
		}
		switch($cmd){
			case "region":
				if(!$sender instanceof Player){
					$sender->sendMessage("このコマンドはゲーム内で実行してください。");
					break;
				}
				if(!isset($args[0]) or $args[0] === ""){
					$sender->sendMessage("使用方法: //region claim [登録名] 登録\n".
										"使用方法: //region remove [登録名] 削除\n".
										"使用方法: //region info [登録名]\n".
										"使用方法: //region addmember [登録名] [プレーヤー名]\n".
										"使用方法: //region removemember [登録名] [プレーヤー名]"
					);
					break;
				}
				switch($args[0]){
					case "claim":
						if(!isset($args[1]) or $args[1] === ""){
							$sender->sendMessage("使用方法: //region claim [登録名]");
							break;
						}
						$name = $args[1];
						if(is_array($this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName()))){
							$sender->sendMessage("[WorldGuard] ".$name."はすでに登録されています。");
							break;
						}
						$session = $this->PluginSession->getSession($sender);
						if(!is_array($session)){
							$sender->sendMessage("範囲が設定されていません。");
							break;
						}
						$this->DataBaseManager->setGuardArea($sender, $name, "create", $session);
					break;
					case "remove":
						if(!isset($args[1]) or $args[1] === ""){
							$sender->sendMessage("使用方法: //region remove [登録名]");
							break;
						}
						$name = $args[1];
						if($this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName()) === false){
							$sender->sendMessage("[WorldGuard] ".$name."は存在しません。");
							break;
						}
						$this->DataBaseManager->setGuardArea($sender, $name, "remove");
					break;
					case "info":
						if(!isset($args[1]) or $args[1] === ""){
							$result = $this->DataBaseManager->getGuardAreaInPosition($sender->getLevel()->getName(), (int) floor($sender->getX()), (int) floor($sender->getY()), (int) floor($sender->getZ()), $areaname);
							if(is_array($result) and isset($result["PlayerData"])){
								$members = "";
								$num = 0;
								foreach($result["PlayerData"] as $PlayerName => $flag){
									if($num !== 0){
										$members .= ", ";
									}
									$members .= $PlayerName;
									$num++;
								}

								$sender->sendMessage("[WorldGuard] ".$areaname." - 保護エリア情報\n".
													"管理者リスト : ".$members."\n".
													"保護座標: \n".
													"(".$result["AreaData"][0][0].", ".$result["AreaData"][0][1].", ".$result["AreaData"][0][2].") - \n".
													"(".$result["AreaData"][1][0].", ".$result["AreaData"][1][1].", ".$result["AreaData"][1][2].")"
								);
							}else{
								$sender->sendMessage("[WorldGuard] 今いる座標は保護エリアではありません。");
							}
						}else{			
							$name = $args[1];
							if($this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName()) === false){
								$sender->sendMessage("[WorldGuard] ".$name."は存在しません。");
								break;
							}
							$areadata = $this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName());

							$members = "";
							$num = 0;
							foreach($areadata["PlayerData"] as $PlayerName => $flag){
								if($num !== 0){
									$members .= ", ";
								}
								$members .= $PlayerName;
								$num++;
							}

							$sender->sendMessage("[WorldGuard] ".$name." - 保護エリア情報\n".
												"管理者リスト : ".$members."\n".
												"保護座標: \n".
												"(".$areadata["AreaData"][0][0].", ".$areadata["AreaData"][0][1].", ".$areadata["AreaData"][0][2].") - \n".
												"(".$areadata["AreaData"][1][0].", ".$areadata["AreaData"][1][1].", ".$areadata["AreaData"][1][2].")"
							);
						}
					break;
					case "addmember":
						if(!isset($args[1]) or $args[1] === "" or !isset($args[2]) or $args[2] === ""){
							$sender->sendMessage("使用方法: //region addmember [登録名] [プレーヤー名]");
							break;
						}

						$name = $args[1];
						if($this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName()) === false){
							$sender->sendMessage("[WorldGuard] ".$name."は存在しません。");
							break;
						}

						$this->DataBaseManager->setGuardArea($sender, $name, "update", ["Type" => "addmember", "PlayerName" => $args[2]]);
					break;
					case "removemember":
						if(!isset($args[1]) or $args[1] === "" or !isset($args[2]) or $args[2] === ""){
							$sender->sendMessage("使用方法: //region removemember [登録名] [プレーヤー名]");
							break;
						}
						$name = $args[1];
						if($this->DataBaseManager->getGuardArea($name, $sender->getLevel()->getName()) === false){
							$sender->sendMessage("[WorldGuard] ".$name."は存在しません。");
							break;
						}

						$this->DataBaseManager->setGuardArea($sender, $name, "update", ["Type" => "removemember", "PlayerName" => $args[2]]);
					break;
					default:
						$sender->sendMessage("使用方法: //region claim [登録名] 登録\n".
											"使用方法: //region remove [登録名] 削除\n".
											"使用方法: //region info [登録名]\n".
											"使用方法: //region addmember [登録名] [プレーヤー名]\n".
											"使用方法: //region removemember [登録名] [プレーヤー名]"
						);
					break;
				}
			break;
		}
		return true;
	}

	public function onBlockBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$result = $this->checkGuardArea($player, $block->getX(), $block->getY(), $block->getZ());
		if($result){
			$event->setCancelled();
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$result = $this->checkGuardArea($player, $block->getX(), $block->getY(), $block->getZ());
		if($result){
			$event->setCancelled();
		}
	}

	public function onBlockUpdate(BlockUpdateEvent $event){
		$block = $event->getBlock();
		//TODO: WorldEditor Plugin
	}

	public function onBlockTouch(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$result = $this->checkGuardArea($player, $block->getX(), $block->getY(), $block->getZ());
		if($result){
			$event->setCancelled();
		}
	}

	public function checkGuardArea($player, $x, $y, $z){
		$result = $this->DataBaseManager->getGuardAreaInPosition($player->getLevel()->getName(), (int) $x, (int) $y, (int) $z, $areaname);
		if(is_array($result) and isset($result["PlayerData"])){
			if(!isset($result["PlayerData"][$player->getName()])){
				$player->sendMessage("[WorldGuard] このエリアは保護されています。 詳細は//region info ".$areaname);
				return true;
			}
		}
		return false;
	}

}
