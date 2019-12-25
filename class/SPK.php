<?php

namespace SPK;

class GlobalFunc
{

	public static function getMysqlConn($host, $user, $pass, $ddbb)
	{
		$conn = @new \MySQLi($host, $user, $pass, $ddbb);
		if ($conn->connect_errno) {
			die($conn->connect_error);
		} else {
			return $conn;
		}
	}

	public static function minecraftVersion($intVer)
	{
		switch ($intVer) {
			case 498:
				return "1.14.4";
			case 490:
				return "1.14.3";
			case 485:
				return "1.14.2";
			case 480:
				return "1.14.1";
			case 477:
				return "1.14";
			case 404:
				return "1.13.2";
			case 401:
				return "1.13.1";
			case 393:
				return "1.13";
			case 340:
				return "1.12.2";
			case 338:
				return "1.12.1";
			case 335:
				return "1.12";
			case 316:
				return "1.11.1-2";
			case 315:
				return "1.11";
			case 210:
				return "1.10.0-2";
			case 110:
				return "1.9.3-4";
			case 109:
				return "1.9.2";
			case 108:
				return "1.9.1";
			case 107:
				return "1.9";
			case 47:
				return "1.8.0-9";
			case 5:
				return "1.7.6-10";
			case 4:
				return "1.7.2-5";
			case 78:
				return "1.6.4";
			case 77:
				return "1.6.3";
			case 74:
				return "1.6.2";
			case 73:
				return "1.6.1";
			case 61:
				return "1.5.2";
			case 60:
				return "1.5.0-1";
			case 51:
				return "1.4.6-7";
			default:
				return "N/A";
		}
	}

	public static function sec2hms($sec)
	{
		$hms = "";
		$hours = intval(intval($sec) / 3600);
		$hms .= str_pad($hours, 2, "0", STR_PAD_LEFT) . ":";
		$minutes = intval(($sec / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";
		$seconds = intval($sec % 60);
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		return $hms;
	}

	public static function randHex($bytes)
	{
		return bin2hex(openssl_random_pseudo_bytes($bytes));
	}

	public static function genSessionId()
	{
		return sprintf("%s-%s-%s-%s-%s", self::randHex(4), self::randHex(2), self::randHex(2), self::randHex(2), self::randHex(6));
	}

	public static function checkSession($conn, $session)
	{
		$sql = "SELECT web_session.id_mc_player, mc_players.playername, mc_players.uuid
		FROM web_session
		INNER JOIN mc_players ON mc_players.id = web_session.id_mc_player
		WHERE web_session.session = ? AND web_session.expire > NOW();";
		$stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die($conn->error);
		}
		$stmt->bind_param(
			's',
			$session
		);
		$stmt->execute();
		if ($stmt->error) {
			die($stmt->error);
		}
		$data = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		if ($data != NULL) {
			$sql = "UPDATE web_session SET expire = DATE_ADD(NOW(),INTERVAL " . _SESSTIMEOUT . ") WHERE id_mc_player = ?;";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				die($conn->error);
			}
			$stmt->bind_param(
				'i',
				$data['id_mc_player']
			);
			$stmt->execute();
			if ($stmt->error) {
				die($stmt->error);
			}
			$stmt->close();
		}

		return $data;
	}
}
