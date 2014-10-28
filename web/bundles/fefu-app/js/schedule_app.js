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
	var isXlsx = $('input[name=ftype]:checked', '#selector').val() == 'xlsx';
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

function process_wb(wb, isXlsx) {
	var output = "";
	output = to_json(wb, isXlsx)
	var res = new Set();
	get_json_values(output, res);
	var tips = [];
	res.forEach(function(value) {
		tips.push(value);
	});
	$('input').autocomplete({
		source: tips,
		delay:  100		
	});
	var data = [
		['', '', '', '', '']
 	];
 	$("#scheduleTable").handsontable({
		data: data,
		startRows: 2,
		minSpareRows: 1,
		contextMenu: false,
		colHeaders: ["Пара", "Название пары", "Тип занятия", "Преподаватель", "Аудитория", "Период", "День"],
		colWidths: [50, 200, 100, 200, 150, 80, 80, 50],
		columns: [
			{
				editor: 'select',
				selectOptions: [1, 2, 3, 4, 5, 6, 7, 8]
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false
			},
			{
				editor: 'autocomplete',
				source: tips,
				strict: false
			},
			{},
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
	// $('#scheduleTable').find('td').autocomplete({
		// source: tips,
		// delay: 100
	// });
	$('#saveBtn').toggleClass('invisible');
	$('#saveBtn').toggleClass('visible');
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


$(function() {
	metaForm = $('<form>', {id: 'metaInfoForm'});
	var metaVals = [
		{
			'id': 'school',
			'name': 'school',
			'label': 'Школа'
		},
		{
			'id': 'group',
			'name': 'group',
			'label': 'Группа'
		},
		{
			'id': 'study_type',
			'name': 'study_type',
			'label': 'Тип обучения'
		},
		{
			'id': 'course',
			'name': 'course',
			'label': 'Курс'
		},
		{
			'id': 'department',
			'name': 'department',
			'label': 'Направление'
		},
		{
			'id': 'specialization',
			'name': 'specialization',
			'label': 'Профиль'
		}
	];

	metaVals.forEach(function(inputInfo) {
		label = $('<label>', {for: inputInfo['id'], text:inputInfo['label'], class: 'metaLabel'});
		input = $('<input>', {id: inputInfo['id'], name: inputInfo['name'], class:'metaInput'});
		// input.autocomplete({
		// 	source: availableTags,
		// 	delay:  100
		// });
		metaForm.append(label);
		metaForm.append(input);
	});
	$('#metaInfo').append(metaForm);

	$('#saveBtn').click(function(){
		alert(JSON.stringify($('#scheduleTable').data('handsontable').getData()));
	});
});


