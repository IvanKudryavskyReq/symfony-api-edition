<?php

namespace Requestum\ApiBundle\Action;

use Requestum\ApiBundle\Action\Extension\FiltersExtensionInterface;

class SubResourseFilter implements FiltersExtensionInterface
{
    /**
     * @var string
     */
    private $nameResourceField;

    /**
     * @var string
     */
    private $valueResourceField;

    /**
     * SubResourseFilter constructor.
     *
     * @param string $nameResourceField
     * @param string $valueResourceField
     */
    public function __construct($nameResourceField, $valueResourceField)
    {
        $this->nameResourceField = $nameResourceField;
        $this->valueResourceField = $valueResourceField;
    }

    /**
     * @return string
     */
    public function getValueResourceField()
    {
        return $this->valueResourceField;
    }

    /**
     * @param $valueResourceField
     *
     * @return SubResourseFilter
     */
    public function setValueResourceField($valueResourceField): self
    {
         $this->valueResourceField = $valueResourceField;

         return $this;
    }

    /**
     * @return string
     */
    public function getNameResourceField()
    {
        return $this->nameResourceField;
    }


    /**
     * @param $nameResourceField
     *
     * @return SubResourseFilter
     */
    public function setNameResourceField($nameResourceField): self
    {
        $this->nameResourceField = $nameResourceField;

        return $this;
    }

    /**
     * @param array $filters
     * @param string $entityClass
     * @param array $options
     *
     * @return mixed
     */
    public function extend(&$filters, $entityClass, $options = [])
    {
        $filters[$this->getNameResourceField()] = $this->getValueResourceField();

        return $filters;
    }
}