<?php

namespace Elastica\Type;
use Elastica\Exception\InvalidException;
use Elastica\Request;
use Elastica\Type as BaseType;

/**
 * Elastica Mapping object
 *
 * @category Xodoa
 * @package Elastica
 * @author Nicolas Ruflin <spam@ruflin.com>
 * @link http://www.elasticsearch.org/guide/reference/mapping/
 */
class MappingType
{
    /**
     * Mapping
     *
     * @var array Mapping
     */
    protected $_mapping = array();

    /**
     * Type
     *
     * @var Elastica\Type Type object
     */
    protected $_type = null;

    /**
     * Construct Mapping
     *
     * @param Elastica\Type $type       OPTIONAL Type object
     * @param array         $properties OPTIONAL Properties
     */
    public function __construct(BaseType $type = null, array $properties = array())
    {
        if ($type) {
            $this->setType($type);
        }

        if (!empty($properties)) {
            $this->setProperties($properties);
        }
    }

    /**
     * Sets the mapping type
     * Enter description here ...
     * @param  Elastica\Type             $type Type object
     * @return Elastica\Type\MappingType Current object
     */
    public function setType(BaseType $type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * Sets the mapping properties
     *
     * @param  array                     $properties Properties
     * @return Elastica\Type\MappingType Mapping object
     */
    public function setProperties(array $properties)
    {
        return $this->setParam('properties', $properties);
    }

    /**
     * Returns mapping type
     *
     * @return Elastica\Type Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Sets source values
     *
     * To disable source, argument is
     * array('enabled' => false)
     *
     * @param  array                     $source Source array
     * @return Elastica\Type\MappingType Current object
     * @link http://www.elasticsearch.org/guide/reference/mapping/source-field.html
     */
    public function setSource(array $source)
    {
        return $this->setParam('_source', $source);
    }

    /**
     * Disables the source in the index
     *
     * Param can be set to true to enable again
     *
     * @param  bool                      $enabled OPTIONAL (default = false)
     * @return Elastica\Type\MappingType Current object
     */
    public function disableSource($enabled = false)
    {
        return $this->setSource(array('enabled' => $enabled));
    }

    /**
     * Sets raw parameters
     *
     * Possible options:
     * _uid
     * _id
     * _type
     * _source
     * _all
     * _analyzer
     * _boost
     * _parent
     * _routing
     * _index
     * _size
     * properties
     *
     * @param  string                    $key   Key name
     * @param  mixed                     $value Key value
     * @return Elastica\Type\MappingType Current object
     */
    public function setParam($key, $value)
    {
        $this->_mapping[$key] = $value;

        return $this;
    }

    /**
     * Set TTL
     *
     * @param  array                     $params TTL Params (enabled, default, ...)
     * @return Elastica\Type\MappingType
     */
    public function setTtl(array $params)
    {
        return $this->setParam('_ttl', $params);

    }

    /**
     * Enables TTL for all documents in this type
     *
     * @param  bool                      $enabled OPTIONAL (default = true)
     * @return Elastica\Type\MappingType
     */
    public function enableTtl($enabled = true)
    {
        return $this->setTTL(array('enabled' => $enabled));
    }

    /**
     * Converts the mapping to an array
     *
     * @throws Elastica\Exception\InvalidException
     * @return array                               Mapping as array
     */
    public function toArray()
    {
        $type = $this->getType();

        if (empty($type)) {
            throw new InvalidException('Type has to be set');
        }

        return array($type->getName() => $this->_mapping);
    }

    /**
     * Submits the mapping and sends it to the server
     *
     * @return Elastica\Response Response object
     */
    public function send()
    {
        $path = '_mapping';

        return $this->getType()->request($path, Request::PUT, $this->toArray());
    }

    /**
     * Creates a mapping object
     *
     * @param  array|Elastica\Type\MappingType     $mapping Mapping object or properties array
     * @return Elastica\Type\MappingType           Mapping object
     * @throws Elastica\Exception\InvalidException If invalid type
     */
    public static function create($mapping)
    {
        if (is_array($mapping)) {
            $mappingObject = new MappingType();
            $mappingObject->setProperties($mapping);
        } else {
            $mappingObject = $mapping;
        }

        if (!$mappingObject instanceof MappingType) {
            throw new InvalidException('Invalid object type');
        }

        return $mappingObject;
    }
}