<?php
if(!defined('__AFOX__')) exit();

$doc_cnt = DB::count(_AF_DOCUMENT_TABLE_, ['wr_regdate{LIKE}'=>date('Y-m-d').'%']);
$cmt_cnt = DB::count(_AF_COMMENT_TABLE_, ['rp_regdate{LIKE}'=>date('Y-m-d').'%']);
$vis_cnt = DB::count(_AF_VISITOR_TABLE_, ['vs_regdate{LIKE}'=>date('Y-m-d').'%']);

?>


<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comment fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo $doc_cnt?></div>
						<div><?php echo getLang('new_document')?></div>
					</div>
				</div>
			</div>
			<a href="<?php echo getUrl('','admin','document')?>">
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
						<div class="huge"><?php echo $cmt_cnt?></div>
						<div><?php echo getLang('new_comment')?></div>
					</div>
				</div>
			</div>
			<a href="<?php echo getUrl('','admin','comment')?>">
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
						<i class="fa fa-plane fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo $vis_cnt?></div>
						<div><?php echo getLang('new_visit')?></div>
					</div>
				</div>
			</div>
			<a href="<?php echo getUrl('','admin','visit')?>">
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
						<i class="fa fa-question-circle fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">25</div>
						<div>준비중...</div>
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
				<div id="morris-area-chart">준비중...</div>
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
				<div id="morris-donut-chart">준비중...</div>
				<div class="text-right">
					<a href="#"><?php echo getLang('view_details')?> <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i><?php echo getLang('new_document')?></h3>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<?php
						$_list = getDBList(_AF_DOCUMENT_TABLE_, [], 'wr_regdate desc', 1, 10);
						if(empty($_list['error'])){
							foreach ($_list['data'] as $val) {
								echo '<a href="'.getUrl('','srl',$val['wr_srl']).'" class="list-group-item"><span class="badge">'.timePassed($val['wr_regdate']).'</span>'.cutstr(strip_tags($val['wr_title']),50).'</a>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i><?php echo getLang('new_comment')?></h3>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<?php
						$_list = getDBList(_AF_COMMENT_TABLE_, [], 'rp_regdate desc', 1, 10);
						if(empty($_list['error'])){
							foreach ($_list['data'] as $val) {
								echo '<a href="'.getUrl('','rp',$val['rp_srl']).'" class="list-group-item"><span class="badge">'.timePassed($val['rp_regdate']).'</span>'.cutstr(strip_tags($val['rp_content']),50).'</a>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
/* End of file dashbd.php */
/* Location: ./module/admin/dashbd.php */