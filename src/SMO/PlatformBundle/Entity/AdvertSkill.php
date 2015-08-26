<?php
// src/SMO/PlatformBundle/Entity/AdvertSkill/AdvertSkill.php

namespace SMO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertSkill
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SMO\PlatformBundle\Entity\AdvertSkillRepository")
 */
class AdvertSkill
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;
    
    /**
    * @ORM\ManyToOne(targetEntity="SMO\PlatformBundle\Entity\Advert")
    * @ORM\JoinColumn(nullable=false)
    */
    private $advert;
    
    /**
    * @ORM\ManyToOne(targetEntity="SMO\PlatformBundle\Entity\Skill")
    * @ORM\JoinColumn(nullable=false)
    */
    private $skill;
    
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
     * Set level
     *
     * @param string $level
     * @return AdvertSkill
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * Set advert
     *
     * @param \SMO\PlatformBundle\Entity\Advert $advert
     * @return AdvertSkill
     */
    public function setAdvert(\SMO\PlatformBundle\Entity\Advert $advert)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \SMO\PlatformBundle\Entity\Advert 
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set skill
     *
     * @param \SMO\PlatformBundle\Entity\Skill $skill
     * @return AdvertSkill
     */
    public function setSkill(\SMO\PlatformBundle\Entity\Skill $skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return \SMO\PlatformBundle\Entity\Skill 
     */
    public function getSkill()
    {
        return $this->skill;
    }
}
