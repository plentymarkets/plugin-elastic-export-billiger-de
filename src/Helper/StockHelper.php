<?php

namespace ElasticExportBilligerDE\Helper;

use Illuminate\Support\Collection;
use Plenty\Modules\StockManagement\Stock\Contracts\StockRepositoryContract;
use Plenty\Modules\StockManagement\Stock\Models\Stock;
use Plenty\Plugin\Log\Loggable;
use Plenty\Repositories\Models\PaginatedResult;

/**
 * Class StockHelper
 * @package ElasticExportBilligerDE\Helper
 */
class StockHelper
{
    use Loggable;

    const STOCK_WAREHOUSE_TYPE = 'sales';

    /**
     * Checks if variation is filtered by stock.
     *
     * @param array $variation
     * @param array $filter
     * @return bool
     */
    public function isFilteredByStock($variation, $filter)
    {
        /**
         * If the stock filter is set, this will sort out all variations
         * not matching the filter.
         */
        if(array_key_exists('variationStock.netPositive', $filter))
        {
            return $this->isStockNegative($variation);
        }
        elseif(array_key_exists('variationStock.isSalable', $filter))
        {
            if(count($filter['variationStock.isSalable']['stockLimitation']) == 2)
            {
                if($variation['data']['variation']['stockLimitation'] != 0 && $variation['data']['variation']['stockLimitation'] != 2)
                {
                    return $this->isStockNegative($variation);
                }
            }
            else
            {
                if($variation['data']['variation']['stockLimitation'] != $filter['variationStock.isSalable']['stockLimitation'][0])
                {
                    return $this->isStockNegative($variation);
                }
            }
        }

        return false;
    }

    /**
     * Checks if variation stock is negative.
     *
     * @param $variation
     * @return bool
     */
    private function isStockNegative($variation):bool
    {
        if($this->getStock($variation) <= 0)
        {
            return true;
        }

        return false;
    }

    /**
     * Get the stock for the variation.
     *
     * @param $variation
     * @return int
     */
    private function getStock($variation):int
    {
        $stock = 0;

        /**
         * StockRepositoryContract $stockRepositoryContract
         */
        $stockRepositoryContract = pluginApp(StockRepositoryContract::class);

        if($stockRepositoryContract instanceof StockRepositoryContract)
        {
            $stockRepositoryContract->setFilters(['variationId' => $variation['id']]);
            $stockResult = $stockRepositoryContract->listStockByWarehouseType(self::STOCK_WAREHOUSE_TYPE, ['stockNet'], 1, 1);

            if($stockResult instanceof PaginatedResult)
            {
                $result = $stockResult->getResult();

                if($result instanceof Collection)
                {
                    foreach($result as $model)
                    {
                        if($model instanceof Stock)
                        {
                            $stock = (int)$model->stockNet;
                        }
                    }
                }
            }
        }

        return $stock;
    }
}