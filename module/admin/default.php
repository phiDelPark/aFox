<?php
if(!defined('__AFOX__')) exit();

$doc_cnt = DB::count(_AF_DOCUMENT_TABLE_, ['wr_regdate{LIKE}'=>date('Y-m-d').'%']);
$cmt_cnt = DB::count(_AF_COMMENT_TABLE_, ['rp_regdate{LIKE}'=>date('Y-m-d').'%']);
$vis_cnt = DB::count(_AF_VISITOR_TABLE_, ['vs_regdate{LIKE}'=>date('Y-m-d').'%']);

?>
	<div class="row">
	  <div class="col-md-3 mb-3">
		<div class="card bg-primary text-white h-100">
		  <div class="card-body py-5">Primary Card</div>
		  <div class="card-footer d-flex">
			View Details
			<span class="ms-auto">
			  <i class="bi bi-chevron-right"></i>
			</span>
		  </div>
		</div>
	  </div>
	  <div class="col-md-3 mb-3">
		<div class="card bg-warning text-dark h-100">
		  <div class="card-body py-5">Warning Card</div>
		  <div class="card-footer d-flex">
			View Details
			<span class="ms-auto">
			  <i class="bi bi-chevron-right"></i>
			</span>
		  </div>
		</div>
	  </div>
	  <div class="col-md-3 mb-3">
		<div class="card bg-success text-white h-100">
		  <div class="card-body py-5">Success Card</div>
		  <div class="card-footer d-flex">
			View Details
			<span class="ms-auto">
			  <i class="bi bi-chevron-right"></i>
			</span>
		  </div>
		</div>
	  </div>
	  <div class="col-md-3 mb-3">
		<div class="card bg-danger text-white h-100">
		  <div class="card-body py-5">Danger Card</div>
		  <div class="card-footer d-flex">
			View Details
			<span class="ms-auto">
			  <i class="bi bi-chevron-right"></i>
			</span>
		  </div>
		</div>
	  </div>
	</div>
	<div class="row">
	  <div class="col-md-6 mb-3">
		<div class="card h-100">
		  <div class="card-header">
			<span class="me-2"><i class="bi bi-bar-chart-fill"></i></span>
			Area Chart Example
		  </div>
		  <div class="card-body">
			<canvas class="chart" width="400" height="200"></canvas>
		  </div>
		</div>
	  </div>
	  <div class="col-md-6 mb-3">
		<div class="card h-100">
		  <div class="card-header">
			<span class="me-2"><i class="bi bi-bar-chart-fill"></i></span>
			Area Chart Example
		  </div>
		  <div class="card-body">
			<canvas class="chart" width="400" height="200"></canvas>
		  </div>
		</div>
	  </div>
	</div>
	<div class="row">
	  <div class="col-md-12 mb-3">
		<div class="card">
		  <div class="card-header">
			<span><i class="bi bi-table me-2"></i></span> Data Table
		  </div>
		  <div class="card-body">
			<div class="table-responsive">
			  <table
				id="example"
				class="table table-striped data-table"
				style="width: 100%"
			  >
				<thead>
				  <tr>
					<th>Name</th>
					<th>Position</th>
					<th>Office</th>
					<th>Age</th>
					<th>Start date</th>
					<th>Salary</th>
				  </tr>
				</thead>
				<tbody>
				  <tr>
					<td>Tiger Nixon</td>
					<td>System Architect</td>
					<td>Edinburgh</td>
					<td>61</td>
					<td>2011/04/25</td>
					<td>$320,800</td>
				  </tr>
				  <tr>
					<td>Garrett Winters</td>
					<td>Accountant</td>
					<td>Tokyo</td>
					<td>63</td>
					<td>2011/07/25</td>
					<td>$170,750</td>
				  </tr>
				</tbody>
				<tfoot>
				  <tr>
					<th>Name</th>
					<th>Position</th>
					<th>Office</th>
					<th>Age</th>
					<th>Start date</th>
					<th>Salary</th>
				  </tr>
				</tfoot>
			  </table>
			</div>
		  </div>
		</div>
	  </div>
	</div>

<?php
/* End of file dashbd.php */
/* Location: ./module/admin/dashbd.php */
