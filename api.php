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
			'1001','1002','1003','1004','1005','1006','1007','1008','1009',   // 1~9호선
			'1032', // GTX-A
			'1067', // 경춘선
			'1063', // 경의중앙선
			'1065', // 공항철도
			'1075', // 수인분당선
			'1077', // 신분당선
			'1092', // 우이신설
			'1094' // 신림선
		);
		$id = $id[$msg]; // POST요청에 사용될 호선명으로 변환

		$res = array();
		
		$result = post($target_link, array("subwayId"=>$id));
		$result = json_decode($result);
		$result = $result->resultList;

		/*  Exist Type
		 *  fast(급행) : f
		 *  exist(일반) : e
		 *  none(없음) : n
		 */
		for($i = 0; $i < count($result); $i++) {
			$t = $result[$i];
			if(str_contains($t->statnNm, "종착")) continue; // 2호선 성수종착 배제

			$down = ''; $up = '';

			if($t->existYn1 == "T") $down = "f";
			else if($t->existYn1 == "Y") $down = "e";
			else $down = "n";

			if($t->existYn2 == "T") $up = "f";
			else if($t->existYn2 == "Y") $up = "e";
			else $up = "n";

			// t->statnId 형태 : 1001000158, 1094000420, 1001002211, 1001080222, ....등등

			$id_ = substr($t->statnId, 4); // line code (ex: 1001)부터 자름

			if($id == "1094") { // 신림선 (특별케이스)
				$id_ = "S" . ltrim($id_, '0');
			} 
			else if ($id == "1032"){
				$id_ = "X" . ((int)ltrim($id_, '0') - 245);

			} else if (preg_match('/^(080|075|047|065)(\d{3})$/', $id_, $match)) { // id 앞자리에 P, K, L, A가 들어가야하는 경우
				$prefixList = [
					'080' => 'P',
					'075' => 'K',
					'047' => 'L',
					'065' => 'A'
				];
				$id_ = $prefixList[$match[1]] . $match[2];
			} else if (preg_match('/^(0065|0047|0068)(\d{2})$/', $id_, $match)){ // id 앞자리에 A, L, D가 들어가야 하는 경우
				$prefixList = [
					'0065' => 'A',
					'0047' => 'L',
					'0068' => 'D'
				];
				$id_ = $prefixList[$match[1]] . $match[2];
			} else if (preg_match('/^(00068)(\d{1})$/', $id_, $match)){ // id 앞자리에 D가 들어가야 하는 경우
				$id_ = "D0" . $match[2];
			} else {
				$id_ = ltrim($id_, '0'); // 맨앞 0들 제거
				if(strlen(preg_replace("/[^0-9]*/s", "", $id_)) > 3) { // 4자리 이상 digit인 경우, ex : id가 211-2 처럼 지선인 경우
					$id_ = substr($id_, 0, 3).'-'.substr($id_, 3-strlen($id_) ,strlen($id_)-3);
				}
			}

			array_push($res, array(
				'name'=>($t->statnNm), // 역 이름
				'exist_down'=>$down, // 아래방향 도착여부
				'exist_up'=>$up, // 위방향 도착여부
				'id'=>$id_, // 정류장 번호
			));
		}

		if(implode('', $res) != '') echo json_encode(array('result'=>$res));
		else echo $json = '{"result":[]}';
	}
?>