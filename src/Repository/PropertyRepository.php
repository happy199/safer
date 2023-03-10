<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    // Sauvegarder une entité Property.

    public function save(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Sauvegarder une entité Property.

    public function remove(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    // Recherche les annonces en fonction du formulaire de recherche textuel
   
    public function search($mots = null, $categorie = null){
        $query = $this->createQueryBuilder('a');
        $query->where('a.category = :categorie')
            ->setParameter('categorie', $categorie);
        if($mots != null){
            $query->andWhere('MATCH_AGAINST(a.title, a.description, a.address, a.status, a.city, a.department) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        return $query->getQuery()->getResult();
    }

    // Recherche les annonces en fonction du formulaire de recherche par prix

    public function findByPriceRange($minPrice, $maxPrice)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price >= :minPrice')
            ->andWhere('p.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->getQuery()
            ->getResult();
    }

    // Recherche les annonces et avec la pagination

    public function findForPagination(?Category $category = null): Query
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC');

        if ($category) {
            $qb->leftJoin('p.category', 'c')
                ->where($qb->expr()->eq('c.id', ':id'))
                ->setParameter('id', $category->getId());
        }

        return $qb->getQuery();
    }

}
