<?php

namespace arkuuu\Publin;

/**
 * Class Citation
 *
 * @package arkuuu\Publin
 */
class Citation extends Entity
{

    protected $id;

    protected $publication_id;

    protected $citation_id;

    protected $citation_publication;


    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Returns the publication ID
     *
     * @return    int
     */
    public function getPublicationId()
    {
        return $this->publication_id;
    }


    /**
     * Returns the citation publication
     *
     * @return Publication
     */
    public function getCitationPublication()
    {
        return $this->citation_publication;
    }


    /**
     * Sets the citation publication
     *
     * @param Publication $citation_publication
     *
     * @return bool
     */
    public function setCitationPublication($citation_publication)
    {
        $this->citation_publication = $citation_publication;

        return true;
    }


    /**
     * Returns the citation ID
     *
     * @return int
     */
    public function getCitationId()
    {
        return $this->citation_id;
    }


}
