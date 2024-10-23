<?php

namespace AcMarche\Icar\Repository;

use AcMarche\Icar\Utils\CoordonateUtils;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class IcarRepository
{
    public function __construct(
        private readonly IcarRemoteRepository $icarRemoteRepository,
        private readonly CacheInterface $cacheItem,
    ) {}

    /**
     * @return object
     * @throws InvalidArgumentException
     */
    public function getCommune(): object
    {
        return $this->cacheItem->get('icar-communes', function (CacheItemInterface $item) {
            $item->expiresAfter(3600 * 10);

            return json_decode(
                $this->icarRemoteRepository->searchCommunes('marche-en-famenne'),
                null,
                512,
                JSON_THROW_ON_ERROR,
            );
        });
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function findLocalitesByCp(): array
    {
        $localites = $this->cacheItem->get('icar-localites', function (CacheItemInterface $item) {
            $item->expiresAfter(3600 * 10);

            return json_decode(
                $this->icarRemoteRepository->getListeLocalitesByCp(6900),
                null,
                512,
                JSON_THROW_ON_ERROR,
            );
        });
        $items = $localites->localites;
        usort($items, function ($a, $b) {
            return strcmp($a->nom, $b->nom);
        });

        return $items;
    }

    /**
     * @param string|null $localiteSelected
     * @return object
     * @throws InvalidArgumentException
     */
    public function findRuesByLocalite(?string $localiteSelected): object
    {
        $key = 'icar-rues';
        if ($localiteSelected) {
            $key .= '-'.$localiteSelected;

            return $this->cacheItem->get($key, function (CacheItemInterface $item) use ($localiteSelected) {
                $item->expiresAfter(3600 * 10);

                return json_decode(
                    $this->icarRemoteRepository->getListeRuesByLocalite($localiteSelected),
                    null,
                    512,
                    JSON_THROW_ON_ERROR,
                );
            });
        } else {
            return $this->cacheItem->get($key, function () {
                return json_decode(
                    $this->icarRemoteRepository->getListeRuesByCp(6900),
                    null,
                    512,
                    JSON_THROW_ON_ERROR,
                );
            });
        }
    }

    public function geolocalisation(): void
    {
        $communes = $this->getCommune();
        $utils = new CoordonateUtils();
        dump($utils->lambertI($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertII($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertIII($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertIIExtend($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lamberIV($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambert93($communes[0]->xMin, $communes[0]->yMin));
    }

    public function urlExecuted(): ?string
    {
        return $this->icarRemoteRepository->urlExecuted;
    }
}