<?php
	use Slim\Http\Request;
	use Slim\Http\Response;
	$app->get('/api/sync', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
		$data = json_decode(Utils::fileToString(__DIR__ . '/../../'."export.json"),True);
		foreach($data['reservations'] as $reservation)
		{
			$house = Conex::_query("CALL getHouseID(:name,:listing_id,@id_house)",array(":name"=>$reservation["listing_name"],":listing_id"=>$reservation["listing_id"]));
			$house_id = Conex::_query("SELECT @id_house");
			if(!House::hasAccess($session->id,$house_id[0][0]))
				if($session->rol_id==1)
				{
					$house = House::getById($house_id[0][0]);
					$house->addAccessTo($session->id);
				}
				echo $reservation["number_of_infants"];
				Conex::_query("CALL getAirBNBEarningID(:house_id, :confirmation_code,:booked_date, :earnings,:end_date,:number_of_adults , :number_of_children , :number_of_infants, :listing_id, :listing_name , :nights,:start_date,:thread_id ,@id)",
		array(":house_id"=>$house_id[0][0], ":confirmation_code"=>$reservation["confirmation_code"],":booked_date"=>$reservation["booked_date"], ":earnings"=>floatval(str_replace('$','',str_replace(',','',explode(" ",$reservation["earnings"])[0]))),":end_date"=>$reservation["end_date"],":number_of_adults"=>$reservation["guest_details"]["number_of_adults"] , ":number_of_children"=>$reservation["guest_details"]["number_of_children"] , ":number_of_infants"=>$reservation["guest_details"]["number_of_infants"], ":listing_id"=>$reservation["listing_id"], ":listing_name"=>$reservation["listing_name"] , ":nights"=>$reservation["nights"],":start_date"=>$reservation["start_date"],":thread_id"=>$reservation["thread_id"] ));
		}
	}
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});


?>
