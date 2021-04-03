<?php
class PermissionsEX
{
	public static function setRank($conn, $uuid, $playername, $rank)
	{
		$sql = "INSERT INTO pex_entity (`name`,`type`,`default`) VALUES (?,1,0);";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die($conn->error);
		}
		$stmt->bind_param(
			's',
			$uuid
		);
		$stmt->execute();
		$stmt->close();

		$sql = "INSERT INTO pex_inheritance (`child`,`parent`, `type` ) VALUES (?,?,1);";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die($conn->error);
		}
		$stmt->bind_param(
			'ss',
			$uuid,
			$rank
		);
		$stmt->execute();
		$stmt->close();

		$mc_modes = PermissionsEX::getMcModes($conn);
		foreach ($mc_modes as $k => $v) {
			$sql = "INSERT INTO pex_permissions_" . $v['dirname'] . " (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				die($conn->error);
			}
			$stmt->bind_param(
				'ss',
				$uuid,
				$playername
			);
			$stmt->execute();
			$stmt->close();
		}
	}

	public static function delRank($conn, $uuid)
	{
		$sql = "DELETE FROM pex_entity WHERE `name` = ?;";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			$out['status'] = "ko";
			$out['msg'] = $conn->error;
			die(json_encode($out));
		}
		$stmt->bind_param(
			's',
			$uuid
		);
		$stmt->execute();
		if ($stmt->error) {
			$out['status'] = "ko";
			$out['msg'] = $stmt->error;
			die(json_encode($out));
		}
		$stmt->close();

		$sql = "DELETE FROM pex_inheritance WHERE `child` = ?;";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			$out['status'] = "ko";
			$out['msg'] = $conn->error;
			die(json_encode($out));
		}
		$stmt->bind_param(
			's',
			$uuid
		);
		$stmt->execute();
		if ($stmt->error) {
			$out['status'] = "ko";
			$out['msg'] = $stmt->error;
			die(json_encode($out));
		}
		$stmt->close();

		$mc_modes = PermissionsEX::getMcModes($conn);
		foreach ($mc_modes as $k => $v) {
			$sql = "DELETE FROM pex_permissions_" . $v['dirname'] . " WHERE `name` = ?;";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				$out['status'] = "ko";
				$out['msg'] = $conn->error;
				die(json_encode($out));
			}
			$stmt->bind_param(
				's',
				$uuid
			);
			$stmt->execute();
			if ($stmt->error) {
				$out['status'] = "ko";
				$out['msg'] = $stmt->error;
				die(json_encode($out));
			}
			$stmt->close();
		}
	}

	public static function getMcModes($conn)
	{
		$sql = "SELECT id, modename, dirname FROM mc_modes WHERE use_pex;";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die($conn->error);
		}
		$stmt->execute();
		$mc_modes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		return $mc_modes;
	}
}
