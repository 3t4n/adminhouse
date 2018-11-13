<?php
use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/api/me', function (Request $request, Response $response, array $args) {
	$session = Session::getInstance();
	if(isset($session->id))
	{
		$user = User::getById($session->id);
		return json_encode(array(
		"name"=> $user->getName()." ".$user->getLastName(),
		"modules"=> $user->getModules()));
	}
	else
		return $response->withStatus(403)->withHeader('Content-type','application/json');
});
?>
