<?php

namespace ElasticExportBilligerDE\Helper;

use Plenty\Modules\Item\Property\Contracts\PropertyMarketReferenceRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Item\Property\Models\PropertyName;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Plugin\Log\Loggable;

/**
 * Class PropertyHelper
 * @package ElasticExportBilligerDE\Helper
 */
class PropertyHelper
{
    use Loggable;

    const BILLIGER_DE = 112.00;

    const PROPERTY_TYPE_TEXT = 'text';
    const PROPERTY_TYPE_SELECTION = 'selection';
    const PROPERTY_TYPE_EMPTY = 'empty';
    const PROPERTY_TYPE_INT = 'int';
    const PROPERTY_TYPE_FLOAT = 'float';

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
    private function getItemPropertyList($variation, $lang):array
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemPropertyCache))
        {
            $list = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], $lang);
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::BILLIGER_DE);

                    // Skip properties which do not have the External Component set up
                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->externalComponent == '0')
                    {
                        $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::item.variationPropertyNotAdded', [
                            'ItemId'            => $variation['data']['item']['id'],
                            'VariationId'       => $variation['id'],
                            'Property'          => $property,
                            'ExternalComponent' => $propertyMarketReference->externalComponent
                        ]);

                        continue;
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_TEXT)
                    {
                        if(is_array($property['texts']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = (string)$property['texts']['value'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_SELECTION)
                    {
                        if(is_array($property['selection']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = (string)$property['selection']['name'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_EMPTY)
                    {
                        $list[(string)$propertyMarketReference->externalComponent] = (string)$propertyMarketReference->externalComponent;
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_INT)
                    {
                        if(!is_null($property['valueInt']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = (string)$property['valueInt'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_FLOAT)
                    {
                        if(!is_null($property['valueFloat']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = (string)$property['valueFloat'];
                        }
                    }

                }
            }

            // Cache the properties list for this item
            $this->itemPropertyCache[$variation['data']['item']['id']] = $list;

            $this->getLogger(__METHOD__)->debug('ElasticExportBilligerDE::item.variationPropertyList', [
                'ItemId'        => $variation['data']['item']['id'],
                'VariationId'   => $variation['id'],
                'PropertyList'  => count($list) > 0 ? $list : 'no properties'
            ]);
        }

        return $this->itemPropertyCache[$variation['data']['item']['id']];
    }
}