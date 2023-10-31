$( document ).ready(function(){
	$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){$("#success-alert").slideUp(500);});
	$(".select2").select2();
	$(".select2bs4").select2({
      theme: 'bootstrap4'
    });
	$('#deleteCheckBox').change(function() {
		if($(this).is(":checked")) 
		{
			if ( confirm(v2['deleteCheckBoxAlert']) )
				this.checked = true;
			else
				this.checked = false;
		}      
	});
	
	$('input[name=postSlug]').keyup(function() {$('#inputPostSlug').text($(this).val());});

	$('#apiDatatable').DataTable( {
	columnDefs: [
	{ "searchable": false, "targets": 0 },
	{ "orderable": false, "targets": [1,2,3,4] },
	{ className: 'text-center', targets: [1,2,3,4] }
	],
	"responsive": true,
    "paging":   true,
    "ordering": true,
	"searching": false,
    "info":     false
    } );
	
  $('#categoryDatatable').DataTable( {
	columnDefs: [
	{ "searchable": false, "targets": 1 },
	{ "orderable": false, "targets": [0,-2,-3] },
	{ className: 'text-center', targets: [1,2,3,4] }
	],
    "paging":   true,
    "ordering": true,
	"searching": true,
    "info":     false
    } );
	
	$('#linksDatatable').DataTable( {
	columnDefs: [
	{ "searchable": false, "targets": 1 },
	{ "orderable": false, "targets": [0,-2,-3] },
	{ className: 'text-center', targets: [1,2,3,4] }
	],
    "paging":   true,
    "ordering": true,
	"searching": true,
    "info":     false
    } );
	
	$('#customTypesDatatable').DataTable( {
	columnDefs: [
	{ "searchable": false, "targets": 0 },
	{ "orderable": false, "targets": 0 },
	{ className: 'text-center', targets: [0,1,2,3] }
	],
    "paging":   true,
    "ordering": true,
	"searching": false,
    "info":     false
    } );
});

function confirm_alert(node) {return confirm(v2['confirmAlert']);}
function confirm_alert2(node) {return confirm(v2['confirmAlert2']);}

//Site Image
$("#buttonRemoveGraph").on("click", function()
{
	$("#graphImagePreview").attr('src', '');
	$("#graphImageFile").attr('value', '');
	$("#buttonRemoveGraph").toggleClass("d-none");
});