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
class Zend_Service_Technorati_KeyInfoResult
{
    /**
     * Technorati API key
     *
     * @var     string
     * @access  protected
     */
    protected $_apiKey;

    /**
     * Number of queries used today
     *
     * @var     int
     * @access  protected
     */
    protected $_apiQueries;

    /**
     * Total number of available queries per day
     *
     * @var     int
     * @access  protected
     */
    protected $_maxQueries;


    /*
     * Technorati API response document
     *
     * @var     DomDocument
     * @access  protected
     * /
    protected $_dom; */

    /*
     * Object for $this->_dom
     *
     * @var     DOMXpath
     * @access  protected
     * /
    protected $_xpath; */


    /**
     * Construct a KeyInfoResult object
     * 
     * Parses given Key element from $dom and sets API key string.
     *
     * @param   DomElement $dom The ReST fragment for this Key object
     * @param   String $apiKey  The API Key string
     * @return  void
     */
    public function __construct(DomDocument $dom, $apiKey = null)
    {
        // $this->_dom   = $dom;
        // $this->_xpath = new DOMXPath($dom);
        $xpath = new DOMXPath($dom);
        
        /** @todo improve xpath expression */
        $this->_apiQueries   = (int) $xpath->query('//apiqueries/text()')->item(0)->data;
        $this->_maxQueries   = (int) $xpath->query('//maxqueries/text()')->item(0)->data;
        $this->setApiKey($apiKey);
    }
    
    
    /**
     * Return API Key string
     * 
     * @return  String  API Key string
     */
    public function getApiKey() {
        return $this->_apiKey;
    }
    
    /**
     * Return the number of queries sent today
     * 
     * @return  Int     Number of queries sent today
     */
    public function getApiQueries() {
        return $this->_apiQueries;
    }
    
    /**
     * Return Key's daily query limit
     * 
     * @return  Int     Maximum number of available queries per day
     */
    public function getMaxQueries() {
        return $this->_maxQueries;
    }
    
    
    /**
     * Set API Key string
     * 
     * @param   String $apiKey  The API Key string
     * @return  void
     */
    public function setApiKey($apiKey) {
        $this->_apiKey = $apiKey;
    }

    
    /*
     * Return the response XML document
     *
     * @return string   The response document converted into XML format
     * /
    function getXML()
    {
        return $this->_dom->saveXML($this->_dom);
    } */
}