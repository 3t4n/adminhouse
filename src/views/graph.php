<?php
	use Slim\Http\Request;
	use Slim\Http\Response;
	$app->get('/api/graph/{id}', function (Request $request, Response $response, array $args){
	$session = Session::getInstance();
	if(isset($session->id))
	{
			$house_id = intval($args['id']);
			if(House::hasAccess($session->id,$house_id))
				
				return json_encode(
					array(
						"earnings"=>Conex::_query("SELECT UNIX_TIMESTAMP(DATE_FORMAT(earning_date,'%Y-%m-01')) as date, SUM(earnings) AS total FROM Earnings WHERE house_id=:house_id GROUP BY YEAR(earning_date),MONTH(earning_date)",array(":house_id"=>$house_id)),
						"outgoings"=>Conex::_query("SELECT UNIX_TIMESTAMP(DATE_FORMAT(ddate,'%Y-%m-01')) as date, SUM(outgoing) as total FROM OutGoings WHERE house_id=:house_id GROUP BY YEAR(ddate),MONTH(ddate)",array(":house_id"=>$house_id)),
						"earningsT"=>Conex::_query("SELECT e.date as date, e.total as e,IFNULL(o.total,0) as o FROM (SELECT UNIX_TIMESTAMP(DATE_FORMAT(earning_date,'%Y-%m-01')) as date, SUM(earnings) as total FROM Earnings WHERE Earnings.house_id=:house_id GROUP BY UNIX_TIMESTAMP(DATE_FORMAT(earning_date,'%Y-%m-01'))) as e
	 LEFT JOIN
(SELECT UNIX_TIMESTAMP(DATE_FORMAT(ddate,'%Y-%m-01')) as date, COALESCE(SUM(outgoing), 0) as total FROM OutGoings WHERE OutGoings.house_id=:house_id GROUP BY UNIX_TIMESTAMP(DATE_FORMAT(ddate,'%Y-%m-01'))) as o ON e.date=o.date ;",array(":house_id"=>$house_id))

					)
				);
	}		
	else
	{
		return $response->withStatus(403)->withHeader('Content-type','application/json');
	}	
	});


?>
