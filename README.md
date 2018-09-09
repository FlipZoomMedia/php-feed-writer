# Feed Writer

A PHP library for writing feeds. Currently supports RSS 2.0 and iTunes.

> **Important** &mdash; This is a work in progress

## Examples

### RSS


```php
$feed = new RSS2( );

$channel = $feed->addChannel( );

$channel
	->title( 'My Blog' )
	->description( 'My personal blog' )
	->link( 'https://example.com' )
	->lastBuildDate( new \DateTime( ) )
	->pubDate( new \DateTime( ) )
	->language( 'en-US' );

foreach( $posts as $post ) {
	$channel->addItem( )
		->title( $post->title )
		->description( $post->description )
		->link( $post->url )
		->pubDate( $post->publishedAt )
		->guid( $post->url, true );
}
```

### iTunes

```php
$feed = new Itunes( );

$channel = $feed->addChannel( );

$channel->title( 'All About Everything' )
    ->subtitle( 'A show about everything' )
    ->description( 'All About Everything is a show about everything. Each week we dive into any subject known to man and talk about it as much as we can. Look for our podcast in the Podcasts app or in the iTunes Store' )
    ->summary( 'All About Everything is a show about everything. Each week we dive into any subject known to man and talk about it as much as we can. Look for our podcast in the Podcasts app or in the iTunes Store' )
    ->link( 'http://www.example.com/podcasts/everything/index.html' )
    ->image( 'http://example.com/podcasts/everything/AllAboutEverything.jpg' )
    ->author( 'John Doe' )
    ->owner( 'John Doe', 'john.doe@example.com' )
    ->explicit( 'no' )
    ->copyright( '&#x2117; &amp; &#xA9; 2014 John Doe &amp; Family' )
    ->generator( 'Feed Writer' )
    ->ttl( 60 )
    ->lastBuildDate( new \DateTime( '2016-03-10 02:00' ) );

$channel->addItem( )
    ->title( 'Shake Shake Shake Your Spices' )
    ->author( 'John Doe' )
    ->subtitle( 'A short primer on table spices' )
    ->duration( '07:04' )
    ->summary( 'This week we talk about <a href="https://itunes/apple.com/us/book/antique-trader-salt-pepper/id429691295?mt=11">salt and pepper shakers</a>, comparing and contrasting pour rates, construction materials, and overall aesthetics. Come and join the party!' )
    ->pubDate( new \DateTime( '2016-03-08 12:00' ) )
    ->guid( 'http://example.com/podcasts/archive/aae20140615.m4a' )
    ->explicit( 'no' )
    ->addEnclosure( )
        ->url( 'http://example.com/podcasts/everything/AllAboutEverythingEpisode3.m4a' )
        ->length( 8727310 )
        ->type( 'audio/x-m4a' );
```

## Features

* Lightning Fast
* Modern &mdash; uses PHP7
* Flexible; use it for syndication, media, podcasts...
* Easy to extend
* Supports custom namespaces
* Full MediaRSS support
* Supports XSL stylesheets
* No third-party dependencies
* Fully tested

## Installation

This package requires PHP 7+.

Install the packae using [Composer](https://getcomposer.org/):

```
composer require lukaswhite\php-feed-writer
```

## Manual

### Creating a Feed

To create a feed:

```php
use Lukaswhite\FeedWriter\Feed;

$feed = new Feed( );
```

This creates a feed with the `utf-8` character encoding; to override that:

```php
$feed = new Feed( 'iso-8859-1' );
```

#### Feed Namespaces

By default, the `content` namespace is set (`http://purl.org/rss/1.0/modules/content/`).

To register a new namespace:

```php
$feed->registerNamespce( 'dc', 'http://purl.org/dc/elements/1.1/' );
```

For convenience, the following common namespaces have their own corresponding methods:

```php
$feed->registerAtomNamespace( );
$feed->registerMediaNamespace( );
$feed->registerDublinCoreNamespace( );
```

To check whether a namespace is registered:

```php
$registered = $feed->isNamespaceRegistered( 'name' );
```

To unregister a namespace &mdash; for example, if for whatever reason you don't want the `content` namespace set &mdash; simply do this:

```php
$channel->unregisterNamespace( 'content' );
```

Note that the Atom and Media namespaces are automatically added by this class when you add an atom link or some media respectively.

### Creating a Channel

A call to `addChanel` creates a new channel, adds it to the feed and returns it.

```php
$channel = $feed->addChannel( );
```

From there, the `Channel` class provides a number of methods for settings its attributes using a fluent interface.

#### Setting a Channel's Title

```php
$channel->title( 'My Blog' );
```

#### Setting a Channel's Description

```php
$channel->description( 'My personal blog' );
```

#### Setting a Channel's Link

```php
$channel->link( 'https://example.com' );
```

#### Setting a Channel's Language

```php
$channel->language( 'en-GB' );
```

#### Setting a Channel's Copyright Notice

```php
$channel->copyright( 'Copyright 2018 Me' );
```

#### Setting a Channel's Last Build Date


```php
$channel->lastBuildDate( new \DateTime( ) );
```

> If you use the excellent [Carbon](https://carbon.nesbot.com/docs/) library then, since it's an extension of `\DateTime`, you can simply pass an instance of that. It's not a required package, however.

#### Setting a Channel's Published Date

```php
$channel->pubDate( new \DateTime( ) );
```

> If you use the excellent [Carbon](https://carbon.nesbot.com/docs/) library then, since it's an extension of `\DateTime`, you can simply pass an instance of that. It's not a required package, however.

#### Setting a Channel's Categories

There are two ways to set a channel's categories. You can set multiple categories like this:

```php
$channel->categories( 'PHP', 'development', 'programming' );
```

The limitation of that, though, is that you cannot set the domain attribute; if you need to do that then simply use the `addCategory` method and pass the domain as the second argument:

```php
$channel->addCategory( 'PHP', 'the domain' );
```

The second argument is optional, so the previous example can be re-written as follows:

```php
$channel
	->addCategory( 'PHP' )
	->addCategory( 'development' )
	->addCategory( 'programming' );
```

#### Adding Links

You can add a link to the feed like this:

```php
$channel->addLink(
	'atom:link',
	'https://example.com/feed.xml',
	'self', // the rel attribute
	'application/atom+xml'
);
```

For convenience, you can add an Atom link as follows:

```php
$channel->addAtomLink( 'https://example.com/feed.xml' );
```

This will automatically register the appropriate namespace for you.

#### PubSubHubbub

To add a PubSubHubbub link:

```php
$channel->addPubSubHubbubLink( 'http://pubsubhubbub.appspot.com' );
```

If you need more flexibility, just use `addLink` directly. For example the line above does this:

```php
$this->addLink(
	'atom:link',
	'http://pubsubhubbub.appspot.com',
	'hub'
);	
```

#### Other Channel Properties

```php
$channel->generator( 'PHP Feed Writer' )
	->ttl( 60 );
```

### Adding Items

Now that your feed has a channel, it's time to start adding items.

To add an item to the channel, call `addItem()`. This creates a new instance, attaches it to the feed and returns it. Like channels, the `Item` class has a fluent interface that you can use to set the appropriate properties.



> End of documentation for now; it's a work-in-progress.