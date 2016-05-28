<?php
if(!defined('__AFOX__')) exit();
?>


<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="fa fa-info-circle"></i>  <strong>aFox</strong> 테스트
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">26</div>
						<div><?php echo getLang('new_document')?></div>
					</div>
				</div>
			</div>
			<a href="#">
				<div class="panel-footer">
					<span class="pull-left"><?php echo getLang('view_details')?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-green">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">12</div>
						<div><?php echo getLang('new_comment')?></div>
					</div>
				</div>
			</div>
			<a href="#">
				<div class="panel-footer">
					<span class="pull-left"><?php echo getLang('view_details')?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-yellow">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">124</div>
						<div><?php echo getLang('new_visit')?></div>
					</div>
				</div>
			</div>
			<a href="#">
				<div class="panel-footer">
					<span class="pull-left"><?php echo getLang('view_details')?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-red">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">13</div>
						<div><?php echo getLang('new_visit')?></div>
					</div>
				</div>
			</div>
			<a href="#">
				<div class="panel-footer">
					<span class="pull-left"><?php echo getLang('view_details')?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Chart 1</h3>
			</div>
			<div class="panel-body">
				<div id="morris-area-chart"></div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i>Chart 2</h3>
			</div>
			<div class="panel-body">
				<div id="morris-donut-chart"></div>
				<div class="text-right">
					<a href="#"><?php echo getLang('view_details')?> <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i>Panel 1</h3>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<a href="#" class="list-group-item">
						<span class="badge">just now</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">4 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">23 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">46 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">1 hour ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">2 hours ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">yesterday</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">two days ago</span>
						document 1
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i>Panel 2</h3>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<a href="#" class="list-group-item">
						<span class="badge">just now</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">4 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">23 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">46 minutes ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">1 hour ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">2 hours ago</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">yesterday</span>
						document 1
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">two days ago</span>
						document 1
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
/* End of file dashbd.php */
/* Location: ./module/admin/dashbd.php */