<?php

namespace SMO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Advert
 *
 * @ORM\Table(name="smo_advert")
 * @ORM\Entity(repositoryClass="SMO\PlatformBundle\Entity\AdvertRepository")
 */
class Advert
{
    /**
     * @ORM\OneToMany(targetEntity="SMO\PlatformBundle\Entity\Application", mappedBy="advert")
     */
    private $applications;
    
    /**
     * @ORM\ManyToMany(targetEntity="SMO\PlatformBundle\Entity\Category", cascade={"persist"})
     */
    private $categories;
    
    /**
     * @ORM\OneToOne(targetEntity="SMO\PlatformBundle\Entity\Image", cascade={"persist"})
     */
    private $image;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
    /**
    * @var boolean
    * 
    * @ORM\Column(name="published", type="boolean")
    */
    private $published = true;
    
    
    /**
    * __construct
    * 
    * @return null
    */
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->categories = new ArrayCollection();
    }
    
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
     * Set date
     *
     * @param \DateTime $date
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Advert
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
     * Set author
     *
     * @param string $author
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set image
     *
     * @param \SMO\PlatformBundle\Entity\Image $image
     * @return Advert
     */
    public function setImage(\SMO\PlatformBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \SMO\PlatformBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add categories
     *
     * @param \SMO\PlatformBundle\Entity\Category $categories
     * @return Advert
     */
    public function addCategory(\SMO\PlatformBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \SMO\PlatformBundle\Entity\Category $categories
     */
    public function removeCategory(\SMO\PlatformBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add applications
     *
     * @param \SMO\PlatformBundle\Entity\Application $applications
     * @return Advert
     */
    public function addApplication(\SMO\PlatformBundle\Entity\Application $application)
    {
        $this->applications[] = $application;
        
        // On lie l'annonce à la candidature
        $application->setAdvert($this);
        
        return $this;
    }

    /**
     * Remove applications
     *
     * @param \SMO\PlatformBundle\Entity\Application $applications
     */
    public function removeApplication(\SMO\PlatformBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplications()
    {
        return $this->applications;
    }
}
