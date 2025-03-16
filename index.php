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

    <link rel="stylesheet" type="text/css" href="./style/semantic/semantic.css">
    <script src="./style/semantic/semantic.js"></script>
	<link rel="stylesheet" type="text/css" href="./style/smallDesign.css">
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
									<option value="" selected disabled hidden> <?php 
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

										$id = '0';
										$json = '{"result":[]};';

										// get json data from  api.php
										if(isset($_POST["id"])) {
											$id = (string)$_POST["id"];
											$json = post(
												"http://nestweb.dothome.co.kr/api.php",
												array("id" => $id) //id data
											).';';
										}

										// reveal line name in option tag
										$list = array(
											'1호선', '2호선', '3호선', '4호선', '5호선', '6호선', '7호선', '8호선', '9호선', 
											'GTX-A', '경춘선', '경의중앙선', '공항철도', '수인분당선', '신분당', '우이신설', '신림선'
										);

										if($id != '') {
											$name = $list[$id];
											echo $name;
										}
										else echo '1호선';
									?> </option>
									<?php 
										$option = ""; // list 기반 option tag 선택지 생성
										$seperator = str_repeat("\t", 9); // purpose to beautify

										for ($i = 0; $i < count($list); $i++) {
											$option .= "<option value=\"".$i."\">".$list[$i]."</option>\n";
											if($i == count($list) - 1) continue;
											$option .= $seperator;
										}

										echo $option;
									?>
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
			echo "let json = ".$json."\n\n\t\tlet line = [\"".$name."\",\"".$id."\"];\n";
		?>
    </script>
	<script src="./script/reprocessData.js" charset='euc-kr'></script>
	<script src="./script/makeChart.js" charset='euc-kr'></script>
    
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