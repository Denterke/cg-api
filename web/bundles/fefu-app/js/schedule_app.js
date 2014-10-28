var tips = [];

function get_json_values(obj, res) {
	for(var key in obj) {
		if (obj.hasOwnProperty(key)) {
			res.add(key);
			if (typeof obj[key] === 'object' && obj[key] !== null) {
				get_json_values(obj[key], res);
			} else {
				res.add(obj[key]);
			}
		}
	}
}

function processFiles(files) {
	var file = files[0];
	var reader = new FileReader();
	reader.onload = function(e) {	
		var data = e.target.result;
		var wb;
		if (isXlsx) {
			var arr = String.fromCharCode.apply(null, new Uint8Array(data));
			wb = XLSX.read(btoa(arr), {type: 'base64'});
		} else {
			var cfb = XLS.CFB.read(data, {type: 'binary'});
			wb = XLS.parse_xlscfb(cfb);
		}
		process_wb(wb, isXlsx);
	};
	var isXlsx = file.name.split('.').pop() == 'xlsx';
	if (isXlsx) {
		reader.readAsArrayBuffer(file);
	} else {
		reader.readAsBinaryString(file);		
	}
}

function to_csv(workbook, isXlsx) {
	var result = [];
	workbook.SheetNames.forEach(function(sheetName) {
		var csv = isXlsx ? XLSX.utils.sheet_to_csv(workbook.Sheets[sheetName]) : XLS.utils.make_csv(workbook.Sheets[sheetName]);
		if(csv.length > 0){
			result.push("SHEET: " + sheetName);
			result.push("");
			result.push(csv);
		}
	});
	return result.join("\n");
}


function initTables() {
	var data = [
		['', '', '', '', '', '', '']
 	];
 	$("#scheduleTable").handsontable({
		data: data,
		startRows: 2,
		minSpareRows: 1,
		contextMenu: false,
		colHeaders: ["Пара", "Название пары", "Тип занятия", "Преподаватель", "Аудитория", "Период", "День"],
		colWidths: [50, 200, 120, 200, 150, 80, 80, 50],
		columns: [
			{
				editor: 'select',
				selectOptions: [1, 2, 3, 4, 5, 6, 7, 8]
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'select',
				selectOptions: ['Каждая неделя', 'Четная неделя', 'Нечетная неделя']
			},
			{
				editor: 'select',
				selectOptions: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
			}
		]
 	});
	var data = [
		['', '', '', '', '', 'Без профиля']
 	];
 	$("#metaInfoTable").handsontable({
		data: data,
		startRows: 2,
		contextMenu: false,
		colHeaders: ["Школа", "Группа", "Тип обучения", "Курс", "Направление", "Профиль"],
		colWidths: [150, 100, 140, 70, 200, 150],
		columns: [
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'select',
				selectOptions: [1, 2, 3, 4, 5]
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false,
				delay: 100
			}
		]
 	});
	if (!$('#scheduleBtns').hasClass('visible')) {
		if ($('#scheduleBtns').hasClass('invisible')) {
			$('#scheduleBtns').toggleClass('invisible');
		}
		$('#scheduleBtns').toggleClass('visible');
	}
}

function process_wb(wb, isXlsx) {
	var output = "";
	output = to_json(wb, isXlsx)
	var res = new Set();
	get_json_values(output, res);
	tips = [];
	res.forEach(function(value) {
		tips.push(value);
	});
	initTables();
	// $('input').autocomplete({
	// 	source: tips,
	// 	delay:  100		
	// })
}

function to_json(workbook, isXlsx) {
	var result = {};
	workbook.SheetNames.forEach(function(sheetName) {
		var roa = isXlsx ? 
			XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]) :
			XLS.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
		if(roa.length > 0){
			result = roa;
		}
	});
	return result;
}

