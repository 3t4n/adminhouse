<?php 

	use Slim\Http\Request;
	use Slim\Http\Response;

	$app->post('/api/auth', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(!isset($session->id))
	{
		$data = $request->getParsedBody();
		$user = User::auth($data['nick'],$data['pass']);
		if($user!=null)
		{
			$session->id=$user->getId();
			$session->rol_id = $user->getRolId();
			return json_encode(array("state"=>"logged"));
		}
		else
		{
			return json_encode(array("error"=>"Contraseña  o nick malos"));
		}
	}
	else
	{
		return json_encode(array("state"=>"logged"));
	}	
	});
?>

