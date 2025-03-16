/**
 * refactoring 된 data set을 활용한 result table 제작
 */

const myURL = "http://nestweb.dothome.co.kr";

document.querySelectorAll('option')[Number(line[1]) + 1].setAttribute('selected', 'selected');

let container = document.getElementById('container');
container.innerText = (line[0] != '') ? ('현재 노선 : ' + line[0]) : '';

let row = document.createElement("table");
row.className = 'chart';

let tempName = "";
json = json["result"];

for (let x = 0; x < json.length; x++) { // 역 개수에 따른 유동적 표 생성, row 변수에 붙일 생각
	let cell = document.createElement("tr");
	let name_ = document.createElement("td"); // 역 이름
	let loc_1 = document.createElement("td"); // 아래쪽 라인
	let loc_2 = document.createElement("td"); // 위쪽 라인
	let t = json[x];

	if (t.id == "p") {
		if (tempName == "") {
			tempName = json[x - 1].name;
		}
		name_.innerHTML = t.name + " (" + tempName + " → " + json[x + 1].name + ")";
		name_.style = "padding: 10px";
		name_.colSpan = 3;

		cell.appendChild(name_);

		row.appendChild(cell);

		continue;
	}

	name_.innerHTML = t.name + '<br><small>' + t.id + '</small>';

	// 아래쪽 라인
	if (t.exist_down == 'f') { //역에 급행열차
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/fast_down_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}
	else if (t.exist_down == 'e') { // 역에 일반열차
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/exist_down_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}
	else if (t.exist_down == 'n') { //역에 열차 없을 때
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/none_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}

	// 위쪽 라인
	if (t.exist_up == 'f') { //역에 급행열차
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/fast_up_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_2.appendChild(img);
	}
	else if (t.exist_up == 'e') { // 역에 일반열차
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/exist_up_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_2.appendChild(img);
	}
	else if (t.exist_up == 'n') { // 역에 열차 없을 때
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/none_' + line[1] + '.png');
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

if (json.length == 0) {
	let intro = document.createElement('div');
	intro.innerText = '필요할 때마다 새로고침 후 확인 바랍니다.';
	container.appendChild(intro);
}