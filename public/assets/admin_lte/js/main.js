(function(){
	$(document).ready(function(){
		
		//datepicker
		$('.datepicker').datepicker({
			format: 'mm/dd/yyyy',
			startDate: '-30d'
		});
		
		$('.start-datepicker').datepicker({
			format: 'mm/dd/yyyy',
			startDate: '-180d'
		});
		
		$('.end-datepicker').datepicker({
			format: 'mm/dd/yyyy',
			startDate: '-180d'
		});
		
		//iCheck
		$('input').iCheck({
		  labelHover: false,
		  cursor: true,
			checkboxClass: 'icheckbox_flat-blue',
    		radioClass: 'iradio_flat'
		});
		
		//dataTables
		$('#mainTable').DataTable({
			responsive: true,
			scroller: true
		});
		
		$('#contentTable').DataTable(/*{
			//responsive: true,
			//deferRender: true,
			//scrollY: 200,
			//scrollCollapse: true,
			//scroller: true
		}*/);
		
		
	});
})();


 