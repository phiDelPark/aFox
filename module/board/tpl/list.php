<?php
if(!defined('__AFOX__')) exit();
if(!empty($_{'board'}['CURRENT_DOCUMENT_LIST'])) $_{'board'} = $_{'board'}['CURRENT_DOCUMENT_LIST'];
?>

<section id="board_list"<?php echo empty($_DATA['srl']) ? '' :' style="margin-top:50px"'; ?>>
	<?php if (empty($_DATA['srl']) && !empty($_CFG['module']['md_category'])) { ?>
		<header>
			<ol class="breadcrumb">
			<?php
				$tmp = explode(',', $_CFG['module']['md_category']);
				foreach ($tmp as $val) {
					echo '<li><a href="'.getUrl('','id',$_DATA['id'],'category', urlencode($val)).'">'.$val.'</a></li>';
				}
			?>
			</ol>
		</header>
	<?php } ?>
	<article class="clearfix">
		<table class="table table-hover list-table">
		<colgroup>
		<col class="col-md-1">
		<col class="col-md-7">
		<col class="col-md-2">
		<col class="col-md-1">
		<col class="col-md-1">
		</colgroup>
		<thead>
			<tr>
				<th><?php echo getLang('number')?></th>
				<th><?php echo getLang('title')?></th>
				<th><?php echo getLang('name')?></th>
				<th><?php echo getLang('view')?></th>
				<th><?php echo getLang('date')?></th>
			</tr>
		</thead>
		<tbody>

		<?php
			$current_page = $_{'board'}['current_page'];
			$total_page = $_{'board'}['total_page'];
			foreach ($_{'board'}['data'] as $key => $val) {
				echo '<tr data-hot-track><th scope="row">'.$val['wr_srl'].'</th>';
				echo '<td class="wr_title"><a href="'.getUrl('srl',$val['wr_srl']).'" onclick="return false">'.escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</td>';
				echo '<td>'.escapeHtml($val['mb_nick'], true).'</td>';
				echo '<td>'.$val['wr_hit'].'</td>';
				echo '<td>'.date('Y/m/d', strtotime($val['wr_update'])).'</td></tr>';
			}
		?>

		</tbody>
		</table>
	</article>
	<nav class="text-center">
		<ul class="pagination">
		<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
	<?php
		for ($i=1; $i <= $total_page; $i++) {
			echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>';
		}
	?>
		<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
		</ul>
	</nav>
	<footer class="clearfix">
		<form class="search-form pull-left col-xs-5 col-sm-4" action="<?php echo getUrl('') ?>" method="get">
			<div class="input-group">
				<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
				<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
				<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
				</span>
			</div>
			<input type="hidden" name="id" value="<?php echo $_DATA['id'] ?>">
		</form>
		<div class="pull-right">
			<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('srl','') ?>" role="button"><i class="fa fa-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
			<a class="btn btn-default" href="<?php echo getUrl('disp','writeDocument','srl','') ?>" role="button"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo getLang('write') ?></a>
		</div>
	</footer>
</section>