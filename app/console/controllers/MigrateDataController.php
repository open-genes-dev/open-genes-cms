<?php

namespace app\console\controllers;

use app\console\service\ParseMyGeneServiceInterface;
use app\models\CommentCause;
use app\models\common\GeneToSource;
use app\models\Disease;
use app\models\GeneInterventionResultToVitalProcess;
use app\models\GeneInterventionToVitalProcess;
use app\models\Phylum;
use app\models\FunctionalCluster;
use app\models\Gene;
use app\models\common\GeneExpressionInSample;
use app\models\GeneToCommentCause;
use app\models\GeneToFunctionalCluster;
use app\models\Sample;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class MigrateDataController extends Controller
{
    public function behaviors()
    {
        return parent::behaviors();
    }

    public function actionMigrateExpression()
    {
        $samplesNames = [];
        $arGenes = Gene::find()->all();
        foreach ($arGenes as $arGene) {
            echo $arGene->symbol . ': ';
            $expression = json_decode($arGene->expressionEN, true);
            $expressionRu = json_decode($arGene->expression, true);
            if ($expression) {
                $geneSamplesNamesEn = array_keys($expression);
                $geneSamplesNamesRu = array_keys($expressionRu);
                $samplesNames = array_merge($samplesNames, array_combine($geneSamplesNamesEn, $geneSamplesNamesRu));
                foreach ($expression as $sample => $expressionValues) {
                    echo $sample . ' ';
                    $arSample = Sample::find()
                        ->andWhere(['name_en' => $sample])
                        ->one();
                    if (!$arSample) {
                        $arSample = new Sample();
                        $arSample->name_en = $sample;
                        $arSample->name_ru = $samplesNames[$sample];
                        $arSample->save();
                        $arSample->refresh();
                    }
                    $arGeneExpressionSample = GeneExpressionInSample::find()
                        ->andWhere(['gene_id' => $arGene->id])
                        ->andWhere(['sample_id' => $arSample->id])
                        ->one();
                    if (!$arGeneExpressionSample) {
                        $arGeneExpressionSample = new GeneExpressionInSample();
                        $arGeneExpressionSample->gene_id = $arGene->id;
                        $arGeneExpressionSample->sample_id = $arSample->id;
                    }
                    $arGeneExpressionSample->expression_value = $expressionValues['full_rpkm'];
                    $arGeneExpressionSample->save();
                }
            } else {
                echo 'No expression for gene ' . $arGene->id;
            }
            echo PHP_EOL;
        }
    }

    public function actionMigrateFunctionalClusters()
    {
        $arGenes = Gene::find()->all();
        foreach ($arGenes as $arGene) {
            echo $arGene->symbol . ': ';
            $functionalClustersRu = explode(',', $arGene->functionalClusters);
            if ($functionalClustersRu) {
                foreach ($functionalClustersRu as $functionalClusterRu) {
                    $functionalClusterRu = trim($functionalClusterRu);
                    $arFunctionalCluster = FunctionalCluster::find()
                        ->where(['name_ru' => $functionalClusterRu])
                        ->one();
                    if (!$arFunctionalCluster) {
                        $arFunctionalCluster = new FunctionalCluster();
                        $arFunctionalCluster->name_ru = $functionalClusterRu;
                        $arFunctionalCluster->name_en = \Yii::t(
                            'main',
                            str_replace([' ', '/'], '_', $functionalClusterRu),
                            [],
                            'en-US'
                        );
                        $arFunctionalCluster->save();
                        $arFunctionalCluster->refresh();
                    }
                    $arGeneToFunctionalCluster = GeneToFunctionalCluster::find()
                        ->andWhere(['gene_id' => $arGene->id])
                        ->andWhere(['functional_cluster_id' => $arFunctionalCluster->id])
                        ->one();
                    if (!$arGeneToFunctionalCluster) {
                        $arGeneToFunctionalCluster = new GeneToFunctionalCluster();
                        $arGeneToFunctionalCluster->gene_id = $arGene->id;
                        $arGeneToFunctionalCluster->functional_cluster_id = $arFunctionalCluster->id;
                    }
                    $arGeneToFunctionalCluster->save();
                    echo $arFunctionalCluster->name_ru . ' ';
                }
                echo PHP_EOL;
            }
        }
    }

    public function actionMigrateAge()
    {
        $arGenes = Gene::find()->all();
        foreach ($arGenes as $arGene) {
            echo $arGene->symbol . ': ';
            if ($arGene->agePhylo) {
                if ($arGene->agePhylo == 'Procaryota') {
                    $arGene->agePhylo = 'Prokaryota';
                }
                $arAge = Phylum::find()->where(
                    ['name_phylo' => $arGene->agePhylo]
                )->one();
            } elseif ($arGene->ageMya) {
                $arAge = $arAge = Phylum::find()->where(
                    ['name_mya' => $arGene->ageMya]
                );
            }
            if (isset($arAge) && $arAge instanceof Phylum) {
                $arGene->age_id = $arAge->id;
                $arGene->save();
                echo $arAge->name_phylo . PHP_EOL;
            } else {
                echo 'no age info' . PHP_EOL;
            }
        }
    }

    public function actionMigrateCommentCause()
    {
        $arGenes = Gene::find()->all();
        foreach ($arGenes as $arGene) {
            echo $arGene->symbol . ': ';
            $commentCausesRu = explode(',', $arGene->commentCause);
            if ($commentCausesRu) {
                foreach ($commentCausesRu as $commentCauseRu) {
                    $commentCauseRu = trim($commentCauseRu);
                    $arCommentCause = CommentCause::find()
                        ->where(['name_ru' => $commentCauseRu])
                        ->one();
                    if (!$arCommentCause) {
                        $arCommentCause = new CommentCause();
                        $arCommentCause->name_ru = $commentCauseRu;
                        $nameForTranslate = str_replace([' ', '/'], '_', mb_strtolower($commentCauseRu));
                        $arCommentCause->name_en = \Yii::t('main', $nameForTranslate, [], 'en-US');
                        $arCommentCause->save();
                        $arCommentCause->refresh();
                    }
                    $arGeneToCommentCause = GeneToCommentCause::find()
                        ->andWhere(['gene_id' => $arGene->id])
                        ->andWhere(['comment_cause_id' => $arCommentCause->id])
                        ->one();
                    if (!$arGeneToCommentCause) {
                        $arGeneToCommentCause = new GeneToCommentCause();
                        $arGeneToCommentCause->gene_id = $arGene->id;
                        $arGeneToCommentCause->comment_cause_id = $arCommentCause->id;
                    }
                    $arGeneToCommentCause->save();
                    echo $arCommentCause->name_ru . ' ';
                }
                echo PHP_EOL;
            }
        }
    }

    public function actionMigrateAbdb($pathToFile = 'abdb.json')
    {
        $json = file_get_contents($pathToFile);
        $array = json_decode($json, true);

        $counter = 1;
        $count = count($array['data']);
        foreach ($array['data'] as $gene) {
            try {
                echo $counter . ' from ' . $count . ': ';
                $symbol = strtoupper(trim($gene[1]));
                $arGene = Gene::find()->where(['symbol' => $symbol])->one();
                if ($arGene) {
                    echo 'gene found' . PHP_EOL;
                } else {
                    /** @var ParseMyGeneServiceInterface $myGeneService */
                    $myGeneService = \Yii::$container->get(ParseMyGeneServiceInterface::class);
                    $arGene = $myGeneService->parseBySymbol($symbol);
                    $arGene->isHidden = 1;
                    $arGene->source = 'abdb';
                    $arGene->save();
                    echo 'OK ' . $symbol . ' ' . $arGene->ncbi_id . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo 'ERROR ' . $e->getMessage() . PHP_EOL;
            }
            $counter++;
        }
    }

    public function actionSetVisibleIcdCodes($onlyNew = 'true', $icdCategoryDepth = 1)
    {
        $onlyNew = filter_var($onlyNew, FILTER_VALIDATE_BOOLEAN);
        $diseasesQuery = Disease::find()
            ->where('name_en is not null');
        if ($onlyNew) {
            $diseasesQuery->andWhere('icd_code_visible is null or icd_code_visible = ""');
        }
        $diseases = $diseasesQuery->all();

        echo PHP_EOL;
        foreach ($diseases as $disease) {
            echo $disease->icd_code;
            try {
                $visibleCategory = $disease->getIcdCategoryByLevel($icdCategoryDepth);
                if ($visibleCategory) {
                    $disease->icd_code_visible = trim($visibleCategory);
                    if ($disease->save()) {
                        echo ' -> ' . $visibleCategory;
                    } else {
                        echo ' error ' . var_export($disease->errors, true);
                    }
                } else {
                    echo ' error!';
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            echo PHP_EOL;
        }
    }

    public function actionFillSourceAbdb($absolutePathToFile)
    {
        if (!file_exists($absolutePathToFile)) {
            return 'Cannot find data file';
        }
        $json = file_get_contents($absolutePathToFile);
        $array = json_decode($json, true);

        if (empty($array['data']) || !is_array($array['data'])) {
            return 'Data file is empty';
        }

        $query = Yii::$app->db->createCommand('SELECT id, symbol FROM gene WHERE symbol IS NOT NULL ')->queryAll();
        $geneList = [];
        foreach ($query as $row) {
            if (!empty($row['symbol'])) {
                $geneList[trim($row['symbol'])] = $row['id'];
            }
        }
        try {
            foreach ($array['data'] as $gene) {
                $symbol = strtoupper(trim($gene[1]));
                if (in_array($symbol, array_keys($geneList))) {
                    $geneToSource = new GeneToSource();
                    $geneToSource->gene_id = $geneList[$symbol];
                    $geneToSource->source_id = 2;
                    $geneToSource->save();
                }
            }
            return 'Source Abdb was successfully filled';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionFillSourceGeneAge($absolutePathToFile)
    {
        if (!file_exists($absolutePathToFile)) {
            return 'Cannot find data file';
        }
        $f = fopen($absolutePathToFile, 'r');
        if (fgetcsv($f) == false) {
            return 'Data file is empty';
        }

        $query = Yii::$app->db->createCommand('SELECT id, symbol FROM gene WHERE symbol IS NOT NULL ')->queryAll();
        $geneList = [];
        foreach ($query as $row) {
            if (!empty($row['symbol'])) {
                $geneList[trim($row['symbol'])] = $row['id'];
            }
        }

        try {
            while (($data = fgetcsv($f)) !== false) {
                $symbol = strtoupper(trim($data[1]));
                if (in_array($symbol, array_keys($geneList))) {
                    $geneToSource = new GeneToSource();
                    $geneToSource->gene_id = $geneList[$symbol];
                    $geneToSource->source_id = 1;
                    $geneToSource->save();
                }
            }
            return 'Source GenAge was successfully filled';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionMergeGreenForm()
    {
        /* @var $model GeneInterventionToVitalProcess */
        /* @var $modelToUpdate GeneInterventionToVitalProcess */

        $theSameModels = $this->searchTheSameModels(GeneInterventionToVitalProcess::find()->all());

        foreach ($theSameModels as $group) {
            $modelToUpdate = array_shift($group);
            foreach ($group as $model) {
                $modelToUpdate->comment_ru = $modelToUpdate->comment_ru . ' ' . $model->comment_ru;
                $modelToUpdate->comment_en = $modelToUpdate->comment_en . ' ' . $model->comment_en;
                $relationList = GeneInterventionResultToVitalProcess::find()
                    ->where(['gene_intervention_to_vital_process_id' => $model->id])->all();
                foreach ($relationList as $relation) {
                    $this->updateRelations($modelToUpdate->id, $relation);
                }
                if ($model->delete()) {
                    Console::output('Green form id ' . $model->id . ' was deleted from table gene_intervention_to_vital_process');
                }
            }

            if ($modelToUpdate->save(false)) {
                Console::output('Green form id ' . $modelToUpdate->id . ' was updated');
            }
            else {
                Console::output('Attention! Green form id ' . $modelToUpdate->id . ' was not updated!');
            }
        }
        return Console::output('Successfully merged');
    }

    private function getModelHash($model)
    {
        return md5($model->reference .
            $model->gene_intervention_method_id .
            $model->model_organism_id .
            $model->organism_line_id .
            $model->genotype .
            $model->sex_of_organism .
            $model->gene_id);
    }

    private function searchTheSameModels ($models) {
        /* @var $model GeneInterventionToVitalProcess */
        /* @var $searchModel GeneInterventionToVitalProcess */
        $theSameModels = [];
        foreach ($models as $model) {
            foreach ($models as $searchModel) {
                if ($model->id == $searchModel->id) {
                    continue;
                }
                $modelHash = $this->getModelHash($model);
                $searchModelHash = $this->getModelHash($searchModel);
                if ($modelHash == $searchModelHash) {
                    if (!isset($theSameModels[$modelHash])) {
                        $theSameModels[$modelHash] = [$model->id => $model];
                    }
                    $theSameModels[$modelHash][$searchModel->id] = $searchModel;
                }
            }
        }
        return $theSameModels;
    }

    private function updateRelations ($modelId, $relation) {
        /* @var $relation GeneInterventionResultToVitalProcess */

        $theSameRelations = GeneInterventionResultToVitalProcess::find()
            ->where(['gene_intervention_to_vital_process_id' => $modelId,
                'intervention_result_for_vital_process_id' => $relation->intervention_result_for_vital_process_id,
                'vital_process_id' => $relation->vital_process_id
            ])->one();
        if ($theSameRelations !== null) {
            $relation->delete();
            return;
        }
        $relation->gene_intervention_to_vital_process_id = $modelId;
        if ($relation->save()) {
            Console::output('Relation id ' . $relation->id . ' was updated (table gene_intervention_result_to_vital_process)');
        }
    }
}

