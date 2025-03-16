/**
 * refactoring �� data set�� Ȱ���� result table ����
 */

const myURL = "http://nestweb.dothome.co.kr";

document.querySelectorAll('option')[Number(line[1]) + 1].setAttribute('selected', 'selected');

let container = document.getElementById('container');
container.innerText = (line[0] != '') ? ('���� �뼱 : ' + line[0]) : '';

let row = document.createElement("table");
row.className = 'chart';

let tempName = "";
json = json["result"];

for (let x = 0; x < json.length; x++) { // �� ������ ���� ������ ǥ ����, row ������ ���� ����
	let cell = document.createElement("tr");
	let name_ = document.createElement("td"); // �� �̸�
	let loc_1 = document.createElement("td"); // �Ʒ��� ����
	let loc_2 = document.createElement("td"); // ���� ����
	let t = json[x];

	if (t.id == "p") {
		if (tempName == "") {
			tempName = json[x - 1].name;
		}
		name_.innerHTML = t.name + " (" + tempName + " �� " + json[x + 1].name + ")";
		name_.style = "padding: 10px";
		name_.colSpan = 3;

		cell.appendChild(name_);

		row.appendChild(cell);

		continue;
	}

	name_.innerHTML = t.name + '<br><small>' + t.id + '</small>';

	// �Ʒ��� ����
	if (t.exist_down == 'f') { //���� ���࿭��
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/fast_down_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}
	else if (t.exist_down == 'e') { // ���� �Ϲݿ���
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/exist_down_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}
	else if (t.exist_down == 'n') { //���� ���� ���� ��
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/none_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_1.appendChild(img);
	}

	// ���� ����
	if (t.exist_up == 'f') { //���� ���࿭��
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/fast_up_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_2.appendChild(img);
	}
	else if (t.exist_up == 'e') { // ���� �Ϲݿ���
		let img = document.createElement('img');
		img.setAttribute('src', myURL + '/asset/exist_up_' + line[1] + '.png');
		img.style.width = "50px";
		img.style.height = "50px";
		loc_2.appendChild(img);
	}
	else if (t.exist_up == 'n') { // ���� ���� ���� ��
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
	intro.innerText = '�ʿ��� ������ ���ΰ�ħ �� Ȯ�� �ٶ��ϴ�.';
	container.appendChild(intro);
}