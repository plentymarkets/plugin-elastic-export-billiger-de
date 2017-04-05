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
    use Loggable;

    const TRANSFER_RRP_YES = 1;
    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportCoreHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var SalesPriceSearchRepository
     */
    private $salesPriceSearchRepository;

    /**
     * MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
     */
    private $marketPropertyHelperRepository;

    /**
     * BilligerDE constructor.
     * @param ArrayHelper $arrayHelper
     * @param MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->marketPropertyHelperRepository = $marketPropertyHelperRepository;
    }

    /**
     * @param VariationElasticSearchScrollRepositoryContract $elasticSearch
     * @param array $formatSettings
     * @param array $filter
     */
    protected function generatePluginContent($elasticSearch, array $formatSettings = [], array $filter = [])
    {
        $this->elasticExportCoreHelper = pluginApp(ElasticExportCoreHelper::class);
        
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
            'mpnr'
        ]);

        $lines = 0;
        $limitReached = false;

        $startTime = microtime(true);
        
        if($elasticSearch instanceof VariationElasticSearchScrollRepositoryContract)
        {
            do 
            {
                if ($limitReached === true) {
                    break;
                }

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.writtenLines', [
                    'lines written' => $lines,
                ]);

                $esStartTime = microtime(true);

                $resultList = $elasticSearch->execute();

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.esDuration', [
                    'Elastic Search duration' => microtime(true) - $esStartTime,
                ]);

                if (count($resultList['error']) > 0) {
                    $this->getLogger(__METHOD__)->error('ElasticExportBilligerDE::log.occurredElasticSearchErrors', [
                        'error message' => $resultList['error'],
                    ]);
                }

                $buildRowStartTime = microtime(true);
                
                foreach ($elasticSearch['documents'] as $item) 
                {
                    $attributes = '';
                    $attributeName = $this->elasticExportCoreHelper->getAttributeName($item, $settings, ',');
                    $attributeValue = $this->elasticExportCoreHelper->getAttributeValueSetShortFrontendName($item, $settings, ',');
                    if (strlen($attributeName) && strlen($attributeValue)) 
                    {
                        $attributes = $this->elasticExportCoreHelper->getAttributeNameAndValueCombination($attributeName, $attributeValue);
                    }

                    if (strlen($attributes) <= 0 && $item->itemBase->variationCount > 1) 
                    {
                        continue;
                    }

                    $dlvCost = $this->elasticExportCoreHelper->getShippingCost($item, $settings);

                    if (!is_null($dlvCost)) 
                    {
                        $dlvCost = number_format((float)$dlvCost, 2, ',', '');
                    } else 
                    {
                        $dlvCost = '';
                    }

                    $data = [
                        'aid' => $item->variationBase->id,
                        'name' => $this->elasticExportCoreHelper->getName($item, $settings) . (strlen($attributes) ? ', ' . $attributes : ''),
                        'price' => number_format((float)$this->elasticExportCoreHelper->getPrice($item), 2, '.', ''),
                        'link' => $this->elasticExportCoreHelper->getUrl($item, $settings, true, false),
                        'brand' => $this->elasticExportCoreHelper->getExternalManufacturerName((int)$item->itemBase->producerId),
                        'ean' => $this->elasticExportCoreHelper->getBarcodeByType($item, $settings->get('barcode')),
                        'desc' => $this->elasticExportCoreHelper->getDescription($item, $settings),
                        'shop_cat' => $this->elasticExportCoreHelper->getCategory((int)$item->variationStandardCategory->categoryId, $settings->get('lang'), $settings->get('plentyId')),
                        'image' => $this->elasticExportCoreHelper->getMainImage($item, $settings),
                        'dlv_time' => $this->elasticExportCoreHelper->getAvailability($item, $settings, false),
                        'dlv_cost' => $dlvCost,
                        'ppu' => $this->elasticExportCoreHelper->getBasePrice($item, $settings),
                        'mpnr' => $item->variationBase->model,
                    ];

                    $lines = $lines + 1;
                    
                    if($lines == $filter['limit'])
                    {
                        $limitReached = true;
                        break;
                    }

                    $this->addCSVContent(array_values($data));

                    $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.buildRowDuration', [
                        'Build Row duration' => microtime(true) - $buildRowStartTime,
                    ]);
                }
                
            } while ($elasticSearch->hasNext());
        }

        $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.fileGenerationDuration', [
            'Whole file generation duration' => microtime(true) - $startTime,
        ]);
    }

    /**
     * Get a List of price, reduced price and the reference for the reduced price.
     * @param array $item
     * @param KeyValue $settings
     * @return array
     */
    private function getPriceList($item, KeyValue $settings):array
    {
        $variationPrice = 0.00;


        //getting the retail price
        /**
         * SalesPriceSearchRequest $salesPriceSearchRequest
         */
        $salesPriceSearchRequest = pluginApp(SalesPriceSearchRequest::class);
        if($salesPriceSearchRequest instanceof SalesPriceSearchRequest)
        {
            $salesPriceSearchRequest->variationId = $item['id'];
            $salesPriceSearchRequest->referrerId = $settings->get('referrerId');
        }

        $salesPriceSearch  = $this->salesPriceSearchRepository->search($salesPriceSearchRequest);
        $variationPrice = $salesPriceSearch->price;

        //getting the recommended retail price
        if($settings->get('transferRrp') == self::TRANSFER_RRP_YES)
        {
            $salesPriceSearchRequest->type = 'rrp';
            $variationRrp = $this->salesPriceSearchRepository->search($salesPriceSearchRequest)->price;
        }
        else
        {
            $variationRrp = 0.00;
        }

        //setting retail price as selling price without a reduced price
        $price = $variationPrice;
        $rrp = '';

        if ($price != '' || $price != 0.00)
        {
            //if recommended retail price is set and higher than retail price...
            if ($variationRrp > 0 && $variationPrice > $variationRrp)
            {
                //set recommended retail price as selling price
                $price = $variationRrp;
                //set retail price as reduced price
                $rrp = $variationPrice;

            }
        }
        
        return array(
            'price'                     =>  $price,
            'reducedPrice'              =>  $rrp
        );
    }
}
