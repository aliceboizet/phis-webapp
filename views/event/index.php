<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', '{n, plural, =1{Event} other{Events}}'
        , ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn']
            , 'type'
            , [
              'attribute' => 'concernsItems',
              'format' => 'raw',
              'value' => function ($model) {
                return implode(", ", $model->concernsItems[0]->labels);
              }
            ]
            , 'dateTimeString'
        ],
    ]); ?>
</div>