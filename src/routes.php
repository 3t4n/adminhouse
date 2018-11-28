<?php

use Slim\Http\Request;
use Slim\Http\Response;

include("views/validator.php");
include("views/ping.php");
include("views/login.php");
include("views/me.php");
include("views/logout.php");
include("views/register.php");
include("views/syncairbnb.php");
include("views/house.php");
include("views/outgoings.php");
include("views/graph.php");
$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'index.html', $args);
});


