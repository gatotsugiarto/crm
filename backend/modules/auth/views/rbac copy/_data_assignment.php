<?php
use yii\rbac\item;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="table-responsive">
        
    <table class="table table-striped">
        <tbody>
            <?php foreach ($children as $child)
                    {
            ?>
            <tr >
                <td>
                    <div >
                        <?php echo $child->roleName?>                
                    </div>
                </td>
                <td >
                    
                </td>
                <td style="width:2%;">
                	<div >
                		<?php
                		$url = Url::toRoute(['deleteuserassignment', 'item_name' => $child->roleName, 'user_id' => $_GET['id']]);
    								echo Html::a('<span class="glyphicon glyphicon-remove"></span>',$url,[
                  	 'title'=>'Delete',
                  	 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete Other Roles & Permissions?'),
                  	 'data-method' => 'post',
                  	 'data' => ['item_name'=> $child->roleName, 'user_id' => $_GET['id'], 'test-name'=>'this is just for testing'],
                  	      ]);
                		?>
                	</div>
                </td>
            </tr>

            <?php } ?>
        </tbody>
    </table>
            
</div>