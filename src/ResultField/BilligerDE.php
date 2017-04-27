<?php

namespace ElasticExportBilligerDE\ResultField;

use Plenty\Modules\DataExchange\Contracts\ResultFields;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\Search\Mutators\ImageMutator;
use Plenty\Modules\Cloud\ElasticSearch\Lib\Source\Mutator\BuiltIn\LanguageMutator;
use Plenty\Modules\Item\Search\Mutators\KeyMutator;
use Plenty\Modules\Item\Search\Mutators\DefaultCategoryMutator;

/**
 * Class BilligerDE
 * @package ElasticExportBilligerDE\ResultField
 */
class BilligerDE extends ResultFields
{
    const BILLIGER_DE = 112.00;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * BilligerDE constructor.
     * 
     * @param ArrayHelper $arrayHelper
     */
    public function __construct(ArrayHelper $arrayHelper)
    {
        $this->arrayHelper = $arrayHelper;
    }

    /**
     * Generate result fields.
     *
     * @param  array $formatSettings = []
     * @return array
     */
    public function generateResultFields(array $formatSettings = []):array
    {
        $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

        $this->setOrderByList(['variation.itemId', 'ASC']);

        $reference = $settings->get('referrerId') ? $settings->get('referrerId') : self::BILLIGER_DE;

        $itemDescriptionFields = ['texts.urlPath'];

        switch($settings->get('nameId'))
        {
            case 3:
                $itemDescriptionFields[] = 'texts.name3';
                break;
            case 2:
                $itemDescriptionFields[] = 'texts.name2';
                break;
            default:
                $itemDescriptionFields[] = 'texts.name1';
                break;
        }

        if($settings->get('descriptionType') == 'itemShortDescription'
            || $settings->get('previewTextType') == 'itemShortDescription')
        {
            $itemDescriptionFields[] = 'texts.shortDescription';
        }

        if($settings->get('descriptionType') == 'itemDescription'
            || $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
            || $settings->get('previewTextType') == 'itemDescription'
            || $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
        {
            $itemDescriptionFields[] = 'texts.description';
        }
        $itemDescriptionFields[] = 'texts.technicalData';

        //Mutator
        /**
         * @var ImageMutator $imageMutator
         */
        $imageMutator = pluginApp(ImageMutator::class);
        if($imageMutator instanceof ImageMutator)
        {
            $imageMutator->addMarket($reference);
        }

        /**
         * @var KeyMutator $keyMutator
         */
        $keyMutator = pluginApp(KeyMutator::class);
        if($keyMutator instanceof KeyMutator)
        {
            $keyMutator->setKeyList($this->getKeyList());
            $keyMutator->setNestedKeyList($this->getNestedKeyList());
        }

        /**
         * @var LanguageMutator $languageMutator
         */
        $languageMutator = pluginApp(LanguageMutator::class, [[$settings->get('lang')]]);

        /**
         * @var DefaultCategoryMutator $defaultCategoryMutator
         */
        $defaultCategoryMutator = pluginApp(DefaultCategoryMutator::class);
        if($defaultCategoryMutator instanceof DefaultCategoryMutator)
        {
            $defaultCategoryMutator->setPlentyId($settings->get('plentyId'));
        }

        // Fields
        $fields = [
            [
                //item
                'item.id',
                'item.manufacturer.id',

                //variation
                'id',
                'variation.availability.id',
                'variation.model',
                'variation.isMain',

                //images
                'images.all.urlMiddle',
                'images.all.urlPreview',
                'images.all.urlSecondPreview',
                'images.all.url',
                'images.all.path',
                'images.all.position',

                'images.item.urlMiddle',
                'images.item.urlPreview',
                'images.item.urlSecondPreview',
                'images.item.url',
                'images.item.path',
                'images.item.position',

                'images.variation.urlMiddle',
                'images.variation.urlPreview',
                'images.variation.urlSecondPreview',
                'images.variation.url',
                'images.variation.path',
                'images.variation.position',

                //unit
                'unit.content',
                'unit.id',

                //defaultCategories
                'defaultCategories.id',

                //barcodes
                'barcodes.code',
                'barcodes.type',

                //attributes
                'attributes.attributeValueSetId',
                'attributes.attributeId',
                'attributes.valueId',
                'attributes.names.name',
                'attributes.names.lang',

                //properties
                'properties.property.id',
                'properties.property.valueType',
                'properties.selection.name',
                'properties.texts.value'
            ],

            [
                $keyMutator,
                $languageMutator,
                $defaultCategoryMutator
            ],
        ];

        if($reference != -1)
        {
            $fields[1][] = $imageMutator;
        }

        foreach($itemDescriptionFields as $itemDescriptionField)
        {
            $fields[0][] = $itemDescriptionField;
        }

        return $fields;
    }

    /**
     * Returns the list of keys.
     *
     * @return array
     */
    private function getKeyList()
    {
        $keyList = [
            //item
            'item.id',
            'item.manufacturer.id',

            //variation
            'variation.availability.id',
            'variation.model',
            'variation.isMain',

            //unit
            'unit.content',
            'unit.id',
        ];

        return $keyList;
    }

    /**
     * Returns the list of nested keys.
     *
     * @return mixed
     */
    private function getNestedKeyList()
    {
        $nestedKeyList['keys'] = [
            //images
            'images.all',
            'images.item',
            'images.variation',

            //texts
            'texts',

            //defaultCategories
            'defaultCategories',

            //barcodes
            'barcodes',

            //attributes
            'attributes',

            //properties
            'properties'
        ];

        $nestedKeyList['nestedKeys'] = [
            //images
            'images.all' => [
                'urlMiddle',
                'urlPreview',
                'urlSecondPreview',
                'url',
                'path',
                'position',
            ],

            'images.item' => [
                'urlMiddle',
                'urlPreview',
                'urlSecondPreview',
                'url',
                'path',
                'position',
            ],

            'images.variation' => [
                'urlMiddle',
                'urlPreview',
                'urlSecondPreview',
                'url',
                'path',
                'position',
            ],

            //texts
            'texts' => [
                'urlPath',
                'name1',
                'name2',
                'name3',
                'shortDescription',
                'description',
                'technicalData',
            ],

            //defaultCategories
            'defaultCategories' => [
                'id'
            ],

            //barcodes
            'barcodes' => [
                'code',
                'type',
            ],

            //attributes
            'attributes' => [
                'attributeValueSetId',
                'attributeId',
                'valueId',
                'names.name',
                'names.lang',
            ],

            //proprieties
            'properties'    => [
                'property.id',
                'property.valueType',
                'selection.name',
                'texts.value'
            ]
        ];

        return $nestedKeyList;
    }
}