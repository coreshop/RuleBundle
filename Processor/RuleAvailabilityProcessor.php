<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\RuleBundle\Accessor;

use CoreShop\Bundle\CoreBundle\Event\RuleAvailabilityCheckEvent;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Rule\Condition\RuleAvailabilityAccessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RuleAvailabilityProcessor implements RuleAvailabilityProcessorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * RuleAvailabilityProcessor constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $entityManager
     * @param ServiceRegistryInterface $ruleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        ServiceRegistryInterface $ruleRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        /** @var RuleAvailabilityAccessorInterface $ruleAssessor */
        foreach ($this->ruleRepository->all() as $ruleAssessor) {

            foreach ($ruleAssessor->getRules() as $rule) {
                $ruleIsAvailable = $ruleAssessor->isValid($rule);
                $this->processRule($rule, $ruleIsAvailable);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param RuleInterface $rule
     * @param bool          $ruleIsAvailable
     */
    private function processRule(RuleInterface $rule, bool $ruleIsAvailable)
    {
        $event = $this->eventDispatcher->dispatch(
            'coreshop.rule.availability_check',
            new RuleAvailabilityCheckEvent($rule, get_class($rule), $ruleIsAvailable)
        );

        if ($event->isAvailable() === false) {
            if ($rule instanceof ToggleableInterface) {
                var_dump($rule->getName());
                //$rule->setActive(false);
                //$this->entityManager->persist($rule);
            }
        }
    }
}