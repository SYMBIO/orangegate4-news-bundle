<?php

namespace Symbio\OrangeGate\NewsBundle\Entity;

use Sonata\NewsBundle\Entity\BasePostRepository;

class PostRepository extends BasePostRepository
{

    public function getListQuery($locale, $maxResults = false)
    {
        $qb = $this->createQueryBuilder('n');


        $query = $qb
            ->select('n,m')
            ->leftJoin('n.image', 'm')
            ->where('n.enabled = :true')
            ->andWhere('DATE(n.publicationDateStart) <= CURRENT_DATE()')
            ->orderBy('n.publicationDateStart', 'desc')
            ->addOrderBy('n.title', 'asc')
            ->setParameter('true', true)

        ;

        if ($maxResults) {
            $query->setMaxResults($maxResults);
        }

        $query = $query->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );

        return $query;
    }

    /**
     * get Previous and Next Entites for Post detail
     *
     * @param Post $article
     * @return array ('previous' => Post $article, 'next' => Post $article)
     * @throws \Exception
     */
    public function getPreviousAndNextArticle(Post $article)
    {
        $queryBuilder = $this->getArticlesQueryBuilder($article);

        try {
            $previousArticle = $this->getPrevious($queryBuilder);
            $nextArticle = $this->getNext($queryBuilder);
        } catch (\Exception $e) {
            \error_log($e->getMessage());
            throw $e;
        }

        return array(
            'previous' => $previousArticle,
            'next' => $nextArticle
        );
    }

    /**
     * get Current queryBuilder
     *
     * @param Post $article
     * @return \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function getArticlesQueryBuilder(Post $article) {

        $queryBuilder = $this->createQueryBuilder('n')
            ->select('n')
            ->andWhere('n.publicationDateStart IS NULL OR DATE(n.publicationDateStart) <= CURRENT_DATE()')
            ->setParameter('publicationDateStart', $article->getPublicationDateStart())
            ->orderBy('n.publicationDateStart', 'desc')
            ->addOrderBy('n.title', 'asc')
            ->setMaxResults(1)
        ;

        return $queryBuilder;
    }


    /**
     * Nacteni predchozi novinky
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return Post $previousArticle
     * @throws \Exception $e - pouze v pripade obecne vyjimky
     */
    public function getPrevious(\Doctrine\ORM\QueryBuilder $queryBuilder) {

        $previousQuery = clone $queryBuilder;

        try {
            $previousArticle = $previousQuery
                ->andWhere('n.publicationDateStart < :publicationDateStart')
                ->orderBy('n.publicationDateStart', 'desc')
                ->getQuery()
                ->getSingleResult();
        } catch(\Doctrine\Orm\NoResultException $e) {
            $previousArticle = false;
        } catch (\Exception $e) {
            \error_log($e->getMessage());
            throw $e;
        }

        return $previousArticle;
    }


    /**
     * Nacteni nasledujiciho novinky
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return Post $nextArticle
     * @throws \Exception $e - pouze v pripade obecne vyjimky
     */
    public function getNext(\Doctrine\ORM\QueryBuilder $queryBuilder) {

        $nextQuery = clone $queryBuilder;

        try {
            $nextArticle = $nextQuery
                ->andWhere('n.publicationDateStart > :publicationDateStart')
                ->orderBy('n.publicationDateStart', 'asc')
                ->getQuery()
                ->getSingleResult();
        } catch(\Doctrine\Orm\NoResultException $e) {
            $nextArticle = false;
        } catch (\Exception $e) {
            \error_log($e->getMessage());
            throw $e;
        }

        return $nextArticle;

    }


    /**
     * Nacteni detailu novinky
     *
     * @param String $slug
     * @param String $locale
     * @return array -
     */
    public function getDetail($slug, $locale)
    {
        $qb = $this->createQueryBuilder('n');

        $query = $qb
            ->select('n,m')
            ->leftJoin('n.image', 'm')
            ->where('n.slug = :slug')
            ->orderBy('n.publicationDateStart', 'desc')
            ->addOrderBy('n.title', 'asc')
            ->setParameter('slug', $slug)
        ;

        $query = $query->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );

        try {
            return $query->getSingleResult();
        } catch(\Doctrine\Orm\NoResultException $e) {
            return array();
        }
    }
}
