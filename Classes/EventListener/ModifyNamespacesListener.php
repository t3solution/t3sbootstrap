<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Fluid\Event\ModifyNamespacesEvent;

#[AsEventListener]
final readonly class ModifyNamespacesListener
{
	public function __invoke(ModifyNamespacesEvent $event): void
	{
		$namespaces = $event->getNamespaces();
		// Replace existing "theme" namespace completely
		$namespaces['t3sb'] = ['T3SBS\\T3sbootstrap\\ViewHelpers'];
		$event->setNamespaces($namespaces);
	}
}
