<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/api/validator/email', function (Request $request, Response $response, array $args)
	{
		$data = $request->getParsedBody();
		return json_encode(array("status"=>User::emailIsAvailable($data["email"])));
	});

$app->post('/api/validator/nick', function (Request $request, Response $response, array $args)
	{
		$data = $request->getParsedBody();
		return json_encode(array("status"=>User::emailIsAvailable($data["nick"])));
	});




?>
