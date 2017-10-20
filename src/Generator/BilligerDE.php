<?php

namespace ElasticExportBilligerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportPriceHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
use ElasticExport\Helper\ElasticExportPropertyHelper;
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
     * @var ElasticExportPropertyHelper
     */
    private $elasticExportPropertyHelper;

    /**
     * @var array
     */
    private $shippingCostCache;

    /**
     * @var array
     */
    private $imageCache;

    /**
     * BilligerDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     */
    public function __construct(
        ArrayHelper $arrayHelper
    )
    {
        $this->arrayHelper = $arrayHelper;
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

        $this->elasticExportPropertyHelper = pluginApp(ElasticExportPropertyHelper::class);
        
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
			$shardIterator = 0;

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

				$shardIterator++;

				//log the amount of the elasticsearch result once
				if($shardIterator == 1)
				{
					$this->getLogger(__METHOD__)->addReference('total', (int)$resultList['total'])->info('ElasticExportBilligerDE::log.esResultAmount');
				}

                $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::log.esDuration', [
                    'Elastic Search duration' => microtime(true) - $esStartTime,
                ]);

                if(count($resultList['error']) > 0)
                {
                    $this->getLogger(__METHOD__)->addReference('failedShard', $shardIterator)->error('ElasticExportBilligerDE::log.occurredElasticSearchErrors', [
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
            'old_price',
            'images',
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
        // Get and set the price and rrp
        $priceList = $this->getPriceList($variation, $settings);

        $imageList = $this->getImageList($variation, $settings);

        // Only variations with the Retail Price greater than zero will be handled
        if(!is_null($priceList['price']) && (float)$priceList['price'] > 0)
        {
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
                'ppu'           => $this->elasticExportPriceHelper->getBasePrice($variation, (float)$priceList['price'], $settings->get('lang')),
                'link'          => $this->elasticExportHelper->getMutatedUrl($variation, $settings, true, false),
                'image'         => isset($imageList[0]) ? $imageList[0] : '',
                'dlv_time'      => $this->elasticExportHelper->getAvailability($variation, $settings, false),
                'dlv_cost'      => $this->getShippingCost($variation),
                'pzn'           => $this->elasticExportPropertyHelper->getProperty($variation, 'pzn', self::BILLIGER_DE, $settings->get('lang')),

                // optional
                'promo_text'    => $this->elasticExportPropertyHelper->getProperty($variation, 'promo_text', self::BILLIGER_DE, $settings->get('lang')),
                'voucher_text'  => $this->elasticExportPropertyHelper->getProperty($variation, 'voucher_text', self::BILLIGER_DE, $settings->get('lang')),
                'eec'           => $this->elasticExportPropertyHelper->getProperty($variation, 'eec', self::BILLIGER_DE, $settings->get('lang')),
                'light_socket'  => $this->elasticExportPropertyHelper->getProperty($variation, 'light_socket', self::BILLIGER_DE, $settings->get('lang')),
                'wet_grip'      => $this->elasticExportPropertyHelper->getProperty($variation, 'wet_grip', self::BILLIGER_DE, $settings->get('lang')),
                'fuel'          => $this->elasticExportPropertyHelper->getProperty($variation, 'fuel', self::BILLIGER_DE, $settings->get('lang')),
                'rolling_noise' => $this->elasticExportPropertyHelper->getProperty($variation, 'rolling_noise', self::BILLIGER_DE, $settings->get('lang')),
                'hsn_tsn'       => $this->elasticExportPropertyHelper->getProperty($variation, 'hsn_tsn', self::BILLIGER_DE, $settings->get('lang')),
                'dia'           => $this->elasticExportPropertyHelper->getProperty($variation, 'dia', self::BILLIGER_DE, $settings->get('lang')),
                'bc'            => $this->elasticExportPropertyHelper->getProperty($variation, 'bc', self::BILLIGER_DE, $settings->get('lang')),
                'sph_pwr'       => $this->elasticExportPropertyHelper->getProperty($variation, 'sph_pwr', self::BILLIGER_DE, $settings->get('lang')),
                'cyl'           => $this->elasticExportPropertyHelper->getProperty($variation, 'cyl', self::BILLIGER_DE, $settings->get('lang')),
                'axis'          => $this->elasticExportPropertyHelper->getProperty($variation, 'axis', self::BILLIGER_DE, $settings->get('lang')),
                'size'          => $this->elasticExportPropertyHelper->getProperty($variation, 'size', self::BILLIGER_DE, $settings->get('lang')),
                'color'         => $this->elasticExportPropertyHelper->getProperty($variation, 'color', self::BILLIGER_DE, $settings->get('lang')),
                'gender'        => $this->elasticExportPropertyHelper->getProperty($variation, 'gender', self::BILLIGER_DE, $settings->get('lang')),
                'material'      => $this->elasticExportPropertyHelper->getProperty($variation, 'material', self::BILLIGER_DE, $settings->get('lang')),
                'class'         => $this->elasticExportPropertyHelper->getProperty($variation, 'class', self::BILLIGER_DE, $settings->get('lang')),
                'features'      => $this->elasticExportPropertyHelper->getProperty($variation, 'features', self::BILLIGER_DE, $settings->get('lang')),
                'style'         => $this->elasticExportPropertyHelper->getProperty($variation, 'style', self::BILLIGER_DE, $settings->get('lang')),
                'old_price'     => $priceList['oldPrice'],
                'images'        => $this->getAdditionalImages($imageList),
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
     * @param  array    $variation
     * @param  KeyValue $settings
     * @return array
     */
    private function getPriceList(array $variation, KeyValue $settings):array
    {
        $price = $oldPrice = '';

        $priceList = $this->elasticExportPriceHelper->getPriceList($variation, $settings);

        //determinate which price to use as 'price'
        //only use specialPrice if it is set and the lowest price available
        if(    $priceList['specialPrice'] > 0.00
            && $priceList['specialPrice'] < $priceList['price'])
        {
            $price = $priceList['specialPrice'];
        }
        elseif($priceList['price'] > 0.00)
        {
            $price = $priceList['price'];
        }

        //determinate which price to use as 'old_price'
        //only use oldPrice if it is higher than the normal price
        if(    $priceList['recommendedRetailPrice'] > 0.00
            && $priceList['recommendedRetailPrice'] > $price
            && $priceList['recommendedRetailPrice'] > $priceList['price'])
        {
            $oldPrice = $priceList['recommendedRetailPrice'];
        }
        elseif($priceList['price'] > 0.00
            && $priceList['price'] < $price)
        {
            $oldPrice = $priceList['price'];
        }

        return [
            'price'     => $price,
            'oldPrice'  => $oldPrice,
        ];
    }

    /**
     * @param  array    $variation
     * @param  KeyValue $settings
     * @return array
     */
    private function getImageList(array $variation, KeyValue $settings):array
    {
        if(isset($this->imageCache[$variation['data']['item']['id']]))
        {
            return $this->imageCache[$variation['data']['item']['id']];
        }
        else
        {
            $this->imageCache = [];
            $this->imageCache[$variation['data']['item']['id']] = $this->elasticExportHelper->getImageListInOrder($variation, $settings);
            return $this->imageCache[$variation['data']['item']['id']];
        }
    }

    /**
     * Returns a string of all additional picture-URLs separated by ","
     *
     * @param array $imageList
     * @return string
     */
    private function getAdditionalImages(array $imageList):string
    {
        $imageListString = '';

        unset($imageList[0]);

        if(count($imageList))
        {
            $imageListString = implode(',', $imageList);
        }

        return $imageListString;
    }

    /**
     * Get the shipping cost.
     *
     * @param $variation
     * @return string
     */
    private function getShippingCost($variation):string
    {
        $shippingCost = '';

        if(isset($this->shippingCostCache) && array_key_exists($variation['data']['item']['id'], $this->shippingCostCache))
        {
            $shippingCost = $this->shippingCostCache[$variation['data']['item']['id']];
        }

        return $shippingCost;
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

            if(!is_null($shippingCost))
            {
                $this->shippingCostCache[$variation['data']['item']['id']] = number_format((float)$shippingCost, 2, '.', '');
            }
            else
            {
                $this->shippingCostCache[$variation['data']['item']['id']] = '';
            }
        }
    }
}
