<?php
declare(strict_types=1);
class House
{
	private static $table = "Houses";
	protected $instance;
	protected $id;
	protected $name;
	protected $airbnb_listing_id;
	protected $columns;
	public function __construct(string $name,int $airbnb_listing_id)
	{
		$this->name=$name;
		$this->airbnb_listing_id=$airbnb_listing_id;
		$this->columns = array(":nick"=>$this->airbnb_listing_id,":name"=>$this->name);
	}

	public function save():void
	{
			$pre = Utils::modelArrayToInsertSQL($this->columns);
			
			try
			{
				if($this->id==null)
				{
					Conex::_query("INSERT INTO ".self::$table."(".$pre[0].") VALUES (".$pre[1].");" ,$this->columns);
					$this->id=Conex::_query("SELECT LAST_INSERT_ID()")[0][0];
				}
				else
					Conex::_query("UPDATE ".self::$table."SET name=:name, airbnb_listing_id=:airbnb_listing_id WHERE id=:id" ,$this->columns);
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
	}

	public static function deleteById($id):void
	{
		try
		{
			Conex::_query("DELETE FROM ".self::$table." WHERE id=:id",array(":id"=>intval($id)));
		}
		catch(Exception $e)
		{
			echo "No se pudo eliminar el usuario".$e->getMessage();
		}
	}

	public static function getAll(int $pagination=1000, int $page=1):array
	{
		return Conex::_query("SELECT * FROM ".self::$table." LIMIT ".$pagination." OFFSET ".($page-1)*$pagination);
	}


	public static function getById(int $id):self
	{
		$res=Conex::_query("SELECT * FROM ".self::$table." WHERE id = :id",array(":id"=>$id));
		if($res===array())
			return null;
		$house = $res[0];
		$instance = new self($house["name"],intval($house["airbnb_listing_id"]));
		$instance->setId(intval($house["id"]));
		return $instance;
	}

	public static function getHouse(int $i):array
	{
		$name = Conex::_query("SELECT name FROM Houses WHERE id=:id",array(":id"=>$i))[0][0];
		$outgoings = Conex::_query("SELECT * FROM OutGoings WHERE house_id=:house_id ORDER BY ddate",array(":house_id"=>$i));
		$earnings = Conex::_query("SELECT * FROM Earnings INNER JOIN AirBNBres ON Earnings.id = AirBNBres.earning_id WHERE Earnings.house_id=:house_id ORDER BY start_date", array(":house_id"=>$i) );
		return array("name"=>$name,"outgoings"=>$outgoings,"earnings"=>$earnings);
	}

	public function getId():int
	{
		return intval($this->id);
	}
	public function getName():string
	{
		return $this->name;
	}
	public function getAirbnb_listing_id():string
	{
		return $this->airbnb_listing_id;
	}
	
	public function setId(int $i):void
	{
		$this->id = $i;
	}
	public function setName(string $s):void
	{
		$this->name=$s;
	}
	public function setAirbnb_listing_id(int $s):void
	{
		$this->airbnb_listing_id=$s;
	}
	public function addAccessTo(int $user_id):void
	{
		$data = array(":house_id"=>$this->id,":user_id"=>$user_id);
		$pre = Utils::modelArrayToInsertSQL($data);
		Conex::_query("INSERT INTO HousesUsers(".$pre[0].") VALUES (".$pre[1].");" ,$data);
	}
	public static function hasAccess(int $user_id,int $house_id):bool
	{
		$res = Conex::_query("SELECT * FROM HousesUsers WHERE house_id = :house_id AND  user_id = :user_id", array(":house_id"=>$house_id,":user_id"=>$user_id));
		return $res!=array();
	}
}
?>
