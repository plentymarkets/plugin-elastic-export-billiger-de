<?php

namespace ElasticExportBilligerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use Plenty\Legacy\Repositories\Item\SalesPrice\SalesPriceSearchRepository;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchRequest;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchScrollRepositoryContract;
use Plenty\Modules\Market\Helper\Contracts\MarketPropertyHelperRepositoryContract;
use Plenty\Modules\StockManagement\Stock\Contracts\StockRepositoryContract;
use Plenty\Plugin\Log\Loggable;


/**
 * Class BilligerDE
 * @package ElasticExportBilligerDE\Generator
 */
class BilligerDE extends CSVPluginGenerator
{
    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportCoreHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var array $idlVariations
     */
    private $idlVariations = array();

    /**
     * Billiger constructor.
     * @param ElasticExportCoreHelper $elasticExportCoreHelper
     * @param ArrayHelper $arrayHelper
     */
    public function __construct(ElasticExportCoreHelper $elasticExportCoreHelper, ArrayHelper $arrayHelper)
    {
        $this->elasticExportCoreHelper = $elasticExportCoreHelper;
        $this->arrayHelper = $arrayHelper;
    }

    /**
     * @param RecordList $resultData
     * @param array $formatSettings
     */
    protected function generatePluginContent( $resultData, array $formatSettings = [], array $filter = [])
    {
        if(is_array($resultData) && count($resultData['documents']) > 0)
        {
            $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

            $this->setDelimiter(";");

            $this->addCSVContent([
                'aid',
                'name',
                'price',
                'link',
                'brand',
                'ean',
                'desc',
                'shop_cat',
                'image',
                'dlv_time',
                'dlv_cost',
                'ppu',
                'mpnr',

            ]);

            foreach($resultData['documents'] as $item)
            {
                $attributes = '';
                $attributeName = $this->elasticExportCoreHelper->getAttributeName($item, $settings, ',');
                $attributeValue = $this->elasticExportCoreHelper->getAttributeValueSetShortFrontendName($item, $settings, ',');
                if (strlen($attributeName) && strlen($attributeValue))
                {
                    $attributes = $this->elasticExportCoreHelper->getAttributeNameAndValueCombination($attributeName, $attributeValue);
                }

                if(strlen($attributes) <= 0 && $item->itemBase->variationCount > 1)
                {
                    continue;
                }

                $dlvCost = $this->elasticExportCoreHelper->getShippingCost($item, $settings);

                if(!is_null($dlvCost))
                {
                    $dlvCost = number_format((float)$dlvCost, 2, ',', '');
                }
                else
                {
                    $dlvCost = '';
                }

                $data = [
                    'aid' 		=> $item->variationBase->id,
                    'name' 		=> $this->elasticExportCoreHelper->getName($item, $settings) . (strlen($attributes) ? ', ' . $attributes : ''),
                    'price' 	=> number_format((float)$this->elasticExportCoreHelper->getPrice($item), 2, '.', ''),
                    'link' 		=> $this->elasticExportCoreHelper->getUrl($item, $settings, true, false),
                    'brand' 	=> $this->elasticExportCoreHelper->getExternalManufacturerName((int)$item->itemBase->producerId),
                    'ean' 		=> $this->elasticExportCoreHelper->getBarcodeByType($item, $settings->get('barcode')),
                    'desc' 		=> $this->elasticExportCoreHelper->getDescription($item, $settings),
                    'shop_cat' 	=> $this->elasticExportCoreHelper->getCategory((int)$item->variationStandardCategory->categoryId, $settings->get('lang'), $settings->get('plentyId')),
                    'image'		=> $this->elasticExportCoreHelper->getMainImage($item, $settings),
                    'dlv_time' 	=> $this->elasticExportCoreHelper->getAvailability($item, $settings, false),
                    'dlv_cost' 	=> $dlvCost,
                    'ppu' 		=> $this->elasticExportCoreHelper->getBasePrice($item, $settings),
                    'mpnr' 		=> $item->variationBase->model,
                ];

                $this->addCSVContent(array_values($data));
            }
        }
    }
}
