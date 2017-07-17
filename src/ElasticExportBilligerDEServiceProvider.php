<?php

namespace ElasticExportBilligerDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;

/**
 * Class ElasticExportBilligerDEServiceProvider
 * @package ElasticExportBilligerDE
 */
class ElasticExportBilligerDEServiceProvider extends DataExchangeServiceProvider
{
    /**
     * Abstract function definition for registering the service provider.
     */
    public function register()
    {

    }

    /**
     * Adds the export format to the export container.
     *
     * @param ExportPresetContainer $container
     */
    public function exports(ExportPresetContainer $container)
    {
        $container->add(
            'BilligerDE-Plugin',
            'ElasticExportBilligerDE\ResultField\BilligerDE',
            'ElasticExportBilligerDE\Generator\BilligerDE',
            '',
            true,
            true
        );
    }
}