<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository;

class CarouselContainer implements SingletonInterface
{
    public function __construct(
        private readonly ConnectionPool               $connectionPool,
        private readonly FileRepository               $fileRepository,
        private readonly FrontendRestrictionContainer $frontendRestrictions,
    ) {}

    public function getProcessedData(array $processedData, array $flexconf): array
    {
        $processedData['maxWidth']         = $flexconf['width'] ? $flexconf['width'] . 'px' : '1440px';
        $processedData['interval']         = $flexconf['interval'] ?? 5000;
        $processedData['darkVariant']      = !empty($flexconf['darkVariant']) ? $flexconf['darkVariant'] : 'light';
        $processedData['thumbnails']       = !empty($flexconf['thumbnails']);
        $processedData['mobileIndicators'] = !empty($flexconf['mobileIndicators']) ? '' : ' d-none d-md-block';

        // carousel-fade und carousel-dark als separate Keys — klarer im Template
        $carouselFade  = !empty($flexconf['carouselFade'])  ? ' carousel-fade' : '';
        $carouselFade .= !empty($flexconf['darkVariant'])   ? ' carousel-dark' : '';
        $processedData['carouselFade'] = $carouselFade;

        $carouselUids = $this->fetchCarouselUids((int)$processedData['data']['uid']);

        $carouselSlides = [];
        foreach ($carouselUids as $row) {
            $files = $this->fileRepository->findByRelation('tt_content', 'assets', $row['uid']);
            $carouselSlides[$row['uid']] = $files[0] ?? '';
        }

        $processedData['carouselSlides'] = $carouselSlides;

        return $processedData;
    }

    private function fetchCarouselUids(int $parentUid): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $qb->setRestrictions($this->frontendRestrictions);

        return $qb
            ->select('uid')
            ->from('tt_content')
            ->where(
                $qb->expr()->eq(
                    'tx_container_parent',
                    $qb->createNamedParameter($parentUid, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
