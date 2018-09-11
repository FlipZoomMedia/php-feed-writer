<?php

namespace Lukaswhite\FeedWriter\Entities\Media;

use Lukaswhite\FeedWriter\Entities\Entity;
use Lukaswhite\FeedWriter\Exceptions\InvalidExpressionException;
use Lukaswhite\FeedWriter\Exceptions\InvalidMediumException;
use Lukaswhite\FeedWriter\Traits\HasDimensions;
use Lukaswhite\FeedWriter\Traits\HasUrl;

/**
 * Class Media
 *
 * @package Lukaswhite\FeedWriter\Helpers
 */
class Media extends Entity
{
    use HasUrl,
        HasDimensions;

    /**
     * Class constants representing the available mediums
     */
    const IMAGE             =   'image';
    const VIDEO             =   'video';
    const AUDIO             =   'audio';
    const DOCUMENT          =   'document';
    const EXECUTABLE        =   'executable';

    /**
     * Class constants representing the available expressions
     */
    const SAMPLE            =   'sample';
    const FULL              =   'full';
    const NONSTOP           =   'nonstop';

    /**
     * The (Mime) type
     * @var string
     */
    protected $type;

    /**
     * The medium; e.g. image, video
     *
     * @var string
     */
    protected $medium;

    /**
     * Indicates whether you’re linking to a short sample of a longer video (“sample”),
     * or if you’re linking to the full thing (“full”), or if you’re linking to a live stream (“nonstop”).
     *
     * @var string
     */
    protected $expression;

    /**
     * Whether this is the default media item
     *
     * @var bool
     */
    protected $isDefault;

    /**
     * The file size
     *
     * @var int
     */
    protected $fileSize;

    /**
     * The duration, in seconds
     *
     * @var int
     */
    protected $duration;

    /**
     * The bitrate
     *
     * @var int
     */
    protected $bitrate;

    /**
     * The framerate
     *
     * @var int
     */
    protected $framerate;

    /**
     * The title of the item
     *
     * @var string
     */
    protected $title;

    /**
     * The type of the title (plain or HTML)
     *
     * @var string
     */
    protected $titleType;

    /**
     * A description of the item
     *
     * @var string
     */
    protected $description;

    /**
     * The type of the description (plain or HTML)
     *
     * @var string
     */
    protected $descriptionType;

    /**
     * Keywords that describe this media
     *
     * @var array
     */
    protected $keywords = [ ];

    /**
     * The thumbnails
     *
     * @var array
     */
    protected $thumbnails = [ ];

    /**
     * The player; e.g. a page that has an embedded video player for this media item
     *
     * @var string
     */
    protected $player;

    /**
     * The hash of the binary media file.
     *
     * @var string
     */
    protected $hash;

    /**
     * The algorithm used to create the hash.
     *
     * @var string
     */
    protected $hashAlgorithm;

    /**
     * Comments on the media
     *
     * @var array
     */
    protected $comments = [ ];

    /**
     * The credits
     *
     * @var array
     */
    protected $credits = [ ];

    /**
     * The text (transcripts)
     *
     * @var array
     */
    protected $texts = [ ];

    /**
     * The restrictions
     *
     * @var array
     */
    protected $restrictions = [ ];

    /**
     * The prices
     *
     * @var array
     */
    protected $prices = [ ];

    /**
     * Convert this entity into an XML element
     *
     * @return \DOMElement
     */
    public function element( ) : \DOMElement
    {
        $media = $this->feed->getDocument( )->createElement( 'media:content' );

        $media->setAttribute( 'url', $this->url );

        if ( $this->type ) {
            $media->setAttribute( 'type', $this->type );
        }

        if ( $this->medium ) {
            $media->setAttribute( 'medium', $this->medium );
        }

        if ( $this->fileSize ) {
            $media->setAttribute( 'fileSize', $this->fileSize );
        }

        $this->addDimensionsToElement( $media );

        if ( $this->duration ) {
            $media->setAttribute( 'duration', $this->duration );
        }

        if ( $this->bitrate ) {
            $media->setAttribute( 'bitrate', $this->bitrate );
        }

        if ( $this->framerate ) {
            $media->setAttribute( 'framerate', $this->framerate );
        }

        if ( $this->expression ) {
            $media->setAttribute( 'expression', $this->expression );
        }

        if ( $this->isDefault ) {
            $media->setAttribute( 'isDefault', 'true' );
        }

        if ( $this->title ) {
            $title = $this->createElement(
                'media:title',
                $this->title,
                [ ],
                ( $this->titleType === 'html' )
            );
            if ( $this->titleType ) {
                $title->setAttribute( 'type', $this->titleType );
            }
            $media->appendChild( $title );
        }

        if ( $this->description ) {
            $description = $this->createElement(
                'media:description',
                $this->description,
                [ ],
                ( $this->descriptionType === 'html' )
            );
            if ( $this->descriptionType ) {
                $description->setAttribute( 'type', $this->descriptionType );
            }
            $media->appendChild( $description );
        }

        if ( count( $this->keywords ) ) {
            $media->appendChild(
                $this->createElement(
                    'media:keywords',
                    implode( ', ', $this->keywords )
                )
            );
        }

        if ( count( $this->thumbnails ) ) {
            foreach( $this->thumbnails as $thumbnail ) {
                $media->appendChild( $thumbnail->element( ) );
            }
        }

        if ( $this->player ) {
            $player = $this->createElement( 'media:player', null );
            $player->setAttribute( 'url', $this->player );
            $media->appendChild( $player );
        }

        if ( $this->hash ) {
            $hash = $this->createElement( 'media:hash', $this->hash );
            if ( $this->hashAlgorithm ) {
                $hash->setAttribute( 'algo', $this->hashAlgorithm );
            }
            $media->appendChild( $hash );
        }

        if ( count( $this->comments ) ) {
            $comments = $this->createElement( 'media:comments' );
            foreach( $this->comments as $comment ) {
                $comments->appendChild( $this->createElement( 'media:comment', $comment ) );
            }
            $media->appendChild( $comments );
        }

        if ( count( $this->credits ) ) {
            foreach( $this->credits as $credit ) {
                /** @var Credit $credit */
                $media->appendChild( $credit->element( ) );
            }
        }

        if ( count( $this->texts ) ) {
            foreach( $this->texts as $text ) {
                /** @var Text $text */
                $media->appendChild( $text->element( ) );
            }
        }

        if ( count( $this->restrictions ) ) {
            foreach( $this->restrictions as $restriction ) {
                /** @var Restriction $restriction */
                $media->appendChild( $restriction->element( ) );
            }
        }

        if ( count( $this->prices ) ) {
            foreach( $this->prices as $price ) {
                /** @var Price $price */
                $media->appendChild( $price->element( ) );
            }
        }

        return $media;
    }

