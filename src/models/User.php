<?php
declare(strict_types=1);

class User 
{
	private static $table = "Users";
	protected $instance;
	protected $id;
	protected $rol_id;
	protected $boss_id;
	protected $email;
	protected $name;
	protected $lastname;
	protected $nick;
	protected $airbnb;
	protected $airpass;
	public function __construct(string $nick,string $name,string $lastname,string $email,string $password,int $rol_id,  $boss_id, string $airbnb, string $airpass)
	{
		$this->nick=$nick;
		$this->name=$name;
		$this->lastname=$lastname;
		$this->email = $email;
		$this->password=Utils::stringToHash($password,"lol",1);
		$this->rol_id=$rol_id;
		$this->boss_id=$boss_id;
		$this->airbnb=$airbnb;
		$this->airpass=$airpass;
		$this->columns = array(":nick"=>$this->nick,":name"=>$this->name,":lastname"=>$this->lastname,":email"=>$this->email,":pass"=>$this->password,":rol_id"=>$rol_id,":boss_id"=>$this->boss_id,":airbnb"=>$this->airbnb,":airpass"=>$this->airpass);
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
					Conex::_query("UPDATE ".self::$table."SET nick=:nick, name=:name, lastname=:lastname, email=:email, pass=:pass, rol_id=:rol_id, boss_id=:boss_id, airbnb=:airbnb, airpass:airpass WHERE id=:id" ,$this->columns);
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

	public static function auth(string $nick,string $pass)
	{
		$res=Conex::_query("SELECT * FROM ".self::$table." WHERE nick=:nick AND pass=:pass",array(":nick"=>$nick,":pass"=> Utils::stringToHash($pass,"lol",1)));
		if($res===array())
			return null;
		$user = $res[0];
		$instance = new self($nick,$user["name"],$user["lastname"],$user["email"],$user["pass"],intval($user["rol_id"]), $user["boss_id"], $user["airbnb"], $user["airpass"]);
		$instance->setId(intval($user["id"]));
		return $instance;
	}

	public static function getById(int $id):self
	{
		$res=Conex::_query("SELECT * FROM ".self::$table." WHERE id = :id",array(":id"=>$id));
		if($res===array())
			return null;
		$user = $res[0];
		$instance = new self($user["nick"],$user["name"],$user["lastname"],$user["email"],$user["pass"],intval($user["rol_id"]), $user["boss_id"], $user["airbnb"], $user["airpass"]);
		$instance->setId(intval($user["id"]));
		return $instance;
	}

	public static function emailIsAvailable(string $m):bool
	{
		return !boolval(Conex::_query("SELECT EXISTS(SELECT * FROM Users WHERE email = :email)",array(":email"=>$m))[0]);
	}

	public static function nickIsAvailable(string $m):bool
	{
		return !boolval(Conex::_query("SELECT EXISTS(SELECT * FROM Users WHERE nick = :nick)",array(":email"=>$m))[0]);
	}


	public function getId():int
	{
		return intval($this->id);
	}
	public function getName():string
	{
		return $this->name;
	}
	public function getLastname():string
	{
		return $this->lastname;
	}
	public function getEmail():string
	{
		return $this->email;
	}
	public function getPassword():string
	{
		return $this->pass;
	} 
	public function getRolId():int
	{
		return $this->rol_id;
	}
	public function getBossId():int
	{
		return $this->boss_id;
	}
	public function getAirbnb():string
	{
		return $this->airbnb;
	}
	public function getAirpass():string
	{
		return $this->airpass;
	}
	public function setId(int $i):void
	{
		$this->id = $i;
	}
	public function setName(string $s):void
	{
		$this->name=$s;
	}
	public function setLastname(string $s):void
	{
		$this->lastname=$s;
	}
	public function setEmail(string $s):void
	{
		$this->email=$s;
	}
	public function setPassword(string $s):void
	{
		$this->pass=$s;
	} 
	public function setIdRol(int $i):void
	{
		$this->id_r=$i;
	}
	public function setIdBoss(int $i):void
	{
		$this->boss_id=$i;
	}
	public function setAirbnb(string $s):void
	{
		$this->airbnb=$s;
	}
	public function setAirpass(string $s):void
	{
		$this->airpass=$s;
	}

	public function getModules():array
	{
		$res = Conex::_query("SELECT name, route FROM Modules INNER JOIN Rol_Modules ON Rol_Modules.module_id = Modules.id WHERE Rol_Modules.rol_id=:rol_id ",array(":rol_id"=>$this->rol_id));
		return $res;
	}
	public function getHouses():array
	{
		$res = Conex::_query("SELECT house_id, name FROM Houses INNER JOIN HousesUsers ON Houses.id=HousesUsers.house_id WHERE HousesUsers.user_id = :user_id",array(":user_id"=>$this->id));
		return $res;
	}
}
?>
