<?php

namespace WorldGuard;

use pocketmine\level\Position;

class WorldGuardDatabaseManager{

    /** @var \SQLite3 */
    private $db;

    public function __construct($path){
		$this->db = new \SQLite3($path. "WorldGuard.sqlite3");
		$this->db->exec(
				"CREATE TABLE IF NOT EXISTS WorldGuard(
				Owner INTEGER PRIMARY KEY AUTOINCREMENT,
				subOwner INTEGER NOT NULL,
				name INTEGER NOT NULL,
				protect INTEGER NOT NULL,
				minX INTEGER NOT NULL,
				minY INTEGER NOT NULL,
				minZ INTEGER NOT NULL,
				maxX INTEGER NOT NULL,
				maxY INTEGER NOT NULL,
				maxZ INTEGER NOT NULL
		                )"
		);
	}

}