<?php

//**********************************************************************************************
//                                       ScientificObjectController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: implements the CRUD actions for YiiScientificObjectModel
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\wsModels\WSGermplasmModel;
use \app\models\yiiModels\YiiGermplasmModel;

require_once '../config/config.php';

/**
 * CRUD actions for YiiScientificObjectModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiScientificObjectModel
 * @update [Bonnefont Julien] 12 Septembre, 2019: add visualization functionnalities & cart & cart action to add Event on multipe scientific objects
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class GermplasmController extends Controller {

    /**
     * the Genus column for the csv files
     * @var GENUS
     */
    const GENUS = "Genus";


    /**
     * the species column for the csv files
     * @var SPECIES
     */
    const SPECIES = "Species";

    /**
     * the variety column for the csv files
     * @var VARIETY
     */
    const VARIETY = "Variety";
    
    /**
     * the Accession column for the csv files
     * @var ACCESSION
     */
    const ACCESSION = "Accession";
    
    /**
     * the Lot column for the csv files
     * @var LOT
     */
    const LOT = "Lot";
    
    /**
     * the LotType column for the csv files
     * @var LOT_TYPE
     */
    const LOT_TYPE = "LotType";


    

    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * transform a csv in JSON
     * @param array $csvContent the csv content to transform in json
     * @return string the csv content in json. Unescape the slashes in the csv
     */
    private function csvToArray($csvContent) {
        $arrayCsvContent = [];
        foreach ($csvContent as $line) {
            $arrayCsvContent[] = str_getcsv($line, Yii::$app->params['csvSeparator']);
        }
        return $arrayCsvContent;
    }

   
    /**
     * 
     * @param string $species
     * @return boolean true if the specie uri is in the species list
     */
    private function existSpecies($species) {
        $aoModel = new YiiScientificObjectModel();
        return in_array($species, $aoModel->getSpeciesUriList());
    }


    /**
     * generated the scientific object creation page
     * @return mixed
     */
    public function actionCreate() {
        $germplasmModel = new YiiGermplasmModel();

        $token = Yii::$app->session['access_token'];


        //If the form is complete, register data
        if ($germplasmModel->load(Yii::$app->request->post())) {
            //Store uploaded CSV file
            $document = UploadedFile::getInstance($germplasmModel, 'file');
            $serverFilePath = \config::path()['documentsUrl'] . "GermplasmFiles/" . $document->name;
            $document->saveAs($serverFilePath);

            //Read CSV file content
            $fileContent = str_getcsv(file_get_contents($serverFilePath), "\n");
            $csvHeaders = str_getcsv(array_shift($fileContent), Yii::$app->params['csvSeparator']);
            unlink($serverFilePath);

            foreach ($fileContent as $rowStr) {
                $row = str_getcsv($rowStr, Yii::$app->params['csvSeparator']);
                $genus = $row[0];
                $species = $row[1];
                $variety = $row[2];
                $accession = $row[3];
                for ($i = 2; $i < count($row); $i++) {
                    $values[] = [
                        "genus" => $genus,
                        "species" => $species,
                        "variety" => $variety,
                        "accession" => $accession                            
                    ];
                }
            }

            $germplasmService = new WSGermplasmModel();
            $result = $germplasmService->post($token, "/", $values);

            // If data successfully saved
            if (is_array($result->metadata->datafiles) && count($result->metadata->datafiles) > 0) {
                $arrayData = $this->csvToArray($fileContent);
                return $this->render('_form_germplasm_created', [
                            'model' => $germplasmModel,
                            'insertedDataNumber' => count($arrayData)
                ]);
            } else {

                return $this->render('create', [
                            'model' => $germplasmModel,
                            'errors' => $result->metadata->status
                ]);
            }
 
        } else {
            return $this->render('create', [
                        'model' => $germplasmModel,
            ]);
        }
    }


}
