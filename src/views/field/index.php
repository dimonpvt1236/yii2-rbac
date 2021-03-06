<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('rbac', 'Field Access');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-access-index">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>


    <p>
        <?= Html::a(Yii::t('rbac', 'Create Field Access'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'model_name',
                'scenario_name',
                'attribute_name',
                'description',

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
