<?php

namespace ElasticExportBilligerDE;


class ElasticExportBilligerDEServiceProvider extends DataExchangeServiceProvider
{
    public function register()
    {

    }

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