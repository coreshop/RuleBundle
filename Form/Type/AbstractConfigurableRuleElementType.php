<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\RuleBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurableRuleElementType extends AbstractResourceType
{
    public function __construct(string $dataClass, array $validationGroups, private FormTypeRegistryInterface $formTypeRegistry)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                if (!$this->formTypeRegistry->has($type, 'default')) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('type')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['type'])) {
                    return;
                }

                if (!$this->formTypeRegistry->has($data['type'], 'default')) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['type'], 'default'));
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null']);
    }

    /**
     * @param string        $configurationType
     */
    protected function addConfigurationFields(FormInterface $form, $configurationType): void
    {
        $form->add('configuration', $configurationType);
    }

    /**
     * @param mixed         $data
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getType()) {
            return $data->getType();
        }

        return null;
    }
}
