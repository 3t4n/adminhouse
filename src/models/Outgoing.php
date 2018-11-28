<?php
declare(strict_types=1);
class OutGoing
{
	private static $table = "OutGoings";
	protected $instance;
	protected $id;
        protected $date;
	protected $concept;
	protected $outgoing;
	protected $house_id;
	protected $columns;
	public function __construct(string $date,string $concept, int $outgoing, int $house_id)
	{
		$this->name=$name;
		$this->columns = array(":ddate"=>$date,":concept"=>$concept, ":outgoing"=>$outgoing, ":house_id"=>$house_id);
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
					{

					$this->columns = array(":ddate"=>$this->date,":concept"=>$this->concept, ":outgoing"=>$this->outgoing);
					$this->columns[":id"]=$this->id;
					Conex::_query("UPDATE ".self::$table." SET ddate=:ddate, concept=:concept, outgoing=:outgoing WHERE id=:id" , $this->columns);
					}
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
			echo "No se pudo eliminar el outgoing".$e->getMessage();
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
		$outgoing = $res[0];
		$instance = new self($outgoing["ddate"],$outgoing["concept"],intval($outgoing["outgoing"]),intval($outgoing["house_id"]));
		$instance->setId(intval($outgoing["id"]));

		return $instance;
	}

	public function getId():int
	{
		return intval($this->id);
	}
	public function getName():string
	{
		return $this->name;
	}
	public function setDate(string $s):void
	{
		$this->date=$s;
	}
	public function setConcept(string $s):void
	{
		$this->concept=$s;
	}
	public function setOutgoing(int $s):void
	{
		$this->outgoing=$s;
	}
	public function setId(int $s):void
	{
		$this->id=$s;
	}
	public function setHouseId(int $s):void
	{
		$this->house_id=$s;
	}
	
}
?>
