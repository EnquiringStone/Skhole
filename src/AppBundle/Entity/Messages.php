<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 24-Jan-16
 * Time: 14:50
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessagesRepository")
 * @ORM\Table(name="messages")
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="user_id")
     */
    protected $userId;

    /**
     * @ORM\Column(type="datetime", name="send_date_time")
     */
    protected $sendDateTime;

    /**
     * @ORM\Column(type="boolean", name="is_read")
     */
    protected $isRead;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $contents;

    /**
     * @ORM\Column(type="string", length=255, name="sender_name")
     */
    protected $senderName;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Messages
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set sendDateTime
     *
     * @param \DateTime $sendDateTime
     *
     * @return Messages
     */
    public function setSendDateTime($sendDateTime)
    {
        $this->sendDateTime = $sendDateTime;

        return $this;
    }

    /**
     * Get sendDateTime
     *
     * @return \DateTime
     */
    public function getSendDateTime()
    {
        return $this->sendDateTime;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Messages
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Messages
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contents
     *
     * @param string $contents
     *
     * @return Messages
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set senderName
     *
     * @param string $senderName
     *
     * @return Messages
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * Get senderName
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Messages
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
