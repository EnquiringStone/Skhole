<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Apr-16
 * Time: 21:31
 */

namespace AppBundle\Doctrine;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

class SearchQuery
{
    const AndCorrelation = 'AND';
    const OrCorrelation = 'OR';

    /**
     * @var array
     */
    private $searchParams;

    /**
     * @var string
     */
    private $correlationType;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;
    /**
     * @var array
     */
    private $sort;

    public function __construct(array $searchParams, $correlationType, $offset, $limit, array $sort)
    {
        if(!(array_key_exists('sortAttribute', $sort) && array_key_exists('sortValue', $sort)))
            throw new \Exception('Invalid sort: sortAttribute or sortValue not defined');

        if($correlationType != SearchQuery::AndCorrelation && $correlationType != SearchQuery::OrCorrelation)
            throw new \Exception('Invalid correlationType: '.$correlationType);

        $this->correlationType = $correlationType;
        $this->searchParams = $searchParams;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->sort = $sort;
    }

    public function buildFromQuery(QueryBuilder $qb)
    {
        $params = [];
        foreach($this->searchParams as $entity => $attributes)
        {
            $expressions = [];
            foreach($attributes as $attribute => $value)
            {
                if(is_array($value))
                {
                    foreach($value as $item)
                    {
                        $params[] = $item;
                        $expressions[] = $this->getExpression($qb, $entity, $attribute, $item, sizeof($params));
                    }
                }
                else
                {
                    $params[] = $value;
                    $expressions[] = $this->getExpression($qb, $entity, $attribute, $value, sizeof($params));
                }
            }
            if($this->correlationType == SearchQuery::AndCorrelation)
                $qb->andWhere($this->buildOrQuery($qb, $expressions));
            else
                $qb->orWhere($this->buildOrQuery($qb, $expressions));
        }

        return $qb;
    }

    public function getResult(QueryBuilder $qb, $entity)
    {
        $qb->orderBy($entity.'.'.$this->sort['sortAttribute'], $this->sort['sortValue']);
        $result = $qb->getQuery()->getResult();
        $resultSet = new ArrayCollection($result);

        return array('resultSet' => $resultSet->slice($this->offset, $this->limit), 'total' => count($result));
    }

    private function getExpression(QueryBuilder $qb, $entity, $attribute, $value, $index)
    {
        if ($attribute == 'id') $value = intval($value);

        $expression = $qb->expr()->like($entity.'.'.$attribute, '?'.$index);
        $qb->setParameter($index, is_string($value) ? '%'.$value.'%' : $value);
        return $expression;
    }

    private function buildOrQuery(QueryBuilder $qb, array $expressions)
    {
        $or = $qb->expr()->orX();
        foreach($expressions as $expression)
        {
            $or->add($expression);
        }
        return $or;
    }
}