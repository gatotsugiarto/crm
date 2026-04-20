<?php
use yii\rbac\item;

?>

<?php
$script = <<< JS
$("#checkAll").click(function () {
$(".check").prop('checked', $(this).prop('checked'));
});
JS;
$this->registerJs($script);
?>

<div class="table-responsive">
        
    <table class="table table-striped">
        <tbody>
            <tr>
                <td style="width:2%;"><div ><input type="checkbox" class="check" id="checkAll"></div ></td> 
                <td><strong><span class="label label-info">Select All</span></strong></td>
                <td>&nbsp;</td>
            </tr>
            <?php 
            foreach ($roles as $role)
                {
                	if($role->name != 'owner'){
            ?>
            <tr >
                <td style="width:2%;">
                		<?php
                		if (in_array($role->name, $assigned)){
										  echo "+";
										}else{
										?>
										  <input type="checkbox" class="check" name="formchildren[<?=item::TYPE_ROLE?>][<?=$role->name?>]" value="1">
										<?php } ?>
                </td>
                
                <td>
                    <div >
                        <?php echo $role->name?>            
                    </div>
                </td>
                <td>
                    <?php if ( $role->type == item::TYPE_ROLE ) { ?>
                        ROLE
                    <?php } else { ?>
                        PERMISSION
                    <?php } ?></td>
            </tr>

            <?php }} ?>
        </tbody>
    </table>
</div>