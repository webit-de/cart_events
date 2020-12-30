<?php
declare(strict_types=1);
namespace Extcode\CartEvents\EventListener\ProcessOrderCreate;

/*
 * This file is part of the package extcode/cart-events.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\ProcessOrderCreateEvent;
use Extcode\CartEvents\Domain\Repository\EventDateRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlushCache
{
    /**
     * @var EventDateRepository
     */
    protected $eventDateRepository;

    public function __construct(
        EventDateRepository $eventDateRepository
    ) {
        $this->eventDateRepository = $eventDateRepository;
    }

    public function __invoke(ProcessOrderCreateEvent $event): void
    {
        $cartProducts = $event->getCart()->getProducts();

        foreach ($cartProducts as $cartProduct) {
            if ($cartProduct->getProductType() === 'CartEvents') {
                $eventDate = $this->eventDateRepository->findByUid($cartProduct->getProductId());

                $cacheTag = 'tx_cartevents_event_' . $eventDate->getEvent()->getUid();
                $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
                $cacheManager->flushCachesInGroupByTag('pages', $cacheTag);
            }
        }
    }
}
