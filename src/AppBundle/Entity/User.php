<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 11-Dec-15
 * Time: 15:52
 */

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="study_livre_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, name="custom_email", nullable=true)
     */
    protected $customEmail;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    protected $facebook_id;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebook_access_token;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    protected $google_id;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $google_access_token;

    /**
     * @ORM\Column(name="first_name", type="text", length=16777215, nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $surname;

    /**
     * @ORM\Column(name="insert_date_time", type="datetime")
     */
    protected $insertDateTime;

    /**
     * @ORM\Column(name="date_of_birth", type="datetime", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="string", name="real_name", length=255, nullable=true)
     */
    protected $realName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nickname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $picture;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    protected $mentorCode;

    /**
     * @ORM\Column(type="boolean", name="agreed_to_cookie")
     */
    protected $agreedToCookie;

    /**
     * @ORM\Column(type="datetime", name="agreed_to_cookie_date_time", nullable=true)
     */
    protected $agreedToCookieDateTime;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Education\Educations", mappedBy="user")
     */
    private $education;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\SharedReports", mappedBy="user")
     */
    private $sharedReports;

    public function __construct()
    {
        parent::__construct();
        $this->sharedReports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getStatisticsByMentor($mentorId)
    {
        $total = 0;
        $revised = 0;
        $rated = 0;

        foreach ($this->sharedReports as $report)
        {
            if($report->getHasAccepted() && $report->getMentorUserId() == $mentorId)
            {
                $total ++;
                if($report->getHasRevised()) $revised ++;
                if($report->getRating() != null) $rated ++;
            }
        }

        return array('total' => $total, 'revised' => $revised, 'rated' => $rated);
    }

    /**
     * Set customEmail
     *
     * @param string $customEmail
     *
     * @return User
     */
    public function setCustomEmail($customEmail)
    {
        $this->customEmail = $customEmail;

        return $this;
    }

    /**
     * Get customEmail
     *
     * @return string
     */
    public function getCustomEmail()
    {
        return $this->customEmail;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set googleAccessToken
     *
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get googleAccessToken
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set insertDateTime
     *
     * @param \DateTime $insertDateTime
     *
     * @return User
     */
    public function setInsertDateTime($insertDateTime)
    {
        $this->insertDateTime = $insertDateTime;

        return $this;
    }

    /**
     * Get insertDateTime
     *
     * @return \DateTime
     */
    public function getInsertDateTime()
    {
        return $this->insertDateTime;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set realName
     *
     * @param string $realName
     *
     * @return User
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     *
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set mentorCode
     *
     * @param string $mentorCode
     *
     * @return User
     */
    public function setMentorCode($mentorCode)
    {
        $this->mentorCode = $mentorCode;

        return $this;
    }

    /**
     * Get mentorCode
     *
     * @return string
     */
    public function getMentorCode()
    {
        return $this->mentorCode;
    }

    /**
     * Set education
     *
     * @param \AppBundle\Entity\Education\Educations $education
     *
     * @return User
     */
    public function setEducation(\AppBundle\Entity\Education\Educations $education = null)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return \AppBundle\Entity\Education\Educations
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Add sharedReport
     *
     * @param \AppBundle\Entity\Report\SharedReports $sharedReport
     *
     * @return User
     */
    public function addSharedReport(\AppBundle\Entity\Report\SharedReports $sharedReport)
    {
        $this->sharedReports[] = $sharedReport;

        return $this;
    }

    /**
     * Remove sharedReport
     *
     * @param \AppBundle\Entity\Report\SharedReports $sharedReport
     */
    public function removeSharedReport(\AppBundle\Entity\Report\SharedReports $sharedReport)
    {
        $this->sharedReports->removeElement($sharedReport);
    }

    /**
     * Get sharedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedReports()
    {
        return $this->sharedReports;
    }

    /**
     * Set agreedToCookie
     *
     * @param boolean $agreedToCookie
     *
     * @return User
     */
    public function setAgreedToCookie($agreedToCookie)
    {
        $this->agreedToCookie = $agreedToCookie;

        return $this;
    }

    /**
     * Get agreedToCookie
     *
     * @return boolean
     */
    public function getAgreedToCookie()
    {
        return $this->agreedToCookie;
    }

    /**
     * Set agreedToCookieDateTime
     *
     * @param \DateTime $agreedToCookieDateTime
     *
     * @return User
     */
    public function setAgreedToCookieDateTime($agreedToCookieDateTime)
    {
        $this->agreedToCookieDateTime = $agreedToCookieDateTime;

        return $this;
    }

    /**
     * Get agreedToCookieDateTime
     *
     * @return \DateTime
     */
    public function getAgreedToCookieDateTime()
    {
        return $this->agreedToCookieDateTime;
    }
}
