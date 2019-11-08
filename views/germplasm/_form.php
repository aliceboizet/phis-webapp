<?php

//**********************************************************************************************
//                                       _form.php
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 31 2017
// Subject: creation of scientific objects via CSV
//***********************************************************************************************

use Yii;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\file\FileInput;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiGermplasmModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="germplasm-form well">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <?= Yii::$app->session->getFlash('renderArray'); ?>    

    <div class="alert alert-info" role="alert">
        <b><?= Yii::t('app/messages', 'File Rules')?> : </b>
        <ul>
            <li><?= Yii::t('app/messages', 'CSV separator must be')?> "<b><?= Yii::$app->params['csvSeparator']?></b>"</li>
            <li>If you want to create <strong>genus</strong>, you only need to fill the Genus column with the label</li>
            <li>If you want to create <strong>species</strong>, you have to fill the Species column. The Genus is optionnal</li>
        </ul>
        <br/>
        <b><?= Yii::t('app', 'Columns')?> : </b>
        <table class="table table-hover" id="dataset-csv-columns-desc">
            <tr>
                <th style="color:blue">Genus *</th>
                <td><?= Yii::t('app/messages', 'The label or the URI of the genus (e.g Zea)')?></td>
            </tr>
            <tr>
                <th style="color:blue">Species *</th>
                <td><p><?= Yii::t('app/messages', 'The label or the URI of the species (e.g Maize)') ?></td>
            </tr>
            <tr>
                <th style="color:blue">Variety *</th>
                <td><p><?= Yii::t('app/messages', 'The label or the URI of the variety (e.g B73)') ?></td>
            </tr>
            <tr>
                <th style="color:blue">Accession *</th>
                <td><p><?= Yii::t('app/messages', 'The label or the URI of the accession (e.g B73_INRA)') ?></td>
            </tr>
            <tr>
                <th style="color:blue">PlantMaterialLot *</th>
                <td><p><?= Yii::t('app/messages', 'The number of the seedLot (e.g SL_00211)') ?></td>
            </tr>
            <tr>
                <th style="color:blue">LotType *</th>
                <td><p><?= Yii::t('app/messages', 'The type of the PlantMaterialLot (e.g seedLot)') ?></td>
            </tr>
        </table>
    </div>
    
    <p>
        <?php 
            $csvPath = "coma";
            if (Yii::$app->params['csvSeparator'] == ";") {
                $csvPath = "semicolon";
            }
        ?>
        <i><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Template'), \config::path()['basePath'] . 'documents/GermplasmFiles/' . $csvPath . '/germplasmTemplate.csv', ['id' => 'downloadGermplasmTemplate']) ?></i>
        <i style="float: right"><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Example'), \config::path()['basePath'] . 'documents/GermplasmFiles/' . $csvPath . '/germplasmExemple.csv') ?></i>
    </p>
    <?= $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => [
            'maxFileSize' => 2000,
            'pluginOptions'=>['allowedFileExtensions'=>['csv'],'showUpload' => false],
        ]
    ]);
    ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii' , 'Create') , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
