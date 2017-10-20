<?php

namespace ElasticExportBilligerDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\ServiceProvider;
use Plenty\Log\Services\ReferenceContainer;

/**
 * Class ElasticExportBilligerDEServiceProvider
 * @package ElasticExportBilligerDE
 */
class ElasticExportBilligerDEServiceProvider extends ServiceProvider
{
    /**
     * Abstract function definition for registering the service provider.
     */
    public function register()
    {

    }

	public function boot(
		ExportPresetContainer $exportPresetContainer
		)
	{
		$exportPresetContainer->add(
			'BilligerDE-Plugin',
			'ElasticExportBilligerDE\ResultField\BilligerDE',
			'ElasticExportBilligerDE\Generator\BilligerDE',
			'',
			true,
			true
		);
	}

}