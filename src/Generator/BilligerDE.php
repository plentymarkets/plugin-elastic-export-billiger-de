<?php

namespace ElasticExportBilligerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportPriceHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
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

    const DELIMITER = "\t"; // TAB

    const BILLIGER_DE = 112.00;

    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportHelper;

    /**
     * @var ElasticExportStockHelper
     */
    private $elasticExportStockHelper;

    /**
     * @var ElasticExportPriceHelper
     */
    private $elasticExportPriceHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var PropertyHelper
     */
    private $propertyHelper;

    /**
     * @var array
     */
    private $shippingCostCache;

    /**
     * BilligerDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param PropertyHelper $propertyHelper
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        PropertyHelper $propertyHelper
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->propertyHelper = $propertyHelper;
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

        $this->elasticExportStockHelper = pluginApp(ElasticExportStockHelper::class);

        $this->elasticExportPriceHelper = pluginApp(ElasticExportPriceHelper::class);
        
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
                // Current number of lines written
                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.writtenLines', [
                    'Lines written' => $limit,
                ]);

                // Stop writing if limit is reached
                if($limitReached === true)
                {
                    break;
                }

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
                    $previousItemId = null;

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

                        // Skip non-main variations that do not have attributes
                        $attributes = $this->getAttributeNameValueCombination($variation, $settings);

                        if(strlen($attributes) <= 0 && $variation['variation']['isMain'] === false)
                        {
                            $this->getLogger(__METHOD__)->info('ElasticExportBilligerDE::log.variationNoAttributesError', [
                                'VariationId' => (string)$variation['id']
                            ]);

                            continue;
                        }

                        try
                        {
                            // Set the caches if we have the first variation or when we have the first variation of an item
                            if($previousItemId === null || $previousItemId != $variation['data']['item']['id'])
                            {
                                $previousItemId = $variation['data']['item']['id'];
                                unset($this->shippingCostCache);

                                // Build the caches arrays
                                $this->buildCaches($variation, $settings);
                            }

                            // New line printed in the CSV file
                            $this->buildRow($variation, $settings, $attributes);
                        }
                        catch(\Throwable $throwable)
                        {
                            $this->getLogger(__METHOD__)->error('ElasticExportBilligerDE::logs.fillRowError', [
                                'Error message ' => $throwable->getMessage(),
                                'Error line'     => $throwable->getLine(),
                                'VariationId'    => $variation['id']
                            ]);
                        }

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
            // mandatory
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
            'pzn',

            // optional
            'promo_text',
            'voucher_text',
            'eec',
            'light_socket',
            'wet_grip',
            'fuel',
            'rolling_noise',
            'hsn_tsn',
            'dia',
            'bc',
            'sph_pwr',
            'cyl',
            'axis',
            'size',
            'color',
            'gender',
            'material',
            'class',
            'features',
            'style',
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
        // Get the price list
        $priceList = $this->elasticExportPriceHelper->getPriceList($variation, $settings);

        // Only variations with the Retail Price greater than zero will be handled
        if(!is_null($priceList['price']) && $priceList['price'] > 0)
        {
            // Price for base price calculation
            $variationRetailPrice['variationRetailPrice.price'] = $priceList['price'];

            $data = [
                // mandatory
                'aid'           => $this->elasticExportHelper->generateSku($variation['id'], self::BILLIGER_DE, 0, (string)$variation['data']['skus'][0]['sku']),
                'brand'         => $this->elasticExportHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id']),
                'mpnr'          => $variation['data']['variation']['model'],
                'ean'           => $this->elasticExportHelper->getBarcodeByType($variation, $settings->get('barcode')),
                'name'          => $this->elasticExportHelper->getMutatedName($variation, $settings) . (strlen($attributes) ? ', ' . $attributes : ''),
                'desc'          => $this->elasticExportHelper->getMutatedDescription($variation, $settings),
                'shop_cat'      => $this->elasticExportHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
                'price'         => $priceList['price'],
                'ppu'           => $this->elasticExportHelper->getBasePrice($variation, $variationRetailPrice, $settings->get('lang')),
                'link'          => $this->elasticExportHelper->getMutatedUrl($variation, $settings, true, false),
                'image'         => $this->elasticExportHelper->getMainImage($variation, $settings),
                'dlv_time'      => $this->elasticExportHelper->getAvailability($variation, $settings, false),
                'dlv_cost'      => $this->getShippingCost($variation),
                'pzn'           => $this->propertyHelper->getProperty($variation, $settings, 'pzn'),

                // optional
                'promo_text'    => $this->propertyHelper->getProperty($variation, $settings, 'promo_text'),
                'voucher_text'  => $this->propertyHelper->getProperty($variation, $settings, 'voucher_text'),
                'eec'           => $this->propertyHelper->getProperty($variation, $settings, 'eec'),
                'light_socket'  => $this->propertyHelper->getProperty($variation, $settings, 'light_socket'),
                'wet_grip'      => $this->propertyHelper->getProperty($variation, $settings, 'wet_grip'),
                'fuel'          => $this->propertyHelper->getProperty($variation, $settings, 'fuel'),
                'rolling_noise' => $this->propertyHelper->getProperty($variation, $settings, 'rolling_noise'),
                'hsn_tsn'       => $this->propertyHelper->getProperty($variation, $settings, 'hsn_tsn'),
                'dia'           => $this->propertyHelper->getProperty($variation, $settings, 'dia'),
                'bc'            => $this->propertyHelper->getProperty($variation, $settings, 'bc'),
                'sph_pwr'       => $this->propertyHelper->getProperty($variation, $settings, 'sph_pwr'),
                'cyl'           => $this->propertyHelper->getProperty($variation, $settings, 'cyl'),
                'axis'          => $this->propertyHelper->getProperty($variation, $settings, 'axis'),
                'size'          => $this->propertyHelper->getProperty($variation, $settings, 'size'),
                'color'         => $this->propertyHelper->getProperty($variation, $settings, 'color'),
                'gender'        => $this->propertyHelper->getProperty($variation, $settings, 'gender'),
                'material'      => $this->propertyHelper->getProperty($variation, $settings, 'material'),
                'class'         => $this->propertyHelper->getProperty($variation, $settings, 'class'),
                'features'      => $this->propertyHelper->getProperty($variation, $settings, 'features'),
                'style'         => $this->propertyHelper->getProperty($variation, $settings, 'style'),
            ];

            $this->addCSVContent(array_values($data));
        }
        else
        {
            $this->getLogger(__METHOD__)->info('ElasticExportBilligerDE::log.variationNotPartOfExportPrice', [
                'VariationId' => (string)$variation['id']
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
     * Get the shipping cost.
     *
     * @param $variation
     * @return string
     */
    private function getShippingCost($variation):string
    {
        $shippingCost = null;
        if(isset($this->shippingCostCache) && array_key_exists($variation['data']['item']['id'], $this->shippingCostCache))
        {
            $shippingCost = $this->shippingCostCache[$variation['data']['item']['id']];
        }

        if(!is_null($shippingCost) && $shippingCost != '0.00')
        {
            return $shippingCost;
        }

        return '';
    }

    /**
     * Build the cache arrays for the item variation.
     *
     * @param $variation
     * @param $settings
     */
    private function buildCaches($variation, $settings)
    {
        if(!is_null($variation) && !is_null($variation['data']['item']['id']))
        {
            $shippingCost = $this->elasticExportHelper->getShippingCost($variation['data']['item']['id'], $settings, 0);
            $this->shippingCostCache[$variation['data']['item']['id']] = number_format((float)$shippingCost, 2, '.', '');
        }
    }
}
