<?php
declare(strict_types=1);

final class _Global
{
	private static $config = array();
	private static $init = false;
	private static function __init(string $r="")
	{
		if(self::$init) return;
		if($r=="")
		{
			self::$config=Utils::stringToArray(Utils::fileToString(__DIR__ . '/../'."config.json"));
		}
		else
		{
			self::$config=Utils::stringToArray(Utils::fileToString($r));
		}

		self::$init = true;
	}
	public static function getConfig(string $r=""):array
	{		
			self::__init($r);
			return self::$config;
	}

	public static function resetConfig():void
	{
		self::$config=null;
		self::$init = false;
	}

	public static function getDBConfig(string $r=""):array
	{
		self::__init($r);
		return self::$config["database"];
	}

	public static function getStaticConfig(string $r=null):array
	{
		self::__init($r);
		return self::$config["files"];
	
	}

}

?>