function validateValue(field, value) {
	var err = '';
	var validators = {
		'Школа': {
			nullable: false
		},
		'Группа': {
			nullable: false,
			check: function(val) {
				var pattern = /[a-zA-Z]/g;
				var result = pattern.exec(val); 
				if (result !== null) {
					return 'В названии группы обнаружена латинская буква!';
				}
				return true;
			}
		},
		'Тип обучения': {
			nullable: false
		},
		'Курс': {
			nullable: false,
			convert: function(val) {
				return val + ' курс';
			}
		},
		'Направление': {
			nullable: false
		},
		'Профиль': {
			nullable: true,
			nuller: function() {
				return 'Без профиля';
			}
		},
		'Пара': {
			nullable: false,
			convert: function(val) {
				return val + ' пара';
			}
		},
		'Название пары': {
			nullable: false
		},
		'Тип занятия': {
			nullable: true,
			nuller: function() {
				return 'Не определен';
			}
		},
		'Преподаватель': {
			nullable: true,
			nuller: function() {
				return 'преподаватель не определен';
			}
		},
		'Аудитория': {
			nullable: true,
			nuller: function() {
				return '';
			},
			check: function(val) {
				var pattern = /[\u0400-\u04FF]\d+/g;
				var result = pattern.exec(val); 
				if (result !== null) {
					return 'В названии аудитории обнаружена русская буква!';
				}
				return true;
			}
		},
		'Период': {
			nullable: false,
			convert: function(val) {
				switch(val) {
					case 'Каждая неделя':
						return [7, 0];
					case 'Четная неделя':
						return [14, 7];
					case 'Нечетная неделя':
						return [14, 0];
				}
			}
		},
		'День': {
			nullable: false,
			convert: function(val) {
				var days = ['Пн','Вт','Ср','Чт','Пт','Сб'];
				return days.indexOf(val) + 1;
			}
		}
	};
	if (!validators.hasOwnProperty(field)) {
		err = 'Для поля ' + field + ' не существует метода обработки! Это не ваша ошибка.';
		alert(err);
		return false;
	}
	validator = validators[field];
	value = $.trim(value);
	if (!value && !validator.nullable) {
		err = 'Поле ' + field + ' должно содержать значение!';
		alert(err);
		return false;
	} else if (!value) {
		value = validator.nuller();
	}
	var pattern = /\n/;
	if (pattern.exec(value) !== null) {
		alert('В поле ' + field + 'не должно содержаться переноса строки!');
		return false;
	}
	if (validators[field].hasOwnProperty('check')) {
		var res = validator.check(value);
		if (res !== true) {
			alert(res);
			return false;
		}
	}
	if (validator.hasOwnProperty('convert')) {
		value = validator.convert(value);
	}
	return value;
}

function getScheduleTemplate() {
	var content = [];
	var group = '';
	var metaInfoCols = $('#metaInfoTable').data('handsontable').getColHeader();
	var metaInfo = $('#metaInfoTable').data('handsontable').getData();
	var str = [];
	for (var i = 0; i < metaInfoCols.length; i++) {
		var val = validateValue(metaInfoCols[i], metaInfo[0][i]); 
		if (val === false) {
			return {
				result: false,
				inMeta: true,
				colNum: i,
				rowNum: 0,
				str: 'error'
			};
		}
		if (i == 1) {
			group = val;
		}
		str.push(val);
	}
	content.push(str.join(';'));
	var mainInfoCols = $('#scheduleTable').data('handsontable').getColHeader();
	var mainInfo = $('#scheduleTable').data('handsontable').getData();
	for (var i = 0; i < mainInfo.length - 1; i++) {
		str = [];
		var weekAddition = 0;
		for (var j = 0; j < mainInfoCols.length; j++) {
			var val = validateValue(mainInfoCols[j], mainInfo[i][j]); 
			if (val === false) {
				return {
					result: false,
					inMeta: false,
					colNum: j,
					rowNum: i,
					str: 'error'
				};
			}
			if (j == 5) {
				weekAddition = val[1];
				val = val[0];
			}
			if (j == 6) {
				val += weekAddition;
			}
			str.push(val);
		}
		content.push(str.join(';'));
	}
	return {
		result: true,
		inMeta: false,
		colNum: 0,
		rowNum: 0,
		str: content.join(';\n') + ';',
		group: group
	};
}


$(function() {
	$('#saveBtn').click(function(){
		content = getScheduleTemplate();
		if (!content.result) {
			if (content.inMeta) {
				$('#metaInfoTable').handsontable('selectCell', 0, content.colNum);
			} else {
				$('#scheduleTable').handsontable('selectCell', content.rowNum, content.colNum);
			}
			return;
		}
		saveAs(
			new Blob(
				[content.str],
				{type: "text/plain;charset=UTF-8"}
			),
			content.group + '.csv'
		);
	});
	$('#clearBtn').click(function(){
		initTables();
	});

});


