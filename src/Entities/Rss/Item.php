<?php

namespace Lukaswhite\FeedWriter\Entities\Rss;

use Lukaswhite\FeedWriter\Entities\Entity;
use Lukaswhite\FeedWriter\Entities\General\Enclosure;
use Lukaswhite\FeedWriter\Traits\HasLink;
use Lukaswhite\FeedWriter\Traits\HasMedia;
use Lukaswhite\FeedWriter\Traits\HasMediaGroups;
use Lukaswhite\FeedWriter\Traits\HasPublishedDate;
use Lukaswhite\FeedWriter\Traits\HasTitleAndDescription;

/**
 * Class Item
 *
 * @package Lukaswhite\FeedWriter\Entities\Rss
 */
class Item extends Entity
{
    use HasTitleAndDescription,
        HasLink,
        HasPublishedDate,
        HasMedia,
        HasMediaGroups;

    /**
     * The GUID (globally unique identifier)
     *
     * @var string
     */
    protected $guid;

    /**
     * Whether the GUID is a permalink
     *
     * @var bool
     */
    protected $guidIsPermalink = false;

    /**
     * The enclosures
     *
     * @var array
     */
    protected $enclosures = [ ];

    /**
     * Set the GUID
     *
     * @param string $guid
     * @param bool $isPermalink
     * @return $this
     */
    public function guid( string $guid, bool $isPermalink = false ) : self
    {
        $this->guid = $guid;
        $this->guidIsPermalink = $isPermalink;
        return $this;
    }

    /**
     * Add an enclosure
     *
     * @return Enclosure
     */
    public function addEnclosure( ) : Enclosure
    {
        $enclosure = $this->createEntity( Enclosure::class );
        $this->enclosures[ ] = $enclosure;
        return $enclosure;
    }

    /**
     * Create a DOM element that represents this entity.
     *
     * @return \DOMElement
     */
    public function element( ) : \DOMElement
    {
        $item = $this->feed->getDocument( )->createElement( 'item' );

        $this->addTitleAndDescriptionElements( $item );

        $this->addLinkElement( $item );

        if ( $this->guid ) {

            if ( $this->guidIsPermalink ) {
                $guid = $this->createElement( 'guid', $this->guid, [ 'isPermaLink' => 'true'  ] );
            } else {
                $guid = $this->createElement( 'guid', $this->guid );
            }
            $item->appendChild( $guid );
        }

        if ( count( $this->enclosures ) ) {
            foreach( $this->enclosures as $enclosure ) {
                $item->appendChild( $enclosure->element( $this->feed->getDocument( ) ) );
            }
        }

        $this->addMediaElements( $item );
        $this->addMediaGroupElements( $item );

        return $item;
    }
}