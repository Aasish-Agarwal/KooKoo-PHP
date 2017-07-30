<?php

function authenticateService($server , $user , $pwd) {
	
	$data = array('email' => $user, 'password' => $pwd);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $server . '/api/signin'  );
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	return $json['token'];
}

function makeAppointment($server , $token, $qid,  $reference) {
        $booked_position = 0;
		
		$data = array('action' => 'book', 'reference' => $reference);

        $data_string = json_encode($data);

        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: Bearer '. $token;
        $headr[] = 'Content-Length: ' . strlen($data_string);

        $url = $server . '/api/queue/' .  $qid . '/appointment';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        $result = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($result, true);

		if ( $json['error'] ) 
		{
			$booked_position = 0;
		}  else {
			$booked_position = $json['position'];
		}

		return $booked_position;
}



function getCurrentPositionIfAlreadyBooked($server , $token, $qid , $reference) {
	$already_booked_position = 0;
	
	$headr[] = 'Authorization: Bearer '. $token;

	$url = $server . '/api/queue/' .  $qid . '/appointment';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url );
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);

	$appointments = $json['appointments'];
	
	foreach ($appointments as $app) {
		$ref = $app['reference'];
		$pos = $app['position'];
		
		if ( $ref == $reference) {
			$already_booked_position  = $pos;
			break;
		}
	}
	return $already_booked_position;
}

?>
