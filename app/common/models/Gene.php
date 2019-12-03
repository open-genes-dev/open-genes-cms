<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "gene".
 *
 * @property int $id
 * @property string $agePhylo
 * @property int $ageMya
 * @property string $symbol
 * @property string $aliases
 * @property string $name
 * @property int $entrezGene
 * @property string $uniprot
 * @property string $why
 * @property string $band
 * @property int $locationStart
 * @property int $locationEnd
 * @property int $orientation
 * @property string $accPromoter
 * @property string $accOrf
 * @property string $accCds
 * @property string $references
 * @property string $orthologs
 * @property string $commentEvolution
 * @property string $commentFunction
 * @property string $commentCause
 * @property string $commentAging
 * @property string $commentEvolutionEN
 * @property string $commentFunctionEN
 * @property string $commentAgingEN
 * @property string $commentsReferenceLinks
 * @property int $rating
 * @property string $functionalClusters
 * @property int $dateAdded
 * @property string $userEdited
 * @property int $isHidden
 * @property string $expression
 * @property string $expressionEN
 * @property string $expressionChange
 * @property int $created_at
 * @property int $updated_at
 * @property int $age_id
 * @property string $product_ru
 * @property string $product_en
 * @property int $protein_class_id
 *
 * @property Age $age
 * @property ProteinClass $proteinClass
 * @property GeneExpressionInSample[] $geneExpressionInSamples
 * @property GeneToCommentCause[] $geneToCommentCauses
 * @property GeneToProteinActivity[] $geneToProteinActivities
 */
class Gene extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gene';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ageMya', 'entrezGene', 'locationStart', 'locationEnd', 'orientation', 'rating', 'dateAdded', 'isHidden', 'created_at', 'updated_at', 'age_id', 'protein_class_id'], 'integer'],
            [['dateAdded'], 'required'],
            [['expression', 'expressionEN'], 'string'],
            [['agePhylo', 'symbol', 'aliases', 'name', 'uniprot', 'band', 'accPromoter', 'accOrf', 'accCds'], 'string', 'max' => 120],
            [['why', 'references', 'orthologs', 'functionalClusters'], 'string', 'max' => 1000],
            [['commentEvolution', 'commentFunction', 'commentCause', 'commentAging', 'commentEvolutionEN', 'commentFunctionEN', 'commentAgingEN'], 'string', 'max' => 1500],
            [['commentsReferenceLinks'], 'string', 'max' => 2000],
            [['userEdited'], 'string', 'max' => 50],
            [['expressionChange'], 'string', 'max' => 64],
            [['product_ru', 'product_en'], 'string', 'max' => 255],
            [['age_id'], 'exist', 'skipOnError' => true, 'targetClass' => Age::className(), 'targetAttribute' => ['age_id' => 'id']],
            [['protein_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProteinClass::className(), 'targetAttribute' => ['protein_class_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agePhylo' => 'Age Phylo',
            'ageMya' => 'Age Mya',
            'symbol' => 'Symbol',
            'aliases' => 'Aliases',
            'name' => 'Name',
            'entrezGene' => 'Entrez Gene',
            'uniprot' => 'Uniprot',
            'why' => 'Why',
            'band' => 'Band',
            'locationStart' => 'Location Start',
            'locationEnd' => 'Location End',
            'orientation' => 'Orientation',
            'accPromoter' => 'Acc Promoter',
            'accOrf' => 'Acc Orf',
            'accCds' => 'Acc Cds',
            'references' => 'References',
            'orthologs' => 'Orthologs',
            'commentEvolution' => 'Comment Evolution',
            'commentFunction' => 'Comment Function',
            'commentCause' => 'Comment Cause',
            'commentAging' => 'Comment Aging',
            'commentEvolutionEN' => 'Comment Evolution En',
            'commentFunctionEN' => 'Comment Function En',
            'commentAgingEN' => 'Comment Aging En',
            'commentsReferenceLinks' => 'Comments Reference Links',
            'rating' => 'Rating',
            'functionalClusters' => 'Functional Clusters',
            'dateAdded' => 'Date Added',
            'userEdited' => 'User Edited',
            'isHidden' => 'Is Hidden',
            'expression' => 'Expression',
            'expressionEN' => 'Expression En',
            'expressionChange' => 'Expression Change',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'age_id' => 'Age ID',
            'product_ru' => 'Product Ru',
            'product_en' => 'Product En',
            'protein_class_id' => 'Protein Class ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAge()
    {
        return $this->hasOne(Age::className(), ['id' => 'age_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProteinClass()
    {
        return $this->hasOne(ProteinClass::className(), ['id' => 'protein_class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeneExpressionInSamples()
    {
        return $this->hasMany(GeneExpressionInSample::className(), ['gene_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeneToCommentCauses()
    {
        return $this->hasMany(GeneToCommentCause::className(), ['gene_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeneToProteinActivities()
    {
        return $this->hasMany(GeneToProteinActivity::className(), ['gene_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return GeneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GeneQuery(get_called_class());
    }
}
