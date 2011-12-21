$(document).ready(function() {
	$('#select-all:checkbox').change(function() {
		checked = $(this).attr('checked');
		$(this).parents('table').children('tbody').children('tr').children('td').children('input:checkbox').each(function() {
			$(this).attr('checked', checked);
		});
	});
});
