<?php

use Slim\Http\Request;
use Slim\Http\Response;

include("views/validator.php");
include("views/ping.php");
include("views/login.php");
include("views/me.php");
include("views/logout.php");
include("views/register.php");
$app->get('/', function (Request $request, Response $response, array $args) {
//   $test = new User("ma","lol","xxx","lo@lol.com","putos",1, null, "pito", "a");
    return $this->renderer->render($response, 'index.html', $args);
});


