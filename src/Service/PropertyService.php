<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Option;
use App\Repository\PropertyRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Symfony\Component\HttpFoundation\RequestStack;

class PropertyService
{
    public function __construct(
        private RequestStack $requestStack,
        private PropertyRepository $propertyRepo,
        private PaginatorInterface $paginator
    ) {

    }

    public function getPaginatedProperties(?Category $category = null)
    {
        $request = $this->requestStack->getMainRequest();
        $propertiesQuery = $this->propertyRepo->findForPagination($category);
        $page = $request->query->getInt('page', 1);
        $limit = 6;


        return $this->paginator->paginate($propertiesQuery, $page, $limit);
    }
}