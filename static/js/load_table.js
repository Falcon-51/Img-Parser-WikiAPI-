let table = new DataTable('#example');
	
$(document).ready(function(){
  var empDataTable = $('#example').DataTable({
     dom: 'Blfrtip',
     buttons: [
       {  
          extend: 'copy'
       },
       {
          extend: 'csv',
       },
       {
          extend: 'excel',
       } 
     ] 

  });

});