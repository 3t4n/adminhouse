<?php 

	use Slim\Http\Request;
	use Slim\Http\Response;

	$app->post('/api/register', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(!isset($session->id))
	{
		$data = $request->getParsedBody();
		$user = new User($data["nick"],$data["name"],$data["lastname"],$data["email"],$data["pass"],1, null, $data["airbnb"], $data["airpass"]);
		$user->save();
		if($user!=null)
		{
			$session->id=$user->getId();
			$session->rol_id = $user->getRolId();
			return json_encode(array("state"=>"logged"));
		}
		else
		{
			return json_encode(array("error"=>"Error desconocido"));
		}
	}
	else
	{
		return json_encode(array("state"=>"logged"));
	}	
	});
?>

