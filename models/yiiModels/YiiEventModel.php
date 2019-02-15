<?php

//******************************************************************************
//                             YiiEventModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 02 jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSEventModel;
use app\models\wsModels\WSConstants;

/**
 * The yii model for an event 
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiEventModel extends WSActiveRecord {
    
    /**
     * @example http://www.phenome-fppn.fr/id/event/96e72788-6bdc-4f8e-abd1-ce9329371e8e
     * @var string
     */
    public $uri;
    const URI = "uri";
    
    /**
     * @example http://www.opensilex.org/vocabulary/oeev#MoveFrom
     * @var string
     */
    public $type;
    const TYPE = "type";
    
    /**
     * @example 2019-01-02T00:00:00+01:00
     * @var string 
     */
    public $date;
    const DATE = "date";
    
    /**
     * @var array
     */
    public $concernedItems; 
    const CONCERNED_ITEMS = "concernedItems";
    const CONCERNED_ITEMS_LABELS = "labels";
    const CONCERNED_ITEMS_URI = "uri";
    const CONCERNED_ITEMS_TYPE_URI = "typeURI";
    
    /**
     * The properties of the event
     * @var array 
     */
    public $properties;
    const PROPERTIES = "properties";
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSEventModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize 
                : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @return array the rules of the attributes
     */
    public function rules() {
       return [ 
           [[YiiEventModel::URI], 'required'],
           [[
                YiiEventModel::TYPE, 
                YiiEventModel::CONCERNED_ITEMS, 
                YiiEventModel::DATE
            ] , 'safe']
        ]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            YiiEventModel::URI => 'URI', 
            YiiEventModel::TYPE => Yii::t('app', 'Type'), 
            YiiEventModel::DATE => Yii::t('app', 'Date')
        ];
    }
    
    /**
     * Allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     * an event
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiEventModel::URI];
        $this->type = $array[YiiEventModel::TYPE];
        if ($array[YiiEventModel::CONCERNED_ITEMS]) {
            foreach ($array[YiiEventModel::CONCERNED_ITEMS] as $concernedItemInArray) {
                $eventConcernedItem  = new YiiConcernedItemModel();
                $eventConcernedItem->uri = $concernedItemInArray->uri;
                $eventConcernedItem->rdfType = $concernedItemInArray->typeURI;
                $eventConcernedItem->labels = $concernedItemInArray->labels;
                $this->concernedItems[] = $eventConcernedItem;
            } 
        } 
        if ($array[YiiEventModel::PROPERTIES]) {
            foreach ($array[YiiEventModel::PROPERTIES] as $propertyInArray) {
                $property  = new YiiPropertyModel();
                $property->arrayToAttributes($propertyInArray);
                $this->properties[] = $property;
            } 
        } 
        $this->date = $array[YiiEventModel::DATE];
    }
    
    /**
     * Transform the json into array
     * @param json jsonList
     * @return array
     */
    private function jsonListOfArraysToArray($jsonList) {
        $toReturn = []; 
        if ($jsonList !== null) {
            foreach ($jsonList as $value) {
                $toReturn[] = $value;
            }
        }
        return $toReturn;
    }

    /**
     * Get the detailed event corresponding to the given uri
     * 
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getEventDetailed($sessionToken, $uri) {
        $eventDetailed = $this->wsModel->getEventDetailed($sessionToken, $uri);

        if (!is_string($eventDetailed)) {
            if (isset($eventDetailed[WSConstants::TOKEN])) {
                return $eventDetailed;
            } else {
                $this->uri = $uri;
                $this->arrayToAttributes($eventDetailed);
                return $this;
            }
        } else {
            return $eventDetailed;
        }
    }

    /**
     * Calls web service and returns the list of events types
     * //SILEX:todo Not used yet. Will be used to generate a dropdown list to
     * select the event type filter
     * //\SILEX
     * @param sessionToken
     * @return list of the events types
     */
    public function getEventsTypes($sessionToken) {
        $eventConceptUri = "http://www.opensilex.org/vocabulary/oeev#Event";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $eventConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
}
