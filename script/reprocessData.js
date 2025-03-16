/**
 * 주어진 JSON data의 순서, 형태 등 refactoring
 */

function filteringData(data) { // 겹치는 항목 필터링
	const result = [];
	const seenNames = new Map();

	data.result.forEach(item => {
		const { name, exist_down, exist_up } = item;

		if (seenNames.has(name)) { // 이미 있는 name이면
			const existingItem = seenNames.get(name);

			if (exist_down === "e" || exist_up === "e") { // e가 있으면 e인 데이터로 교체
				seenNames.set(name, item);
			}
		} else {
			seenNames.set(name, item); // 새로운 name이면 추가
		}
	});

	seenNames.forEach(item => result.push(item));
	return { result };
}

function processData(data, keyword) { // json에서 P(분기점)이 있는 데이터를 따로 분류 및 정렬

	const pItems = [];
	const nonPItems = [];

	data.result.forEach(item => {
		if (item.id.startsWith(keyword)) {
			pItems.push(item);  // P
		} else {
			nonPItems.push(item);  // non-P
		}
	});

	pItems.sort((a, b) => a.id.localeCompare(b.id)); //P 오름차순 정렬
	nonPItems.sort((a, b) => a.id.localeCompare(b.id)); //non-P 오름차순 정렬

	const result = nonPItems.concat(pItems);
	return { result };
}

function addPointData(data, keyword) { // P(분기점)인 지점 추가
	let result = [];
	let pIds = data.result.filter(item => item.id.startsWith(keyword)).map(item => parseInt(item.id.slice(1))); // P(분기점) 있는 id들

	data.result.forEach(item => {
		const value = item.id;
		const idValue = parseInt(value.replace(keyword, ""));

		if (idValue == pIds[0]) {
			let pData = {};
			pData["name"] = "분기점";
			pData["exist_down"] = pData["exist_up"] = pData["id"] = keyword.toLowerCase();
			result.push(pData);
		}
		result.push(item);
	});

	return { result };
}

function rearrangeBySubId(data) {
	let result = data.result.sort((a, b) => {
		const sepId = (id) => id.split("-").map(Number); // Id 구분

		let [aMain, aSub] = sepId(a.id);
		let [bMain, bSub] = sepId(b.id);

		if (aMain !== bMain) {
			return aMain - bMain;
		}
		return ~~aSub - ~~bSub; // ~~undefined (subId가 없음) -> 0
	});

	return { result };
}


json = filteringData(json); // 중복 삭제
json = rearrangeBySubId(json); // subId가 있는 데이터들 위치 조정, ex) 234-1, 234-2

const Pline = ["0", "4"]; // 1, 5호선

if (Pline.includes(line[1])) {
	json = processData(json, "P"); // 분기점 데이터 한쪽으로 분류
	json = addPointData(json, "P");
}