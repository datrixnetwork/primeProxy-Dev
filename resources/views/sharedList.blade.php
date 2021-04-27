<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prim Proxy | Marketting</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="http://207.154.197.92/prime/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="http://207.154.197.92/prime/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="http://207.154.197.92/prime/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="http://207.154.197.92/prime/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="http://207.154.197.92/prime/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<style>
.lblClass2{
    cursor: pointer;
font-size: 15px;
color: #827d7d;
}
.w3-xlarge {
      font-size: 24px!important;
    }
    .w3-display-topright {
    position: absolute;
    right: 0;
    top: 86px;
}
.w3-modal-content {
    width: 500px;
}
.w3-modal {
    z-index: 11;
    display: none;
    padding-top: 100px;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}
</style>
</head>
<body>
<div class="wrapper">

<div id="modal01" class="w3-modal" onclick="this.style.display='none'">
      <span class="w3-button w3-hover-red w3-xlarge w3-display-topright">&times;</span>
      <div class="w3-modal-content w3-animate-zoom">
        <img id="img01" style="width:100%">
      </div>
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Seller  <span id='sellerCode'></span></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="sellerShared" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width=4%>Sr #</th>
                    <th width=4%>Date</th>
                    <th width=4%>Country</th>
                    <th width=8%>Product Name</th>
                    <th width=8%>Order Number</th>
                    <th width=8%>Paypal Email</th>
                    <th width=6%>Price</th>
                    <th width=12%>Order image</th>
                    <th width=12%>Review image</th>
                    <th width=12%>Feedback image</th>
                    <th width=12%>Refund image</th>
                    <th width=5%>Paid</th>
                  </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                  <tr>
                    <th>Sr #</th>
                    <th>Date</th>
                    <th>Country</th>
                    <th>Product Name</th>
                    <th>Order Number</th>
                    <th>Paypal Email</th>
                    <th>Price</th>
                    <th>Order image</th>
                    <th>Review image</th>
                    <th>Feedback image</th>
                    <th>Refund image</th>
                    <th>Comm Paid</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

  <div id='footer'></div>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="http://207.154.197.92/prime/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="http://207.154.197.92/prime/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="http://207.154.197.92/prime/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="http://207.154.197.92/prime/plugins/datatables-buttons/js/buttons.print.min.js"></script>


<script>
  $(function () {
    var sites = {!! $title !!};
    var autoIndex = 0;
    var loadInto ="sellerShared";
    //   var url ='http://localhost/Datrix/dtxOms/public/api/v1/orderSheet/'+sites;
      var url ='http://207.154.197.92/primeProxy-Dev/public/api/v1/orderSheet/'+sites;
      var formData ={};
    $("#"+loadInto).DataTable({
      "buttons": ["copy", "csv", "excel", "pdf", "print"],
      "scrollX": false,
      dom: 'Bfrtip',
		"bFilter": true,
		"bLengthChange": true,
		"bAutoWidth":false ,
		"autoWidth": true,
        "paging": false,
        "ordering": false,
        'serverMethod': 'post',

		'ajax': {
      type:'GET',
      dataType: "json",

			'url':url,
      data: formData,
      headers: {
        'x-clx-id' : 1,
        'x-clx-key': 'kso8GaEgUsdadh7LE796WeRt9P4Mn61Q0PoKEEWq',
      },
		},
		'columnDefs': [
			{
                "targets": 0,
                "render": function ( data, type, row, meta ) {
                    autoIndex++;
                    return '<span><label class="lblClass2">'+autoIndex+'</label></span>';
                    }
            },
			{
				"targets": 1,
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row['created_on']+'</label></span>';
				}
			},
			{
				"targets": 2,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.market_place+'</label></span>';

                    }
			},
			{
				"targets": 3,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.product_name+'</label></span>';

				}
			},
			{
				"targets": 4,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.store_order_no+'</label></span>';
				}
			},
			{
				"targets": 5,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    console.log(row);
                    return '<span><label class="lblClass2">'+row.buyer_email+'</label></span>';
				}
			},
			{
				"targets": 6,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.product_price+'</label></span>';
				}
			},
			{
				"targets": 7,
				data: 'data',
				"render": function ( data, type, row, meta ) {

                    if(row.orderCreated == null){
                        var ordreCreated = "No Order Screen shot yet";
                    }else{
                        var ordreCreated = '<img  onclick="onClick(this)"  src='+row.orderCreated+' width=30%>';
                    }
                    return '<center><label class="lblClass2">'+ordreCreated+'</label></center>';

                    }
			},
			{
				"targets": 8,
				data: 'data',
				"render": function ( data, type, row, meta ) {

                    if(row.reviewAttachment == null){
                        var review = "No Reviews yet";
                    }else{
                        var review = '<img  onclick="onClick(this)"  src='+row.reviewAttachment+' width=30%>';
                    }
                    return '<center><label class="lblClass2">'+review+'</label></center>';


                    }
			},
			{
				"targets": 9,
				data: 'data',
				"render": function ( data, type, row, meta ) {

                    if(row.feedbackAttachment == null){
                        var feedback = "No Feedback yet";
                    }else{
                        var feedback = '<img onclick="onClick(this)" src='+row.feedbackAttachment+' width=30%>';
                    }
                    return '<center><label class="lblClass2">'+feedback+'</label></center>';

                    }
			},
			{
				"targets": 10,
				data: 'data',
				"render": function ( data, type, row, meta ) {

                    if(row.refundAttachment == null){
                        var refund = "No Refund yet";
                    }else{
                        var refund = '<img onclick="onClick(this)" src='+row.refundAttachment+' width=30%>';
                    }
                    return '<center><label class="lblClass2">'+refund+'</label></center>';

          }
			},
			{
				"targets": 11,
				data: 'data',
				"render": function ( data, type, row, meta ) {

                    comPaid = 'No';
                    if(row.is_admin_comm_paid ==1){
                        comPaid = 'Yes';
                    }
                    return '<span><label class="lblClass2">'+comPaid+'</label></span>';
                    }
			},
		],
		'order': [[0, 'desc']],

		"preDrawCallback": function( settings ) {
			$('#'+loadInto+' tbody').empty();
			$('.loader-list').show();
			$('#'+loadInto+'-footer').css( 'visibility', 'visible');
			$('#'+loadInto+'-header').css( 'visibility', 'collapse');
		  },
		  "drawCallback": function( settings ) {
			$('.loader-list').hide();
			$('#'+loadInto+'-footer').css( 'visibility', 'collapse');
			$('#'+loadInto+'-header').css( 'visibility', 'visible');
		  }

    }).buttons().container().appendTo('#sellerShared_wrapper .col-md-6:eq(0)');

  });

  function onClick(element) {
  document.getElementById("img01").src = element.src;
  document.getElementById("modal01").style.display = "block";
}


</script>
</body>
</html>
