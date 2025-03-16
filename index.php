<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Subway Information">
    <meta name="description" content="Real-time information">

    <meta property="og:title" content="Subway Information" />
    <meta property="og:description" content="Real-time information" />
    <meta property="og:locale" content="ko_KR" />
    <title>Subway Information</title>

    <script src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="http://nestweb.dothome.co.kr/semantic/semantic.css">
    <script src="http://nestweb.dothome.co.kr/semantic/semantic.js"></script>
	<style>
		table.chart {
			margin: 0;
            padding: 0;
            border-collapse: collapse;
		}
		table.chart th, table.chart td {
			margin: 0;
            padding: 0;
            border-collapse: collapse;
			width: 100%;
			text-align: center;
		}
		table.chart th:nth-child(3n-1), table.chart td:nth-child(3n-2) {
            border-bottom: 1px solid #cccccc;
        }
        table.chart caption {
            margin: 0;
            padding: 0;
            text-align: right;
			border-collapse: collapse;
        }
		table.chart thead th {
			background-color: #e9e9e9 !important;
            border-bottom: 1px solid #999999;
			border-collapse: collapse;
        }
		table.chart tfoot th {
            background-color: #e2e2e2 !important;
        }
		input {
			height: 2em;
			width: 3.7em;
			box-sizing: border-box;
			border-radius: 7px;
			border: none;
			font-size: 1em;
			font-family: "Montserrat", "Noto Sans KR", sans-serif;
			background-color: #E7E7E7;
			background-repeat: no-repeat;
			background-position: 1em;
			background-size: 7%;
			outline: none;
		}
		img {
			display: block;
		}
		div.fixedBottom {
			position: fixed;
			width: 100%;
			bottom: 0;
			left: 0;
		}
	</style>
