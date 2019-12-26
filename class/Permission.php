<?php
class Permission
{
	// Nada
	const	SIN_ACCESO					=   0;

	// Constantes para la web normal


	// Constantes para Web staff
	const	STAFF_WEBCONSOLE			=    1;
	const	STAFF_BANLIST 				=    2;
	const	STAFF_HELPOP 				=    4;
	const	STAFF_IPLIST				=    8;
	const	STAFF_RANKS					=   16;
	const	STAFF_TRANSACTION			=   32;
	const	STAFF_MIHELPER				=   64;
	const	STAFF_WEBCONSOLEACCEPTCMDS	=  128;
	const	STAFF_RANGOSEXTRACMDS		=  256;
	const	STAFF_PERMISSIONSEX			=  512;


	public static function checkPermission($toCheck, $perm)
	{
		return (bool) (($toCheck & $perm) == $perm);
	}
}
