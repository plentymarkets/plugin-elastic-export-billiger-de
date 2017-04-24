<?php

namespace ElasticExportBilligerDE\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceSearchRepositoryContract;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchRequest;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchResponse;
use Plenty\Plugin\Log\Loggable;

/**
 * Class PriceHelper
 * @package ElasticExportBilligerDE\Helper
 */
class PriceHelper
{
    use Loggable;

    /**
     * @var SalesPriceSearchRepositoryContract
     */
    private $salesPriceSearchRepository;

    /**
     * PriceHelper constructor.
     *
     * @param SalesPriceSearchRepositoryContract $salesPriceSearchRepository
     */
    public function __construct(SalesPriceSearchRepositoryContract $salesPriceSearchRepository)
    {
        $this->salesPriceSearchRepository = $salesPriceSearchRepository;
    }

    /**
     * Get an array with the retail price.
     *
     * @param  array $variation
     * @param  KeyValue $settings
     * @return array
     */
    public function getPriceList($variation, KeyValue $settings):array
    {
        $variationPrice = $variationRrp = 0.00;

        /**
         * SalesPriceSearchRequest $salesPriceSearchRequest
         */
        $salesPriceSearchRequest = pluginApp(SalesPriceSearchRequest::class);
        if($salesPriceSearchRequest instanceof SalesPriceSearchRequest)
        {
            $salesPriceSearchRequest->variationId = $variation['id'];
            $salesPriceSearchRequest->referrerId = $settings->get('referrerId');
        }

        // getting the retail price
        $salesPriceSearch = $this->salesPriceSearchRepository->search($salesPriceSearchRequest);
        if($salesPriceSearch instanceof SalesPriceSearchResponse)
        {
            $variationPrice = (float)$salesPriceSearch->price;
        }

        return array(
            'variationRetailPrice.price'            =>  $variationPrice
        );
    }
}