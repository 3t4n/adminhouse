<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/api/logout', function (Request $request, Response $response, array $args) {
	$session = Session::getInstance();
	$session->destroy();
	return json_encode(array("status"=>"ok"));
});
?>
