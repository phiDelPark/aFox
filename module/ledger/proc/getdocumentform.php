<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	$DOC = getDocument($data['ev_srl']);
	if($ex=get_error()) return set_error($ex->getMessage(), $ex->getCode());
	$CATE = getCategorys();
	$items = empty($DOC['ev_items']) ? [] : json_decode($DOC['ev_items'], true);

	ob_start();
?>
<div class='clearfix'></div>

<div id="ledger-form">
	<div class="form-group">
		<select name="ev_category" class="form-control" required>
		<option value=""><?php echo getLang('category')?></option>
		<?php
			foreach ($CATE as $val){
				echo '<option value="'.$val['ca_srl'].'"'.(($val['ca_srl']===$DOC['ev_category'])?' selected="selected"':'').'>'.$val['ca_name'].'</option>';
			}
		?>
		</select>
	</div>

	<div class="form-group">
		<input type="hidden" name="ev_items" value="<?php echo urlencode($DOC['ev_items']) ?>">
		<input type="text" name="ev_title" class="form-control" id="ev_title" required maxlength="255" placeholder="<?php echo getLang('title')?>" value="<?php echo $DOC['ev_title']?>">
	</div>

	<div class="form-group">
		<div class='clearfix' style="padding:2px">
			<input type="radio" id="contractChoice1" name="ev_status" value="0" <?php echo $DOC['ev_status']!='1'?'checked="checked"':'' ?> />
			<label for="contractChoice1">예약</label>
			<input type="text" name="ev_reserve" class="datepicker pull-right" value="<?php echo $DOC['ev_reserve'] ?>">
		</div>
		<div class='clearfix' style="padding:2px">
			<input type="radio" id="finishedChoice1" name="ev_status" value="1" <?php echo $DOC['ev_status']=='1'?'checked="checked"':'' ?> />
			<label for="finishedChoice1">완료</label>
			<input type="text" name="ev_finish" class="datepicker pull-right" value="<?php echo $DOC['ev_finish'] ?>">
		</div>
	</div>

	<div class="form-group">
		<div class='clearfix' style="padding:2px">
		<label>총액</label>
		<input type="text" name="ev_amount" id="totalMoney" class="pull-right input-number" readonly value="<?php echo $DOC['ev_amount'] ?>">
		</div>
		<div class='clearfix' style="padding:2px">
		<label for="paymentMoney">결제</label>
		<input type="number" name="ev_payment" id="paymentMoney" class="pull-right" value="<?php echo $DOC['ev_payment'] ?>">
		</div>
	</div>

	<div class="form-group clearfix">
		<input type="radio" id="paymentType0" name="ev_paytype" value="1" <?php echo $DOC['ev_paytype']=='1'?'checked="checked"':'' ?> />
		<label for="paymentType0">현금</label>
		<input type="radio" id="paymentType1" name="ev_paytype" value="2" <?php echo $DOC['ev_paytype']=='2'?'checked="checked"':'' ?> />
		<label for="paymentType1">카드</label>
		<input type="radio" id="paymentType2" name="ev_paytype" value="3" <?php echo $DOC['ev_paytype']=='3'?'checked="checked"':'' ?> />
		<label for="paymentType2">은행</label>
		<input type="radio" id="paymentType3" name="ev_paytype" value="0" <?php echo $DOC['ev_paytype']=='0'?'checked="checked"':'' ?> />
		<label for="paymentType3">그외</label>
	</div>

	<div class="form-group">
		<div>
		<input type="checkbox" id="receiptType0" name="ev_receipt[]" value="1" <?php echo $DOC['ev_receipt'] & 1 << 1 ?'checked="checked"':'' ?> />
		<label for="receiptType0">세금 계산서</label>
		<input type="checkbox" id="receiptType1" name="ev_receipt[]" value="2" <?php echo $DOC['ev_receipt'] & 1 << 2 ?'checked="checked"':'' ?> />
		<label for="receiptType1">현금 영수증</label>
		</div>
		<div>
		<textarea name="ev_memo" id="ev_memo" rows="3" placeholder="내용"><?php echo stripslashes(str_replace(['\\r', '\\n'], ['', "\n"], $DOC['ev_memo'])) ?></textarea>
		</div>
	</div>
</div>

<div id="ledger-item-form">
	<div class="form-group">
		<a class="btn btn-default" href="#" role="button" data-toggle="ledger-item-form"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> 거래 품목 추가</a>
	</div>
	<div class="form-group">
		<div style="position:absolute;width:100%;padding-right:10px">
		<ul id="id-ev-items" class="scroll">
		<?php
			foreach($items as $v){
				$tmp = '{"caption":"' . $v['caption'] . '",';
					$tmp .= '"count":"' . $v['count'] . '",';
					$tmp .= '"unit":"' . $v['unit'] . '",';
					$tmp .= '"price":"' . $v['price'] . '",';
					$tmp .= '"tax":"' . $v['tax'] . '",';
					$tmp .= '"info":"' . $v['info'] . '"}';
		?>
				<li data-value="<?php echo urlencode($tmp) ?>">
					<div><?php echo $v['caption'] ?></div>
					<div class="right"><?php echo $v['price'] + $v['tax'] ?></div>
				</li>
		<?php
			}
		?>
		</ul>
		</div>
		<div id="ledger-item-panel" class="modal-content" style="display:none">
			<div class="form-group">
				<p><input type="text" id="ev_item_caption" style="width:100%" placeholder="품목명"></p>
				<div class='clearfix' style="margin-top:16px;text-align:right">
					<label for="ev_item_unit">단위</label>
					<input type="text" id="ev_item_unit" style="width:50px;margin-left:8px">
					<div class="pull-right">
						<label for="ev_item_count" style="margin-left:10px">수량</label>
						<input type="number" id="ev_item_count" style="width:80px;margin-left:8px">
					</div>
				</div>
			<div class="form-group">
			</div>
				<div class='clearfix' style="padding:2px">
					<label for="ev_item_price">금액</label>
					<input type="number" id="ev_item_price" class="pull-right">
				</div>
				<div class='clearfix' style="padding:2px">
					<label for="ev_item_tax">세액</label>
					<input type="number" id="ev_item_tax" class="pull-right">
				</div>
				<p class='clearfix' style="margin-top:16px"><textarea id="ev_item_info" rows="2" maxlength="255" style="width:100%" placeholder="메모"></textarea></p>
			</div>
			<div class="modal-footer">
				<div class="pull-left"><button id="itemdelete" class="btn btn-warning btn-sm" style="opacity:0.8;display:none"><i class="glyphicon glyphicon-remove-circle" aria-hidden="true"></i> <?php echo getLang('delete')?></button></div>
				<div class="pull-right">
				<button id="itemclose" class="btn btn-default btn-sm"><?php echo getLang('close')?></button>
				<button id="iteminsert" class="btn btn-sm"><i class="glyphicon glyphicon-ok-circle" aria-hidden="true"></i> <?php echo getLang('insert')?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='clearfix'></div>

<script>
  $( function(){
    $("input.datepicker").datepicker();
  } );
</script>
<?php
	return ['tpl'=>ob_get_clean()];
}

/* End of file getdocumentform.php */
/* Location: ./module/ledger/proc/getdocumentform.php */
