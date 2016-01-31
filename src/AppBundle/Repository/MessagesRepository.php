<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Jan-16
 * Time: 21:39
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Messages;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\PaginationInterface;
use AppBundle\Interfaces\SortableInterface;
use Doctrine\ORM\EntityRepository;

class MessagesRepository extends EntityRepository implements SortableInterface, PaginationInterface
{
    private $sortItems = array('ASC', 'DESC');

    public function getCountByUserId($userId)
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.userId = :userId')
            ->setParameter('userId', $userId);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Sorts the given attribute and returns a number of entities,
     * started at the offset
     *
     * @param $attribute
     * @param $order
     * @param $offset
     * @param $userId
     * @param $limit
     * @return array of entities
     * @throws \Exception
     */
    public function sortByAttribute($attribute, $order, $offset, $limit, $userId)
    {
        if(in_array($attribute, $this->getSortableAttributes())) {
            if(in_array(strtoupper($order), $this->sortItems)) {
                return $this->findBy(array('userId' => $userId), array($attribute => $order), $limit, $offset);
            }
        }
        //TODO Create frontend exception with translations
        throw new \Exception('');
    }

    /**
     * Gets all sortable attributes for the entity
     *
     * @return array
     */
    public function getSortableAttributes()
    {
        return array(
            'isRead',
            'sendDateTime'
        );
    }

    /**
     * Gets all entities for the given offset and limit belonging to the userId
     *
     * @param $offset int
     * @param $limit int
     * @param $attribute string
     * @param $order string
     * @param $userId int
     * @return mixed
     * @throws \Exception
     */
    function getByPage($offset, $limit, $attribute, $order, $userId)
    {
        $sort = null;
        if($attribute != null && $order != null)
            $sort = array($attribute => $order);
        if($sort == null || in_array($attribute, $this->getSortableAttributes())) {
            if($sort == null || in_array(strtoupper($order), $this->sortItems)) {
                return $this->findBy(array('userId' => $userId), $sort, $limit, $offset);
            }
        }
        throw new \Exception('');
    }

    /**
     * Gets the count of entities
     *
     * @return mixed
     */
    function getPaginationCount()
    {
        return $this->getCountByUserId(func_get_args()[0]);
    }
}