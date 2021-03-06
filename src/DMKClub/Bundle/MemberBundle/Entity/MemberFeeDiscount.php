<?php
namespace DMKClub\Bundle\MemberBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberFeeDiscount;

/**
 * Class MemberFeeDiscount
 *
 * @ORM\Entity
 * @ORM\Table(name="dmkclub_member_feediscount")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *   defaultValues={
 *     "entity"={
 *       "icon"="icon-list-alt"
 *     }
 *   }
 * )
 */
class MemberFeeDiscount extends ExtendMemberFeeDiscount
{

    /**
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Soap\ComplexType("int", nillable=true)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\Member", inversedBy="memberFeeDiscounts")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", onDelete="CASCADE")
     * @Oro\Versioned
     */
    protected $member;

    /**
     *
     * @var \Date
     * @ORM\Column(name="start_date", type="date", nullable=true)
     * @Oro\Versioned
     */
    protected $startDate;

    /**
     *
     * @var \Date
     * @ORM\Column(name="end_date", type="date", nullable=true)
     * @Oro\Versioned
     */
    protected $endDate;

    /**
     *
     * @var string
     * @ORM\Column(name="reason", type="string", length=255, nullable=true)
     */
    private $reason;

    // single_price, total_price
    public function getReason()
    {
        return $this->reason;
    }

    /**
     *
     * @param string $value
     * @return \DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount
     */
    public function setReason($value)
    {
        $this->reason = $value;
        return $this;
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     *
     * @param \DMKClub\Bundle\MemberBundle\Entity\Member $value
     * @return MemberFeeDiscount
     */
    public function setMember(\DMKClub\Bundle\MemberBundle\Entity\Member $value)
    {
        $this->member = $value;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set id
     *
     * @param int $endDate
     * @return Member
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return MemberFeeDiscount
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return MemberFeeDiscount
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Checks if the given period is contained in the current period.
     *
     * @param \DateTime $date
     *
     * @return bool true if the period contains this date
     */
    public function contains(\DateTime $date)
    {
        $gtStart = $this->startDate <= $date;
        $ltEnd = $this->endDate == NULL ? true : ($date <= $this->endDate);
        return $gtStart && $ltEnd;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        $data = [
            $this->getReason(),
            ',',
            $this->getStartDate()->format('d.m.Y'),
            ' - ',
            ($this->getEndDate() ? $this->getEndDate()->format('d.m.Y') : '...')
        ];
        $str = implode('', $data);
        return $str;
    }
}
