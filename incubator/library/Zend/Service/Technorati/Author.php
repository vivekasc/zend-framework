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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_Author
{
    /**
     * Author first name
     *
     * @var     string
     * @access  protected
     */
    protected $_firstName;

    /**
     * Author last name
     *
     * @var     string
     * @access  protected
     */
    protected $_lastName;
    
    /**
     * Technorati account username
     *
     * @var     string
     * @access  protected
     */
    protected $_username;
    
    /**
     * Technorati account description
     *
     * @var     string
     * @access  protected
     */
    protected $_description;

    /**
     * Technorati account biography
     *
     * @var     string
     * @access  protected
     */
    protected $_bio;

    /**
     * Technorati account thumbnail picture URL, if any
     *
     * @var     null|Zend_Uri_Http
     * @access  protected
     */
    protected $_thumbnailPicture;


    /**
     * Construct a Key object
     * 
     * Parses given Author element from from $dom.
     *
     * @param   DomElement $dom The ReST fragment for this author object
     * @return  void
     * 
     * @todo    Check which elements are optional
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);

    	/**
    	 * @todo   Create accessor method
    	 */
    	
        $result = $xpath->query('./firstname/text()', $dom);
        if ($result->length == 1) $this->setFirstName($result->item(0)->data);
        
        $result = $xpath->query('./lastname/text()', $dom);
        if ($result->length == 1) $this->setLastName($result->item(0)->data);
        
        $result = $xpath->query('./username/text()', $dom);
        if ($result->length == 1) $this->setUsername($result->item(0)->data);
        
        $result = $xpath->query('./description/text()', $dom);
        if ($result->length == 1) $this->setDescription($result->item(0)->data);
        
        $result = $xpath->query('./bio/text()', $dom);
        if ($result->length == 1) $this->setBio($result->item(0)->data);

        
        /* 
         * The following elements need more attention 
         */

        $result = $xpath->query('./thumbnailpicture/text()', $dom);
        if ($result->length == 1) $this->setThumbnailPicture($result->item(0)->data);
    }
    

    /**
     * Return Author first name
     * 
     * @return  String  Author first name
     */
    public function getFirstName() {
        return $this->_firstName;
    }
    
    /**
     * Return Author last name
     * 
     * @return  String  Author last name
     */
    public function getLastName() {
        return $this->_lastName;
    }
    
    /**
     * Return Technorati account username
     * 
     * @return  String  Technorati account username
     */
    public function getUsername() {
        return $this->_username;
    }
    
    /**
     * Return Technorati account description
     * 
     * @return  String  Technorati account description
     */
    public function getDescription() {
        return $this->_description;
    }
    
    /**
     * Return Technorati account biography
     * 
     * @return  String  Technorati account biography
     */
    public function getBio() {
        return $this->_bio;
    }
        
    /**
     * Return Technorati account thumbnail picture
     * 
     * @return  null|Zend_Uri_Http  Technorati account thumbnail picture
     */
    public function getThumbnailPicture() {
        return $this->_thumbnailPicture;
    }
    
    
    /**
     * Set author first name
     * 
     * @param   String First Name input value 
     * @return  void
     */
    public function setFirstName($input) {
        $this->_firstName = (string) $input;
    }
    
    /**
     * Set author last name
     * 
     * @param   String  Last Name input value 
     * @return  void
     */
    public function setLastName($input) {
        $this->_lastName = (string) $input;
    }
    
    /**
     * Set Technorati account username
     * 
     * @param   String  Username input value 
     * @return  void
     */
    public function setUsername($input) {
        $this->_username = (string) $input;
    }

    /**
     * Set Technorati account biography
     * 
     * @param   String  Biography input value
     * @return  void
     */
    public function setBio($input) {
        $this->_bio = (string) $input;
    }
        
    /**
     * Set Technorati account description
     * 
     * @param   String  Description input value
     * @return  void
     */
    public function setDescription($input) {
        $this->_description = (string) $input;
    }
        
    /**
     * Set Technorati account thumbnail picture
     * 
     * @param   String|Zend_Uri_Http Thumbnail Picture URI
     * @return  void
     */
    public function setThumbnailPicture($input) {
        /**
         * @see Zend_Uri
         */
        require_once 'Zend/Uri.php';
              
    	if ($input instanceof Zend_Uri_Http || $input === null) {
    		$uri = $input;
    	}
    	else {
    		try {
    			$uri = Zend_Uri::factory((string) $input);
    		}
    		catch (Exception $e) {
    			throw new Zend_Service_Technorati_Exception($e);
    		}
    	}
    	
        $this->_thumbnailPicture = $uri;
    }
    
}
