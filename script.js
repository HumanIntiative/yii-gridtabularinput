var taskList = $(idListName)

$(addTaskRow).on('click', addTaskOnClick)
$(delTaskRow).on('click', delTaskOnClick)
$(btnSave).on('click', btnSaveOnClick)
// $(titleName).on('keyup', addTaskOnKeyup)

function addTaskOnKeyup(e) {
	e.preventDefault()
	if (e.keyCode==13) addTaskOnClick(e)
}
function addTaskOnClick(e) {
	var clone  = $('tr:last-child', taskList).clone(withDataAndEvents),
		num    = clone.children('td:eq(0)').html(),
		odd    = clone.hasClass('odd'),
		length = clone.children().length,
		input  = null

	clone.removeClass(odd ? 'odd' : 'even')
	clone.addClass(odd ? 'even' : 'odd')
	clone.children('td:eq(0)').find('.num').html(parseInt(num) + 1)

	for (var i = 1; i < length; i++) {
		if (i == (length-1)) continue
		input = clone.children('td:eq('+i+')').find('.form-control')
		input.val('')
		if (input.is('div')) {
			input.html('')
		}
		if (input.hasClass(calendarTask)) {
			input.datepicker({
				'language': 'id',
				'format': 'yyyy-mm-dd',
				'viewformat': 'yyyy-mm-dd',
				'placement': 'right',
				'autoclose': 'true'
			})
		}
	}

	clone.children('td:last-child').find('button'+delTaskRow).removeClass('disabled').removeAttr('disabled')
	clone.children('td:last-child').find('button'+addTaskRow).unbind('click')
	clone.children('td:last-child').find('button'+addTaskRow).on('click', addTaskOnClick)
	clone.children('td:last-child').find('button'+delTaskRow).unbind('click')
	clone.children('td:last-child').find('button'+delTaskRow).on('click', delTaskOnClick)
	clone.appendTo(taskList)
}
function delTaskOnClick(e) {
	$(this).parents('tr.'+taskRowClone).remove()

	taskList.children('tr').each(function(index){
		$(this).children('td:eq(0)').find('.num').html(index + 1)
	})
}
function btnSaveOnClick(e) {
	var errCount = 0
	e.preventDefault()

	$(titleName).each(function(index){
		if ($(this).val().length<=0) {
			errCount++
			$(this).focus()
		}
	});

	if (errCount==0) {
		$(formName).submit()
	} else {
		bootbox.alert(messageSubmit)
	}
}