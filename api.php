<?php 
	ini_set('display_errors', '0');  // 오류 표시 안함


	/*
		http://m.bus.go.kr/mBus/subway/getStatnByRoute.bms
		http://m.bus.go.kr/mBus/subway/getArvlByInfo.bms

		POST

		subwayId
		statnId
	*/

	function post($url, $data) // post request 구현
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

	function contains($a, $b) { //contains 구현
		$pos = strpos($a, $b);
		if($pos !== false) return true;
		else return false;
	}

	$target_link = 'http://m.bus.go.kr/mBus/subway/getStatnByRoute.bms';

	if(isset($_POST["id"])) {
		$msg = (string)$_POST["id"]; // POST를 통해 id값을 받음
		$id = array(
			'0'=>'1001','1'=>'1002','2'=>'1003','3'=>'1004','4'=>'1005',
			'5'=>'1006','6'=>'1007','7'=>'1008', '8'=>'1009',   // 1~9호선

			'9'=>'1032', // GTX-A
			'10'=>'1067', // 경춘선
			'11'=>'1063', // 경의중앙선
			'12'=>'1065', // 공항철도
			'13'=>'1075', // 수인분당선
			'14'=>'1077', // 신분당선
			'15'=>'1092', // 우이신설
			'16'=>'1094' // 신림선
		);
		$id = $id[$msg]; // POST요청에 사용될 호선명으로 변환

		$res = array();
		
		$result = post($target_link, array("subwayId"=>$id));
		$result = json_decode($result);
		$result = $result->resultList;

		for($i = 0; $i < count($result); $i++) {
			$t = $result[$i];
			$down = ''; $up = '';

			if($t->existYn1 == "T") $down = "f";
			else if($t->existYn1 == "Y") $down = "e";
			else $down = "n";

			if($t->existYn2 == "T") $up = "f";
			else if($t->existYn2 == "Y") $up = "e";
			else $up = "n";

			$id_ = substr($t->statnId, 4);
			$id_ = (int)$id_ + 0;
			$id_ = trim(str_replace(' 80', 'P', ' '.(string)$id_));
			$id_ = trim(str_replace(' 75', 'K', ' '.(string)$id_));
			$id_ = trim(str_replace(' 47', 'L', ' '.(string)$id_));
			if(strlen(preg_replace("/[^0-9]*/s", "", $id_)) > 3) $id_ = substr($id_, 0, 3).'-'.substr($id_, 3-strlen($id_) ,strlen($id_)-3);

			$res[$i] = array(
				'name'=>($t->statnNm), // 역 이름
				'exist_down'=>$down, // 아래방향 도착여부
				'exist_up'=>$up, // 위방향 도착여부
				'id'=>$id_, // 정류장 번호
			);
		}

		if(implode('', $res) != '') echo json_encode(array('result'=>$res));
		else echo $json = '{"result":[]}';
	}
?>