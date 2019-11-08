<?php

//******************************************************************************
//                                       YiiGermplasmModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: The Yii model for the sensors. Used with web services
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSGermplasmModel;

class YiiGermplasmModel extends WSActiveRecord {
       
    /**
     * data generating script
     * @var file
     */
    public $file;
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSGermplasmModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'provenanceUri' => Yii::t('app', 'Provenance (URI)'),
            'provenanceComment' => Yii::t('app', 'Provenance comment'),
            'variables' => Yii::t('app', 'Variable(s)'),
            'file' => Yii::t('app', 'Data file'),
            'documentsUris' => Yii::t('app', 'Documents')
        ];
    }
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }

}