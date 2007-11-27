<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Patch for default timezone in PHP >= 5.1.0
 */
if (!ini_get('date.timezone')) date_default_timezone_set(@date_default_timezone_get());

/**
 * @see Zend_Service_Technorati
 */
require_once 'Zend/Service/Technorati.php';

/**
 * @see Zend_Service_Technorati_Author
 */
require_once 'Zend/Service/Technorati/Author.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_AuthorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->xmlAuthor = dirname(__FILE__) . '/_files/TestAuthor.xml';
    }
    
    public function testConstructValid()
    {
        $dom = new DOMDocument();
        $dom->load($this->xmlAuthor);
        
        try {
            $weblog = new Zend_Service_Technorati_Author($dom->documentElement);
            $this->assertType('Zend_Service_Technorati_Author', $weblog);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }
    
    public function testConstructExceptionDomInvalid() 
    {
        try {
            $weblog = new Zend_Service_Technorati_Author('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMElement", $e->getMessage());
        }
    }
    
    public function testSetterGetter()
    {
        $dom = new DOMDocument();
        $dom->load($this->xmlAuthor);
        $author = new Zend_Service_Technorati_Author($dom->documentElement);
        
        // check valid object
        $this->assertNotNull($author);
        
        /**
         * check first name
         */
        
        $set = 'first';
        $get = $author->setFirstName($set)->getFirstName();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
        
        /**
         * check last name
         */
        
        $set = 'last';
        $get = $author->setLastName($set)->getLastName();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
        
        /**
         * check username
         */
        
        $set = 'user';
        $get = $author->setUsername($set)->getUsername();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
        
        /**
         * check description
         */
        
        $set = 'desc';
        $get = $author->setUsername($set)->getUsername();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
                
        /**
         * check bio
         */
        
        $set = 'biography';
        $get = $author->setBio($set)->getBio();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
        
        /**
         * check thubmnail picture
         */
        
        $set = Zend_Uri::factory('http://www.simonecarletti.com/');
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals($set, $get);
        
        $set = 'http://www.simonecarletti.com/';
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals(Zend_Uri::factory($set), $get);
        
        $set = 'http:::/foo';
        try {
            $author->setThumbnailPicture($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid URI", $e->getMessage());
        }
    }
    
    public function testAuthor()
    {
        $dom = new DOMDocument();
        $dom->load($this->xmlAuthor);
        $author = new Zend_Service_Technorati_Author($dom->documentElement);
        
        // check valid object
        $this->assertNotNull($author);
        // check first name
        $this->assertEquals('Cesare', $author->getFirstName());
        // check last name
        $this->assertEquals('Lamanna', $author->getLastName());
        // check username
        $this->assertEquals('cesarehtml', $author->getUsername());
        // check description
        $this->assertEquals('', $author->getDescription());
        // check bio
        $this->assertEquals('', $author->getBio());
        
        // check thumbnailpicture
        $this->assertEquals(Zend_Uri::factory('http://static.technorati.com/progimages/photo.jpg?uid=117217'), $author->getThumbnailPicture());
    }
}