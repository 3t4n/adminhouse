<?php
	use Slim\Http\Request;
	use Slim\Http\Response;


	$app->post('/api/outgoings', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
			$data = $request->getParsedBody();		
			$house_id =$data['house_id'];
			if(House::hasAccess($session->id,$house_id))
					{	
 				$date = DateTime::createFromFormat('D M d Y H:i:s e+',$data['date']);
				$outgoing = new OutGoing(date_format($date, 'Y-m-d'),$data['concept'],intval($data['outgoing']),intval($data['house_id']));
				$outgoing->save();
				}
	}		
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});


	$app->delete('/api/outgoings/{id}', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
			//Falta checar si puede eliminar :)
			$outgoing_id = intval($args['id']);	
			OutGoing::deleteById($outgoing_id);
			//if(House::hasAccess($session->id,$data['house_id']))
			
	}		
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});

	$app->post('/api/outgoings/{id}', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
			$data = $request->getParsedBody();
			if(House::hasAccess($session->id,intval($data['house_id'])))
				{
					$outgoing_id = intval($args['id']);
					$outgoing=OutGoing::getById($outgoing_id);
					 $date = DateTime::createFromFormat('D M d Y H:i:s e+',$data['date']);
					$outgoing->setDate(date_format($date, 'Y-m-d'));
					$outgoing->setConcept($data['concept']);
					$outgoing->setOutgoing(intval($data['outgoing']));
					$outgoing->save();
				}
	}		
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});



?>