</head>
<body>
    <div class="ui black inverted vertical footer segment">
        <div class="ui inverted secondary menu">
            <a id="menu" class="item" href="http://nestweb.dothome.co.kr">
                메인
            </a>
        </div>
    </div>
    <div class="ui grid">
        <div class="two wide column"></div>
        <div class="twelve wide column"></div>
        <div class="two wide column"></div>
    </div>
	<div class="ui grid">
        <div class="one wide column"></div>
        <div class="fourteen wide column">
			<form action="./index.php" method="post">
				<table width="100%">
					<tr>
						<td>
							<h2 style="float:left">
								Live Subway Info <small>(experimental)</small>
							</h2>
						</td>
						<td>
							<span style="float:right">
								<select id="id" name="id">
									<option value="" selected disabled hidden>
									<?php 
										function post($url, $data)
										{
											$curl = curl_init($url);
											curl_setopt($curl, CURLOPT_POST, true);
											curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
											curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
											$response = curl_exec($curl);
											curl_close($curl);
											return $response;
										}

										$id=0;
										$json = '{"result":[]};';


										if(isset($_POST["id"])) {
											$id = (string)$_POST["id"];
											$json = post(
												"http://nestweb.dothome.co.kr/api.php", // api.php
												array("id"=>$id)
											).';';
										}

										if($id != '') {
											$name = array(
												'0'=>'1호선', '1'=>'2호선', '2'=>'3호선', '3'=>'4호선', '4'=>'5호선', '5'=>'6호선',
												'6'=>'7호선', '7'=>'8호선', '8'=>'9호선', '9'=>'GTX-A', '10'=>'경춘선', '11'=>'경의중앙선',
												'12'=>'공항철도', '13'=>'수인분당선', '14'=>'신분당선', '15'=>'우이신설', '16'=>'신림선'
											)[$id];
										}
										else {
											$id = '0';
											$name = '';
										}

										// option 태그에서 고른 값 표시
										if(isset($_POST['id'])) {
											echo array(
												'1호선', '2호선', '3호선', '4호선', '5호선', '6호선', '7호선',
												'8호선', '9호선', 'GTX-A', '경춘선', '경의중앙선', '공항철도', '수인분당선',
												'신분당', '우이신설', '신림선'
											)[(string)$_POST['id']];
										} 
										else echo '1호선';
									?>
									</option>
									<option value="0">1호선</option>
									<option value="1">2호선</option>
									<option value="2">3호선</option>
									<option value="3">4호선</option>
									<option value="4">5호선</option>
									<option value="5">6호선</option>
									<option value="6">7호선</option>
									<option value="7">8호선</option>
									<option value="8">9호선</option>
									<option value="9">GTX-A</option>
									<option value="10">경춘선</option>
									<option value="11">경의중앙선</option>
									<option value="12">공항철도</option>
									<option value="13">수인분당선</option>
									<option value="14">신분당</option>
									<option value="15">우이신설</option>
									<option value="16">신림선</option>
								</select>
								<input type="submit">
							</span>
						</td>
					</tr>
				</table>
			</form>
			<br>
			<div id="container"></div>
		</div>
        <div class="one wide column"></div>
    </div>
    <script>

        <?php 
			// option 태그 내 php 코드에서 받은 JSON을 js로 출력
			echo "\n\t\tlet json = ".$json."\n\t\tlet line = [\"".$name."\",\"".$id."\"];\n";
		?>

		function filteringData(data) { // 겹치는 항목 필터링
			const result = [];
			const seenNames = new Map();

			data.result.forEach(item => {
				const { name, exist_down, exist_up } = item;

				if (seenNames.has(name)) { // 이미 있는 name이면
					const existingItem = seenNames.get(name);

					if (exist_down === 'e' || exist_up === 'e') { // e가 있으면 e인 데이터로 교체
						seenNames.set(name, item);
					} 
				} else {
					seenNames.set(name, item); // 새로운 name이면 추가
				}
			});

			seenNames.forEach(item => result.push(item));
			return { result };
		}

		json = filteringData(json); // 중복 삭제

		const myURL = "http://nestweb.dothome.co.kr";
		
		json = json['result']; // 지하철 정보

		document.querySelectorAll('option')[Number(line[1]) + 1].setAttribute('selected', 'selected');
		let container = document.getElementById('container');
		container.innerText = (line[0] != '') ? ('현재 노선 : ' + line[0]) : '';

		var row = document.createElement("table");
		row.className = 'chart';

		for (let x = 0; x < json.length; x++) { // 역 개수에 따른 유동적 표 생성, row 변수에 붙일 생각
			let cell = document.createElement("tr");
			let name_ = document.createElement("td"); // 역 이름
			let loc_1 = document.createElement("td"); // 아래쪽 라인
			let loc_2 = document.createElement("td"); // 위쪽 라인
			let t = json[x];

			name_.innerHTML = t.name + '<br><small>' + t.id + '</small>';

			// 아래쪽 라인
			if(t.exist_down == 'f') { //역에 급행열차
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/fast_down_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}
			else if(t.exist_down == 'e'){ // 역에 일반열차
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/exist_down_' + line[1] + '.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}
			else if(t.exist_down == 'n'){ //역에 열차 없을 때
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/none_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_1.appendChild(img);
			}

			// 위쪽 라인
			if(t.exist_up == 'f') { //역에 급행열차
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/fast_up_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}
			else if(t.exist_up == 'e'){ // 역에 일반열차
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/exist_up_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}
			else if(t.exist_up == 'n'){ // 역에 열차 없을 때
				let img = document.createElement('img');
				img.setAttribute('src', myURL + '/asset/none_'+line[1]+'.png');
				img.style.width = "50px";
				img.style.height = "50px";
				loc_2.appendChild(img);
			}

			cell.appendChild(name_);
			cell.appendChild(loc_1);
			cell.appendChild(loc_2);

			row.appendChild(cell);	
		}


		container.appendChild(row);
		container.appendChild(document.createElement("br"));


		if(json.length == 0) {
			let intro = document.createElement('div');
			intro.innerText = '필요할 때마다 수동으로 새로고침 후 확인 바랍니다.';
			container.appendChild(intro);
		}
    </script>
    
	<br>

	<div style="height:100px"></div>
	<div class="fixedBottom">
		<div class="ui black inverted vertical footer segment">
			<div class="ui grid">
				<div class="two wide column"></div>
				<div class="eight wide column">
					<div class="ui left aligned container">
						Copyright &copy; 2025. Team WebNest
						<br>
						All rights reserved.
					</div>
				</div>
				<div class="five wide column">
				</div>
				<div class="one wide column"></div>
			</div>
		</div>
	</div>

    <script>
        $('#menu').click(function () {
            $('.ui.left.sidebar').sidebar('setting', 'transition', 'push').sidebar('toggle');
        });
    </script>
</body>
</html>