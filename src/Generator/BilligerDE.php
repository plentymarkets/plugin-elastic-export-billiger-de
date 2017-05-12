<?php

namespace ElasticExportBilligerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
use ElasticExportBilligerDE\Helper\PriceHelper;
use ElasticExportBilligerDE\Helper\PropertyHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchScrollRepositoryContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class BilligerDE
 * @package ElasticExportBilligerDE\Generator
 */
class BilligerDE extends CSVPluginGenerator
{
    use Loggable;

    const DELIMITER = "\t"; // tab
    const PROPERTY_TYPE_PZN = 'pzn';

    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var PropertyHelper
     */
    private $propertyHelper;

    /**
     * @var ElasticExportStockHelper $elasticExportStockHelper
     */
    private $elasticExportStockHelper;

    /**
     * BilligerDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param PriceHelper $priceHelper
     * @param PropertyHelper $propertyHelper
     * @param ElasticExportStockHelper $elasticExportStockHelper
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        PriceHelper $priceHelper,
        PropertyHelper $propertyHelper,
        ElasticExportStockHelper $elasticExportStockHelper
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->priceHelper = $priceHelper;
        $this->propertyHelper = $propertyHelper;
        $this->elasticExportStockHelper = $elasticExportStockHelper;
    }

    /**
     * Generates and populates the data into the CSV file.
     *
     * @param VariationElasticSearchScrollRepositoryContract $elasticSearch
     * @param array $formatSettings
     * @param array $filter
     */
    protected function generatePluginContent($elasticSearch, array $formatSettings = [], array $filter = [])
    {
        $this->elasticExportHelper = pluginApp(ElasticExportCoreHelper::class);
        
        $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

        // Delimiter accepted are TAB or PIPE
        $this->setDelimiter(self::DELIMITER);

        // Add the header of the CSV file
        $this->addCSVContent($this->head());

        $startTime = microtime(true);
        
        if($elasticSearch instanceof VariationElasticSearchScrollRepositoryContract)
        {
            // Initiate the counter for the variations limit
            $limitReached = false;
            $limit = 0;

            do 
            {
                // Stop writing if limit is reached
                if($limitReached === true)
                {
                    break;
                }

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.writtenLines', [
                    'Lines written' => $limit,
                ]);

                $esStartTime = microtime(true);

                // Get the data from Elastic Search
                $resultList = $elasticSearch->execute();

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.esDuration', [
                    'Elastic Search duration' => microtime(true) - $esStartTime,
                ]);

                if(count($resultList['error']) > 0)
                {
                    $this->getLogger(__METHOD__)->error('ElasticExportBilligerDE::log.occurredElasticSearchErrors', [
                        'Error message' => $resultList['error'],
                    ]);
                }

                $buildRowsStartTime = microtime(true);

                if(is_array($resultList['documents']) && count($resultList['documents']) > 0)
                {
                    foreach ($resultList['documents'] as $variation)
                    {
                        // Stop and set the flag if limit is reached
                        if($limit == $filter['limit'])
                        {
                            $limitReached = true;
                            break;
                        }

                        // If filtered by stock is set and stock is negative, then skip the variation
                        if($this->elasticExportStockHelper->isFilteredByStock($variation, $filter) === true)
                        {
                            $this->getLogger(__METHOD__)->info('ElasticExportBilligerDE::log.variationNotPartOfExportStock', [
                                'VariationId' => (string)$variation['id']
                            ]);

                            continue;
                        }

                        // Skip the variations that do not have attributes, print just the main variation in that case
                        $attributes = $this->getAttributeNameValueCombination($variation, $settings);

                        if(strlen($attributes) <= 0 && $variation['variation']['isMain'] === false)
                        {
                            $this->getLogger(__METHOD__)->info('ElasticExportBilligerDE::log.variationNoAttributesError', [
                                'VariationId' => (string)$variation['id']
                            ]);

                            continue;
                        }

                        // New line printed in the CSV file
                        $this->buildRow($variation, $settings, $attributes);

                        // Count the new printed line
                        $limit++;
                    }

                    $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.buildRowsDuration', [
                        'Build rows duration' => microtime(true) - $buildRowsStartTime,
                    ]);
                }
                
            } while ($elasticSearch->hasNext());
        }

        $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.fileGenerationDuration', [
            'Whole file generation duration' => microtime(true) - $startTime,
        ]);
    }

    /**
     * Creates the header of the CSV file.
     *
     * @return array
     */
    private function head():array
    {
        return array(
            'aid',
            'brand',
            'mpnr',
            'ean',
            'name',
            'desc',
            'shop_cat',
            'price',
            'ppu',
            'link',
            'image',
            'dlv_time',
            'dlv_cost',
            'pzn'
        );
    }

    /**
     * Creates the variation row and prints it into the CSV file.
     *
     * @param array $variation
     * @param KeyValue $settings
     */
    private function buildRow($variation, KeyValue $settings, $attributes)
    {
        $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.variationConstructRow', [
            'Data row duration' => 'Row printing start'
        ]);

        $rowTime = microtime(true);

        try
        {
            // Get the price list
            $priceList = $this->priceHelper->getPriceList($variation, $settings);

            // Only variations with the Retail Price greater than zero will be handled
            if($priceList['variationRetailPrice.price'] > 0)
            {
                // Get delivery costs
                $dlvCost = $this->getDeliveryCosts($variation, $settings);

                $data = [
                    'aid'       => $variation['id'],
                    'brand'     => $this->elasticExportHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id']),
                    'mpnr'      => $variation['data']['variation']['model'],
                    'ean'       => $this->elasticExportHelper->getBarcodeByType($variation, $settings->get('barcode')),
                    'name'      => $this->elasticExportHelper->getMutatedName($variation, $settings) . (strlen($attributes) ? ', ' . $attributes : ''),
                    'desc'      => $this->elasticExportHelper->getMutatedDescription($variation, $settings),
                    'shop_cat'  => $this->elasticExportHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
                    'price'     => number_format((float)$priceList['variationRetailPrice.price'], 2, '.', ''),
                    'ppu'       => $this->elasticExportHelper->getBasePrice($variation, $priceList, $settings->get('lang')),
                    'link'      => $this->elasticExportHelper->getMutatedUrl($variation, $settings, true, false),
                    'image'     => $this->elasticExportHelper->getMainImage($variation, $settings),
                    'dlv_time'  => $this->elasticExportHelper->getAvailability($variation, $settings, false),
                    'dlv_cost'  => $dlvCost,
                    'pzn'       => $this->propertyHelper->getProperty($variation, $settings, self::PROPERTY_TYPE_PZN),
                ];

                $this->addCSVContent(array_values($data));

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.variationConstructRowFinished', [
                    'Data row duration' => 'Row printing took: ' . (microtime(true) - $rowTime),
                ]);
            }
            else
            {
                $this->getLogger(__METHOD__)->info('ElasticExportBilligerDE::log.variationNotPartOfExportPrice', [
                    'VariationId' => (string)$variation['id']
                ]);
            }
        }
        catch (\Throwable $throwable)
        {
            $this->getLogger(__METHOD__)->error('ElasticExportBilligerDE::log.fillRowError', [
                'Error message' => $throwable->getMessage(),
                'Error line'    => $throwable->getLine(),
                'VariationId'   => $variation['id']
            ]);
        }
    }

    /**
     * Get attribute and name value combination for a variation.
     *
     * @param $variation
     * @param KeyValue $settings
     * @return string
     */
    private function getAttributeNameValueCombination($variation, KeyValue $settings):string
    {
        $attributes = '';

        $attributeName = $this->elasticExportHelper->getAttributeName($variation, $settings, ',');
        $attributeValue = $this->elasticExportHelper->getAttributeValueSetShortFrontendName($variation, $settings, ',');

        if(strlen($attributeName) && strlen($attributeValue))
        {
            $attributes = $this->elasticExportHelper->getAttributeNameAndValueCombination($attributeName, $attributeValue);
        }

        return $attributes;
    }

    /**
     * Get the delivery costs for a variation.
     *
     * @param $variation
     * @param KeyValue $settings
     * @return string
     */
    private function getDeliveryCosts($variation, KeyValue $settings):string
    {
        $dlvCost = $this->elasticExportHelper->getShippingCost($variation['data']['item']['id'], $settings);

        if(!is_null($dlvCost))
        {
            return number_format((float)$dlvCost, 2, ',', '');
        }

        return '';
    }
}
