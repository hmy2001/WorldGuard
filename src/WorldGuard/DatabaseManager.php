<?php

namespace WorldGuard;

use pocketmine\Player;

class DatabaseManager{
	private $GuardData = [];

	public function __construct($folder = null){
		$this->Folder = $folder;
		$this->CreateData();
	}

	public function CreateData(){
		if(!file_exists($this->getDataFolder()."WorldGuard.dat")){
			file_put_contents($this->getDataFolder()."WorldGuard.dat", json_encode($this->GuardData, JSON_PRETTY_PRINT));
		}else{
			$this->GuardData = json_decode(file_get_contents($this->getDataFolder()."WorldGuard.dat"), true);
		}
	}


	public function getDataFolder(){
		return $this->Folder;
	}

	public function getGuardArea($areaname, $level){
		if(isset($this->GuardData[$level][$areaname])){
			return $this->GuardData[$level][$areaname];
		}
		return false;
	}

	public function getGuardAreaInPosition($level, $x, $y, $z, &$areaname){
		if(isset($this->GuardData[$level])){
			foreach($this->GuardData[$level] as $areaname => $data){
				$areadata = $data["AreaData"];
				for($startX = (int) $areadata[0][0]; $startX <= (int) $areadata[1][0]; ++$startX){
					for($startY = (int) $areadata[0][1]; $startY <= (int) $areadata[1][1]; ++$startY){
						for($startZ = (int) $areadata[0][2]; $startZ <= (int) $areadata[1][2]; ++$startZ){
							if($startX === $x and $startY === $y and $startZ === $z){
								return $this->GuardData[$level][$areaname];
							}
						}
					}
				}
			}
		}
		return false;
	}

	public function setGuardArea($player, $areaname, $type = "create", $areadata = []){
		switch($type){
			case "create":
				if(!isset($this->GuardData[$player->getLevel()->getName()][$areaname]) and isset($areadata[1])){
					$this->GuardData[$player->getLevel()->getName()][$areaname] = ["AreaData" => $areadata, "PlayerData" => [$player->getName() => true]];
					$player->sendMessage("[WorldGuard] (".$areadata[0][0].", ".$areadata[0][1].", ".$areadata[0][2].") - (".$areadata[1][0].", ".$areadata[1][1].", ".$areadata[1][2].") を".$areaname."という登録名で登録しました。");
					$this->saveData();
					return true;
				}else{
					$player->sendMessage("[WorldGuard] Error..... Report to Hmy2001!");
				}
				return false;
			break;
			case "remove":
				if(isset($this->GuardData[$player->getLevel()->getName()][$areaname])){
					$GuardData = $this->GuardData[$player->getLevel()->getName()][$areaname];
					if(isset($GuardData["PlayerData"][$player->getName()])){
						$areadata = $this->GuardData[$player->getLevel()->getName()][$areaname]["AreaData"];
						unset($this->GuardData[$player->getLevel()->getName()][$areaname]);
						$player->sendMessage("[WorldGuard] ".$areaname." (".$areadata[0][0].", ".$areadata[0][1].", ".$areadata[0][2].") - (".$areadata[1][0].", ".$areadata[1][1].", ".$areadata[1][2].") という登録名のエリアを削除しました。");
						$this->saveData();
						return true;
					}else{
						$player->sendMessage("[WorldGuard] ".$areaname." という登録名のエリアの管理者ではありません。");
					}
				}
				return false;
			break;
			case "update":
				if(isset($areadata["Type"])){
					switch($areadata["Type"]){
						case "addmember":
							if(isset($areadata["PlayerName"]) and isset($this->GuardData[$player->getLevel()->getName()][$areaname])){
								if(isset($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$player->getName()])){
									if(isset($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$areadata["PlayerName"]])){
										$player->sendMessage("[WorldGuard] ".$areadata["PlayerName"]."さんは".$areaname."の管理者です。");
									}else{
										$this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$areadata["PlayerName"]] = false;
										$player->sendMessage("[WorldGuard] ".$areadata["PlayerName"]."さんを".$areaname."の管理者に追加しました。");
										$member = $player->getServer()->getPlayerExact($areadata["PlayerName"]);
										if($member instanceof Player){
											$member->sendMessage("[WorldGuard] あなたは".$areaname."の管理者になりました。");
										}
										$this->saveData();
									}
								}else{
									$player->sendMessage("[WorldGuard] あなたは".$areaname."の管理者ではありません。");
								}
							}else{
								$player->sendMessage("[WorldGuard] Error..... Report to Hmy2001!");
							}
						break;
						case "removemember":
							if(isset($areadata["PlayerName"]) and isset($this->GuardData[$player->getLevel()->getName()][$areaname])){
								if(isset($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$areadata["PlayerName"]])){
									if(isset($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$player->getName()])){
										if($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$areadata["PlayerName"]]){
											$player->sendMessage("[WorldGuard] ".$areadata["PlayerName"]."さんは".$areaname."の絶対的管理者なので削除できません。");
										}else{
											unset($this->GuardData[$player->getLevel()->getName()][$areaname]["PlayerData"][$areadata["PlayerName"]]);
											$player->sendMessage("[WorldGuard] ".$areadata["PlayerName"]."さんを".$areaname."の管理者から外しました。");
											$member = $player->getServer()->getPlayerExact($areadata["PlayerName"]);
											if($member instanceof Player){
												$member->sendMessage("[WorldGuard] あなたは".$areaname."の管理者から外されました。");
											}
											$this->saveData();
										}
									}else{
										$player->sendMessage("[WorldGuard] あなたは".$areaname."の管理者ではありません。");
									}
								}else{
									$player->sendMessage("[WorldGuard] ".$areadata["PlayerName"]."さんは".$areaname."の管理者ではありません。");
								}
							}else{
								$player->sendMessage("[WorldGuard] Error..... Report to Hmy2001!");
							}
						break;
					}
				}else{
					$player->sendMessage("[WorldGuard] Error..... Report to Hmy2001!");
				}
			break;
		}
		return false;
	}

	public function saveData(){
		file_put_contents($this->getDataFolder()."WorldGuard.dat", json_encode($this->GuardData, JSON_PRETTY_PRINT));
	}

}