<?php
	use Slim\Http\Request;
	use Slim\Http\Response;
	$app->get('/api/house/{id}', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
			$house_id = intval($args['id']);
			if(House::hasAccess($session->id,$house_id))
				return json_encode(House::getHouse($house_id));
	}		
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});


?>
