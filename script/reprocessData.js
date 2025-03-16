/**
 * �־��� JSON data�� ����, ���� �� refactoring
 */

function filteringData(data) { // ��ġ�� �׸� ���͸�
	const result = [];
	const seenNames = new Map();

	data.result.forEach(item => {
		const { name, exist_down, exist_up } = item;

		if (seenNames.has(name)) { // �̹� �ִ� name�̸�
			const existingItem = seenNames.get(name);

			if (exist_down === "e" || exist_up === "e") { // e�� ������ e�� �����ͷ� ��ü
				seenNames.set(name, item);
			}
		} else {
			seenNames.set(name, item); // ���ο� name�̸� �߰�
		}
	});

	seenNames.forEach(item => result.push(item));
	return { result };
}

function processData(data, keyword) { // json���� P(�б���)�� �ִ� �����͸� ���� �з� �� ����

	const pItems = [];
	const nonPItems = [];

	data.result.forEach(item => {
		if (item.id.startsWith(keyword)) {
			pItems.push(item);  // P
		} else {
			nonPItems.push(item);  // non-P
		}
	});

	pItems.sort((a, b) => a.id.localeCompare(b.id)); //P �������� ����
	nonPItems.sort((a, b) => a.id.localeCompare(b.id)); //non-P �������� ����

	const result = nonPItems.concat(pItems);
	return { result };
}

function addPointData(data, keyword) { // P(�б���)�� ���� �߰�
	let result = [];
	let pIds = data.result.filter(item => item.id.startsWith(keyword)).map(item => parseInt(item.id.slice(1))); // P(�б���) �ִ� id��

	data.result.forEach(item => {
		const value = item.id;
		const idValue = parseInt(value.replace(keyword, ""));

		if (idValue == pIds[0]) {
			let pData = {};
			pData["name"] = "�б���";
			pData["exist_down"] = pData["exist_up"] = pData["id"] = keyword.toLowerCase();
			result.push(pData);
		}
		result.push(item);
	});

	return { result };
}

function rearrangeBySubId(data) {
	let result = data.result.sort((a, b) => {
		const sepId = (id) => id.split("-").map(Number); // Id ����

		let [aMain, aSub] = sepId(a.id);
		let [bMain, bSub] = sepId(b.id);

		if (aMain !== bMain) {
			return aMain - bMain;
		}
		return ~~aSub - ~~bSub; // ~~undefined (subId�� ����) -> 0
	});

	return { result };
}


json = filteringData(json); // �ߺ� ����
json = rearrangeBySubId(json); // subId�� �ִ� �����͵� ��ġ ����, ex) 234-1, 234-2

const Pline = ["0", "4"]; // 1, 5ȣ��

if (Pline.includes(line[1])) {
	json = processData(json, "P"); // �б��� ������ �������� �з�
	json = addPointData(json, "P");
}