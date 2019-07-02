<?php

//******************************************************************************
//                                       data_visualization.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 24 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


/**
 * @var model app\models\yiiModels\YiiScientificObjectModel
 * @var variables array
 * @var data array
 * @var this yii\web\View
 */
?>

<div class="scientific-object-data-visualization">
    <a   role="button" data-toggle="collapse" href="#data-visualization-form" aria-expanded="true" aria-controls="data-visualization-form" style="font-size: 24px; line-height: 1.5em;">Search criteria <i class ="fa-large fa fa-search"></i></a>

    <div class="collapse in" id="data-visualization-form" >
        <?php
        $form = ActiveForm::begin();
        if (empty($variables)) {
            echo "<p>" . Yii::t('app/messages', 'No variables linked to the experiment of the scientific object.') . "</p>";
        } else {
            ?>
            <div class="form-row">

                <fieldset style="border: 1px solid #5A9016; padding: 10px;" >
                    <legend style="width: auto; border: 0; padding: 10px; margin: 0; font-size: 16px; text-align: center; font-style: italic" >
                        Data Search
                    </legend>

                    <div class="form-row">
                        <div class="form-group required col-md-6">
                            <label class="control-label" ><?= Yii::t('app', 'Variable') ?></label>

                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'variable',
                                'data' => $variables,
                                'value' => $selectedVariable ? $selectedVariable : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select a variable ...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text('UPDATE'); }",
                                ]
                            ]);
                            ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label" ><?= Yii::t('app', 'Provenance') ?></label>

                            <?php
                            // Create Provenance select values array
                            foreach ($this->params['provenances'] as $uri => $provenance) {
                                $provenancesArray[$uri] = $provenance->label . " (" . $uri . ")";
                            }
                            ?>
                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'provenances',
                                'data' => $provenancesArray,
                                'value' => $selectedProvenance ? $selectedProvenance : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select provenance ...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text('UPDATE'); }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <?=
                            \kartik\date\DatePicker::widget([
                                'name' => 'dateStart',
                                'options' => ['placeholder' => Yii::t('app', 'Enter date start')],
                                'value' => isset($dateStart) ? $dateStart : null,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'orientation' => 'bottom'
                                ],
                                'pluginEvents' => [
                                    "changeDate" => "function() { $('#scientific-object-data-visualization-submit-button').text('UPDATE');  }",
                                ]
                            ])
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <?=
                            \kartik\date\DatePicker::widget([
                                'name' => 'dateEnd',
                                'value' => isset($dateEnd) ? $dateEnd : null,
                                'options' => ['placeholder' => Yii::t('app', 'Enter date end')],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'orientation' => 'bottom'
                                ],
                                'pluginEvents' => [
                                    "changeDate" => "function() { $('#scientific-object-data-visualization-submit-button').text('UPDATE');  }",
                                ]
                            ])
                            ?>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="form-group" style="margin-bottom: 0px;">

                <?= Html::checkbox("show", isset($show) ? $show : false, ['id' => 'showWidget', 'label' => 'Show Images', 'onchange' => 'doThings(this);']) ?>

            </div>

            <div class="form-row">
                <div id="photoFilter">
                    <fieldset style="border: 1px solid #5A9016; padding: 10px;" >
                        <legend style="width: auto; border: 0; padding: 10px; margin: 0; font-size: 16px; text-align: center; font-style: italic" >
                            Image Search
                        </legend>

                        <div class ="form-group required" >
                            <?php
                            echo '<label class="control-label" >Type</label>';
                            echo \kartik\select2\Select2::widget([
                                'name' => 'imageType',
                                'data' => $this->params['imagesType'],
                                'value' => $imageTypeSelected ? $imageTypeSelected : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select image type ...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text('UPDATE');  }",
                                ],
                            ]);
                            ?>
                        </div>
                        <div class="form-group">

                            <label class="control-label" >Filter Position</label>


                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'position',
                                'data' => Yii::$app->params['image.filter']['metadata.position'],
                                'value' => $selectedPosition ? $selectedPosition : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select position ...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text('UPDATE'); }",
                                ],
                            ]);
                            ?>




                        </div>

                    </fieldset>

                </div>
            </div>

            <?= Html::submitButton(Yii::t('app', 'SHOW'), ['class' => 'btn btn-primary ', 'id' => 'scientific-object-data-visualization-submit-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>
        <div id="visualization-images">

            <?php
            if (isset($data) && isset($show) && $show == true && !empty($data)) {
                echo "<div class='image-visualization ' style='height:146px;'><br><div class='alert alert-info' id='scientific-object-data-visualization-alert-div' role='alert-info'>
                    <p>You have to click on data to see images</p>   </div></div>";
            }
            ?>



        </div>

        <div class="data-visualization-chart ">
            <?php
        }
        if (isset($data)) {
            if (empty($data)) {
                echo "  <div class='well '><p>" . Yii::t('app/messages', 'No result found.') . "</p></div>";
            } else {
                $series = [];
                $series[] = ['name' => $data["scientificObjectData"][0]["label"],
                    'data' => $data["scientificObjectData"][0]["data"]];


                $url2 = Url::to(['image/search-from-scientific-object']);
                $objectURI = $model->uri;
                if ($show) {
                    echo Highcharts::widget([
                        'id' => 'graphic',
                        'options' => [
                            'title' => ['text' => $variables[$data["variable"]]],
                            'xAxis' => [
                                'type' => 'datetime',
                                'title' => 'Date',
//                        ],
//                        'chart' => [
//                            'zoomType' => 'x',
//                            'events'=> [
//                                'click' =>  new JsExpression("function(event){  alert('TEST appel ajax'+\"$url2\");}")
//                                
//                            ]
                            ],
                            'yAxis' => [
                                'title' => null,
                                'labels' => [
                                    'format' => '{value:.2f}'
                                ]
                            ],
                            'series' => $series,
                            'tooltip' => [
                                'xDateFormat' => '%Y-%m-%d %H:%M',
                            ],
                            'plotOptions' => [
                                'series' => [
                                    'cursor' => 'pointer',
                                    'point' => [
                                        'events' => [
                                            'click' => new JsExpression(" function() {"
                                                    // . "alert ('la serie X en YYYY-MM-DDTHH:MM:SSZ : '+Highcharts.dateFormat('%a %d %b %H:%M:%S', this.x));"
                                                    . "var searchFormData = new FormData();"
                                                    . "console.log('URI :'+\"$objectURI\");"
                                                    . "console.log('url :'+\"$url2\");"
                                                    . "searchFormData.append('concernedItems[]', \"$objectURI\");"
                                                    . "searchFormData.append('DataFileSearch[rdfType]',\"$imageTypeSelected\");"
                                                    . "searchFormData.append('provenance',\"$selectedProvenance\");"
                                                    . "searchFormData.append('jsonValueFilter', \"$filterToSend\");"
                                                    . "searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT00:00:00+0200', this.x));"
                                                    . "searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT23:59:00+0200', this.x));"
                                                    . "$.ajax({url: \"$url2\","
                                                    . "   type: 'POST',"
                                                    . "   processData: false,"
                                                    . "   datatype: 'json',"
                                                    . "   contentType: false,"
                                                    . "   data: searchFormData   "
                                                    . "}).done(function (data) { $('#visualization-images').html(data);}
                                                    ).fail(function (jqXHR, textStatus) {alert('ERROR : ' + jqXHR);});"
                                                    . "}")
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                } else {
                    echo Highcharts::widget([
                        'id' => 'graphic',
                        'options' => [
                            'title' => ['text' => $variables[$data["variable"]]],
                            'xAxis' => [
                                'type' => 'datetime',
                                'title' => 'Date',
//                        ],
//                        'chart' => [
//                            'zoomType' => 'x',
//                            'events'=> [
//                                'click' =>  new JsExpression("function(event){  alert('TEST appel ajax'+\"$url2\");}")
//                                
//                            ]
                            ],
                            'yAxis' => [
                                'title' => null,
                                'labels' => [
                                    'format' => '{value:.2f}'
                                ]
                            ],
                            'series' => $series,
                            'tooltip' => [
                                'xDateFormat' => '%Y-%m-%d %H:%M',
                            ],
                            'plotOptions' => [
                                'series' => [
                                    'cursor' => 'pointer',
                                    'point' => [
                                        'events' => [
                                            'click' => new JsExpression(" function() {"
                                                    . "}")
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                }
            }
        }
        ?>
    </div>


</div>
<script> //Highcharts stuff
    $(document).ready(function () {
<?php
if (isset($data)) {
    echo "$('#data-visualization-form').collapse('hide');";
}
?>
        $('#data-visualization-form').on('hidden.bs.collapse', function () {
            $('#graphic').show();
            $('#visualization-images').show();
        });
        $('#data-visualization-form').on('show.bs.collapse', function () {
            $('#graphic').hide();
            $('#visualization-images').hide();
        });

    });


    var checked = $('#showWidget').is(':checked');
    if (checked) {
        $('#photoFilter').show();
    } else {
        // reset values
        $('#photoFilter').hide();
    }
    function doThings(element) {
        $('#scientific-object-data-visualization-submit-button').text('UPDATE');
        var checked = $(element).is(':checked');
        if (checked) {
            $('#photoFilter').show();
        } else {
            // reset values
            $('#photoFilter').hide();
        }
    }

</script>

