<?php
use yii\rbac\item;
?>
<?php
$script = <<< JS
$("#checkAll2").click(function () {
$(".check2").prop('checked', $(this).prop('checked'));
});
JS;
$this->registerJs($script);
?>

<div class="table-responsive">
        
    <table class="table table-striped">
        <tbody>
            <tr>
                <td style="width:2%;"><div ><input type="checkbox" class="check2" id="checkAll2"></div ></td> 
                <td><strong><span class="label label-info">Select All</span></strong></td>
                <td>&nbsp;</td>
            </tr>
        		<?php foreach ($permissions as $permission)
                    {
                    	if(!strpos($permission->name, '*') !== false) {
                    		if($permission->ruleName == '') {
            ?>
            <tr >
                <td style="width:2%;">
                		<?php
                		if (in_array($permission->name, $assigned)){
										  echo "+";
										}else{
										?>
										  <input type="checkbox" class="check2" name="formchildren[<?=item::TYPE_PERMISSION?>][<?=$permission->name?>]" value="1">
										<?php } ?>
                </td>
                <td>
                    <div >
                        <?php echo $permission->name?>                
                    </div>
                </td>
                <td>
                    <?php if ( $permission->type == item::TYPE_PERMISSION ) { ?>
                        PERMISSION
                    <?php } else { ?>
                        ROLE
                    <?php } ?></td>
            </tr>

            <?php }}} ?>
        </tbody>
    </table>
            <button class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span> Assign Permission</button>
</div>