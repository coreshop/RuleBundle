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

namespace CoreShop\Bundle\RuleBundle\Event;

use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RuleAvailabilityCheckEvent extends Event
{
    public function __construct(private RuleInterface $rule, private string $ruleType, private bool $available)
    {
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    public function getRuleType(): string
    {
        return $this->ruleType;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailability(bool $available): void
    {
        $this->available = $available;
    }
}
