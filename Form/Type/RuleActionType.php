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

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RuleActionType extends AbstractConfigurableRuleElementType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder->add('id', IntegerType::class, ['mapped' => false]);
        $builder->add('sort', IntegerType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_rule_action';
    }
}
