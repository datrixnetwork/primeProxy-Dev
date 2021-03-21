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
<style>
.lblClass2{
    cursor: pointer;
font-size: 15px;
color: #827d7d;
}
</style>
</head>
<body>
<div class="wrapper">


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
                    <th>Marketplace</th>
                    <th>Product name</th>
                    <th>Order number</th>
                    <th>Price</th>
                    <th>Review screenshot</th>
                    <th>Feedback screenshot</th>
                    <th>Refund screenshot</th>
                    <th>Commission Paid</th>
                  </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                  <tr>
                    <th>Marketplace</th>
                    <th>Product name</th>
                    <th>Order number</th>
                    <th>Price</th>
                    <th>Review screenshot</th>
                    <th>Feedback screenshot</th>
                    <th>Refund screenshot</th>
                    <th>Commission Paid</th>
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
<script src="http://207.154.197.92/prime/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="http://207.154.197.92/prime/dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    var sites = {!! $title !!};
    var loadInto ="sellerShared";
      var url ='http://localhost/Datrix/dtxOms/public/api/v1/orderSheet/'+sites;
      var formData ={};
    $("#"+loadInto).DataTable({
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
      "scrollX": false,
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
                    return '<span><label class="lblClass2">'+row.market_place+'</label></span>';
				}
			},
			{
				"targets": 1,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.product_name+'</label></span>';
                    }
			},
			{
				"targets": 2,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.order_no+'</label></span>';
				}
			},
			{
				"targets": 3,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    return '<span><label class="lblClass2">'+row.product_price+'</label></span>';
				}
			},
			{
				"targets": 4,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    if(row.reviewAttachment == null){
                        var review = "No Reviews yet";
                    }else{
                        var review = '<a href='+row.reviewAttachment+' download><img src='+row.reviewAttachment+' width=20%></a>';
                    }
                    return '<center><label class="lblClass2">'+review+'</label></center>';
				}
			},
			{
				"targets": 5,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    if(row.feedbackAttachment == null){
                        var feedback = "No Feedback yet";
                    }else{
                        var feedback = '<a href='+row.feedbackAttachment+' download><img src='+row.feedbackAttachment+' width=20%></a>';
                    }
                    return '<center><label class="lblClass2">'+feedback+'</label></center>';
                    }
			},
			{
				"targets": 6,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    if(row.refundAttachment == null){
                        var refund = "No Refund yet";
                    }else{
                        var refund = '<a href='+row.refundAttachment+' download><img src='+row.refundAttachment+' width=20%></a>';
                    }
                    return '<center><label class="lblClass2">'+refund+'</label></center>';
                    }
			},
			{
				"targets": 7,
				data: 'data',
				"render": function ( data, type, row, meta ) {
                    comPaid = 'No';
                    if(row.is_comm_paid ==1){
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
</script>
</body>
</html>
