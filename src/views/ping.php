<?php
	use Slim\Http\Request;
	use Slim\Http\Response;

	$app->get('/api/ping',function(Request $request, Response $response, array $args)
	{
			$session = Session::getInstance();
	return json_encode(array("active"=>isset($session->id)));
	});
?>
