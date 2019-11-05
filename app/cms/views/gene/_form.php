<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gene */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gene-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'isHidden')->checkbox() ?>

    <div class="form-split">
        <div class="form-half">
            <div class="form-split">
                <div class="form-half">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'agePhylo')->dropDownList([
                        '' => '',
                        'Chordata' => 'Chordata',
                        'Eukaryota' => 'Eukaryota',
                        'Eumetazoa' => 'Eumetazoa',
                        'Euteleostomi' => 'Euteleostomi',
                        'Mammalia' => 'Mammalia',
                        'Opisthokonta' => 'Opisthokonta',
                        'Osteichthyes' => 'Osteichthyes',
                        'Prokaryota' => 'Prokaryota',
                        'Vertebrata' => 'Vertebrata',
                    ]) ?>

                    <?= $form->field($model, 'orientation')->dropDownList([-1 => -1, 0 => 0, 1 => 1]) ?>

                    <?= $form->field($model, 'entrezGene')->textInput() ?>

                    <?= $form->field($model, 'uniprot')->textInput(['maxlength' => true]) ?>

                </div>
                <div class="form-half">

                    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'ageMya')->textInput() ?>

                    <?= $form->field($model, 'expressionChange')->dropDownList([
                        '' => '',
                        'уменьшается' => 'уменьшается',
                        'увеличивается' => 'увеличивается',
                        'неоднозначно' => 'неоднозначно',
                    ]) ?>

                    <?= $form->field($model, 'accPromoter')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'band')->textInput(['maxlength' => true]) ?>

                </div>
            </div>



            <?= $form->field($model, 'why')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="form-half">

            <?= $form->field($model, 'aliases')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'functionalClusters')->textInput(['maxlength' => true]) ?>

            <div class="form-split">
                <div class="form-half">

                    <?= $form->field($model, 'accOrf')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'locationStart')->textInput() ?>

                </div>
                <div class="form-half">

                    <?= $form->field($model, 'accCds')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'locationEnd')->textInput() ?>

                </div>
            </div>

            <?= $form->field($model, 'references')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'orthologs')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'commentCause')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentAging')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentAgingEN')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentEvolution')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentEvolutionEN')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentFunction')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentFunctionEN')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'commentsReferenceLinks')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- todo create styles for cms -->
<style>
    .form-half {
        width: 45%;
        float: left;
        margin-right: 5%;
    }
    .form-split:after {
        content: "";
        display: table;
        clear: both;
    }
</style>