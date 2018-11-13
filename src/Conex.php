<?php
declare(strict_types=1);
final class Conex extends pdo
{
	private static $instance = null;
	public function __construct(array $database){
		parent::__construct("mysql:dbname=".$database["database"].";host=".$database["hostname"].";charset=utf8",$database["user"],$database["password"]);
	}

	public static function getInstance(array $r=null):object
	{
		if(self::$instance==null)
		{
			self::con($r);
		}
		return self::$instance;
	}
	public static function con(array $r = null):void
	{	
		if(self::$instance == null)
		{
			try
			{
			if($r==null)
				self::$instance=new self(_Global::getDBConfig());
			else
				self::$instance = new self($r);		
			self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e)
			{
				self::$instance = null;		
				throw new Exception($e->getMessage());
			}
		}
	}

	public static function _query(string $str, array $params=array()):array
	{
		self::con();
		$statement = self::$instance->prepare($str);
		foreach($params as $key=>&$val)
			$statement->bindParam($key,$val);

		$statement->execute();
		if (strpos($str, 'SELECT') !== false) 
		{
			$res = $statement->fetchAll(PDO::FETCH_BOTH);	
			if($res!==false)
				return $res;
		}
		return array();
		
	}


	public static function ping():bool
	{
		try{
			self::$instance->query('SELECT 1+1');
		}
		catch(PDOException $e)
		{
			return false;
		}
		return true;
	}

}
?>
