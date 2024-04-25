<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\PayumBundle\Form\Type\PaypalGatewayConfigurationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class PaypalGatewayConfigurationTypeExtension extends AbstractTypeExtension implements DataMapperInterface
{
    public function __construct(private DataMapperInterface $dataMapper)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper($this);

        $builder->add('http_client', HiddenType::class, [
            'empty_data' => '@sylius.payum.http_client',
            'label' => false,
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [PaypalGatewayConfigurationType::class];
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {
        unset($viewData['payum.http_client']);

        $this->dataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData)
    {
        $forms = iterator_to_array($forms);

        $newForm = $forms['http_client'];
        unset($forms['http_client']);

        $viewData['payum.http_client'] = $newForm->getData();

        $this->dataMapper->mapFormsToData(new ArrayCollection($forms), $viewData);
    }
}
