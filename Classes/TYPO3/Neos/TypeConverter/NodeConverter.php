<?php
namespace TYPO3\Neos\TypeConverter;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Neos".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * An Object Converter for nodes which can be used for routing (but also for other
 * purposes) as a plugin for the Property Mapper.
 *
 * @Flow\Scope("singleton")
 */
class NodeConverter extends \TYPO3\TYPO3CR\TypeConverter\NodeConverter {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Neos\Domain\Repository\DomainRepository
	 */
	protected $domainRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Neos\Domain\Repository\SiteRepository
	 */
	protected $siteRepository;

	/**
	 * @var string
	 */
	protected $targetType = 'TYPO3\TYPO3CR\Domain\Model\NodeInterface';

	/**
	 * @var integer
	 */
	protected $priority = 3;

	/**
	 * Creates the context for the nodes based on the given workspace.
	 *
	 * @param string $workspaceName
	 * @param \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration
	 * @return \TYPO3\TYPO3CR\Domain\Service\ContextInterface
	 */
	protected function createContext($workspaceName, \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		$contextProperties = array(
			'workspaceName' => $workspaceName,
			'invisibleContentShown' => FALSE,
			'removedContentShown' => FALSE
		);
		if ($workspaceName !== 'live') {
			$contextProperties['invisibleContentShown'] = TRUE;
			if ($configuration !== NULL && $configuration->getConfigurationValue('TYPO3\TYPO3CR\TypeConverter\NodeConverter', self::REMOVED_CONTENT_SHOWN) === TRUE) {
				$contextProperties['removedContentShown'] = TRUE;
			}
		}

		$currentDomain = $this->domainRepository->findOneByActiveRequest();
		if ($currentDomain !== NULL) {
			$contextProperties['currentSite'] = $currentDomain->getSite();
			$contextProperties['currentDomain'] = $currentDomain;
		} else {
			$contextProperties['currentSite'] = $this->siteRepository->findFirst();
		}

		return $this->contextFactory->create($contextProperties);
	}
}
