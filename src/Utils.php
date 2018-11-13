<?php
declare(strict_types=1);

final class Utils
{

	public static function fileToString(string $file):string
	{
		$temp="";
		if(!file_exists($file)) throw new InvalidArgumentException("No existe tal archivo");
		$res = fopen($file,'r');
		if(!$res) throw new InvalidArgumentException("No se pudo cargar el archivo de configuración");
		while(($buffer=fgets($res,4096))!==false) $temp.=$buffer;
		return $temp;
	}

	
	public static function stringToFile(string $str,string $file, bool $ow): void
	{
		if(file_exists($file)&&!$ow) throw new InvalidArgumentException("Se está sobreescribiendo un archivo");
		file_put_contents($file,$str);
	}

	public static function stringToHash(string $str, string $salt, int $rounds): string
	{
		for($i=0; $i<$rounds;  $i++)
		{
			$str=hash('sha256',$str.$salt);
		}
		return $str;
	}
	public static function modelArrayToInsertSQL(array $arr):array
	{
		$columns = "";
		$values = "";
		foreach($arr as $key=>$value)
		{
			$values.=$key.",";
			$key=ltrim($key,':');
			$columns.=$key.",";
		}
		$columns=rtrim($columns, ',');
		$values=rtrim($values, ',');
		return array( 0 => $columns, 1=> $values);
	}
	public static function stringToArray(string $str):array
	{
		$ret=json_decode($str, true);	
		switch(json_last_error()) {
			case JSON_ERROR_NONE:
				break;
			case JSON_ERROR_DEPTH:
				throw new Exception("Se excedió el tamaño de la pila");
			case JSON_ERROR_STATE_MISMATCH:
				throw new InvalidArgumentException("El string '".$str."'tiene errores de syntax");
        		case JSON_ERROR_CTRL_CHAR:
				throw new InvalidArgumentException("El string '".$str."'tiene errores de syntax");
			case JSON_ERROR_SYNTAX:
				throw new InvalidArgumentException("El string '".$str."'tiene errores de syntax");
        		case JSON_ERROR_UTF8:
				throw new InvalidArgumentException("El string '".$str."'tiene errores de codificación");
        		default:
				throw new Exception("Error desconocido");
		}
		return $ret;
	}
	public static function saveFile($file, $tipo):void
	{
		$info = pathinfo($file['name']);
		$ext = $info['extension']; // get the extension of the file
		$newname = hash_file('md5', $file['tmp_name']).".".$ext; 
		$target = dirname(__FILE__).'/static/uploads/'.$newname;
		move_uploaded_file($file['tmp_name'], $target);
	}
}

?>