    /**
     * Set the (MIME) type
     *
     * @param string $type
     * @return Media
     */
    public function type( string $type ) : self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the medium; e.g. image, video, audio
     *
     * @param string $medium
     * @return Media
     * @throws InvalidMediumException
     */
    public function medium( string $medium ) : self
    {
        if ( ! in_array(
            $medium,
            [
                self::IMAGE,
                self::VIDEO,
                self::AUDIO,
                self::DOCUMENT,
                self::EXECUTABLE,
            ]
        ) ) {
            throw new InvalidMediumException( 'Invalid medium' );
        }
        $this->medium = $medium;
        return $this;
    }

    /**
     * Set the expression; e.g. whether it's a sample, a full video, or non-stop
     *
     * @param string $expression
     * @return Media
     * @throws InvalidExpressionException
     */
    public function expression( string $expression ) : self
    {
        if ( ! in_array(
            $expression,
            [
                self::SAMPLE,
                self::FULL,
                self::NONSTOP,
            ]
        ) ) {
            throw new InvalidExpressionException( 'Invalid expression' );
        }
        $this->expression = $expression;
        return $this;
    }

    /**
     * Set a flag to indicate whether this is the default media item, where there are
     * multiple items.
     *
     * @param bool $isDefault
     * @return Media
     */
    public function isDefault( bool $isDefault = true ) : self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * Set the file size, in bytes
     *
     * @param int $fileSize
     * @return Media
     */
    public function fileSize( int $fileSize ) : self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * Set the duration, in seconds
     *
     * @param int $duration
     * @return Media
     */
    public function duration( int $duration ) : self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Set the bit-rate
     *
     * @param int $bitrate
     * @return Media
     */
    public function bitrate( int $bitrate ) : self
    {
        $this->bitrate = $bitrate;
        return $this;
    }

    /**
     * Set the frame rate
     *
     * @param int $framerate
     * @return Media
     */
    public function framerate( int $framerate ) : self
    {
        $this->framerate = $framerate;
        return $this;
    }

    /**
     * Set the media's title
     *
     * @param string $title
     * @param string $type
     * @return Media
     */
    public function title( string $title, $type = null ) : self
    {
        $this->title        =   $title;
        $this->titleType    =   $type;
        return $this;
    }

    /**
     * Set a description of the media
     *
     * @param string $description
     * @param string $type
     * @return Media
     */
    public function description( string $description, $type = null ) : self
    {
        $this->description = $description;
        $this->descriptionType = $type;
        return $this;
    }

    /**
     * Set the keywords
     *
     * @param string ...$keywords
     * @return Media
     */
    public function keywords( string ...$keywords ) : self
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * Add a thumbnail
     *
     * @return Thumbnail
     */
    public function addThumbnail( ) : Thumbnail
    {
        $thumbnail = new Thumbnail( $this->feed );
        $this->thumbnails[ ] = $thumbnail;
        return $thumbnail;
    }

    /**
     * Set the player
     *
     * @param string $player
     * @return Media
     */
    public function player( string $player ) : self
    {
        $this->player = $player;
        return $this;
    }

    /**
     * Set the hash of the binary media file
     *
     * @param string $hash
     * @param string $algorithm
     * @return $this
     */
    public function hash( $hash, $algorithm = null ) : self
    {
        $this->hash = $hash;
        $this->hashAlgorithm = $algorithm;
        return $this;
    }

    /**
     * Add one or more comments
     *
     * @param string ...$comments
     * @return Media
     */
    public function comments( ...$comments ) : self
    {
        foreach( $comments as $comment ) {
            $this->comments[ ] = $comment;
        }
        return $this;
    }

    /**
     * Add a credit
     *
     * @return Credit
     */
    public function addCredit( ) : Credit
    {
        $credit = new Credit( $this->feed );
        $this->credits[ ] = $credit;
        return $credit;
    }

    /**
     * Add a text (transcript)
     *
     * @return Text
     */
    public function addText( ) : Text
    {
        $text = new Text( $this->feed );
        $this->texts[ ] = $text;
        return $text;
    }

    /**
     * Add a restriction
     *
     * @return Restriction
     */
    public function addRestriction( ) : Restriction
    {
        $restriction = new Restriction( $this->feed );
        $this->restrictions[ ] = $restriction;
        return $restriction;
    }

    /**
     * Add a price
     *
     * @return Price
     */
    public function addPrice( ) : Price
    {
        $price = new Price( $this->feed );
        $this->prices[ ] = $price;
        return $price;
    }

}