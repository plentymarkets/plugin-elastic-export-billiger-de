<?php

namespace ElasticExportBilligerDE\Helper;

use Plenty\Modules\Item\Property\Contracts\PropertyMarketReferenceRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Item\Property\Models\PropertyName;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Plugin\Log\Loggable;

class PropertyHelper
{
    use Loggable;

    const BILLIGER_DE = 151.00;

    const PROPERTY_TYPE_TEXT = 'text';
    const PROPERTY_TYPE_SELECTION = 'selection';

    /**
     * @var array
     */
    private $itemPropertyCache = [];

    /**
     * @var PropertyNameRepositoryContract
     */
    private $propertyNameRepository;

    /**
     * @var PropertyMarketReferenceRepositoryContract
     */
    private $propertyMarketReferenceRepository;

    /**
     * PropertyHelper constructor.
     *
     * @param PropertyNameRepositoryContract $propertyNameRepository
     * @param PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository
     */
    public function __construct(
        PropertyNameRepositoryContract $propertyNameRepository,
        PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository)
    {
        $this->propertyNameRepository = $propertyNameRepository;
        $this->propertyMarketReferenceRepository = $propertyMarketReferenceRepository;
    }

    /**
     * Get property.
     *
     * @param  array   $variation
     * @param  KeyValue $settings
     * @param  string   $property
     * @return string
     */
    public function getProperty($variation, KeyValue $settings, string $property):string
    {
        $itemPropertyList = $this->getItemPropertyList($variation, $settings->get('lang'));

        if(array_key_exists($property, $itemPropertyList))
        {
            return $itemPropertyList[$property];
        }

        return '';
    }

    /**
     * Returns a list of additional configured properties for further usage.
     * The properties have to have a configuration for billiger.de.
     *
     * @param array $variation
     * @param string $lang
     * @return array
     */
    private function getItemPropertyList($variation, $lang = 'de'):array
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemPropertyCache))
        {
            $list = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file' &&
                    $property['property']['valueType'] != 'empty')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], $lang);
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::BILLIGER_DE);

                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->externalComponent == '0')
                    {
                        continue;
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_TEXT)
                    {
                        if(is_array($property['texts']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = $property['texts'][0]['value'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_SELECTION)
                    {
                        if(is_array($property['selection']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = $property['selection'][0]['name'];
                        }
                    }
                }
            }

            $this->itemPropertyCache[$variation['data']['item']['id']] = $list;
        }

        return $this->itemPropertyCache[$variation['data']['item']['id']];
    }
}