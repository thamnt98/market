<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\Checkout\Model\Method\Specification;

use Magento\Payment\Model\Method\SpecificationInterface;

/**
 * Composite specification
 *
 * Use this class for virtual types declaration.
 *
 * @api
 * @since 100.0.2
 */
class Composite implements SpecificationInterface
{
    /**
     * Specifications collection
     *
     * @var SpecificationInterface[]
     */
    protected $specifications = [];

    /**
     * Construct
     *
     * @param Factory $factory
     * @param array $specifications
     */
    public function __construct(Factory $factory, $specifications = [\Magento\Multishipping\Model\Payment\Method\Specification\Enabled::class])
    {
        foreach ($specifications as $specification) {
            $this->specifications[] = $factory->create($specification);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($paymentMethod)
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($paymentMethod)) {
                return false;
            }
        }
        return true;
    }
}
