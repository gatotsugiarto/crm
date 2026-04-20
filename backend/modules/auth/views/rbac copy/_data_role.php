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
                        <?php echo $child->name?>
                    </div>
                </td>
                <td >
                    <?php if ( $child->type == item::TYPE_PERMISSION ) { ?>
                        PERMISSION
                    <?php } else { ?>
                        ROLE
                    <?php } ?>
                </td>
                <td style="width:2%;">
                	<?php
                	$url = Url::toRoute(['deleteroleassigment', 'child' => $child->name, 'parent' => $_GET['id']]);
    							echo Html::a('<span class="glyphicon glyphicon-remove"></span>',$url,[
                   'title'=>'Delete',
                   'data-confirm' => Yii::t('yii', 'Are you sure you want to delete Roles & Permissions?'),
                   'data-method' => 'post',
                        ]);
                	?>
                	<!--div >
                		<a href="delete?id=<?=$child->name?>&role_id=<?=$_GET['id']?>"><span><i class="glyphicon glyphicon-remove"></i></span></a>
                	</div-->
                </td>
            </tr>

            <?php } ?>
        </tbody>
    </table>
            
</div>