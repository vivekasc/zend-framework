<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Simplify AJAX context switching based on requested format
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Suppress exit when sendJson() called
     * @var bool
     */
    public $suppressExit = false;

    /**
     * Create JSON response
     *
     * Encodes and returns data to JSON. Content-Type header set to 
     * 'application/json', and layouts disabled (if being used).
     *
     * @param  mixed $data
     * @param  bool  $keepLayouts
     * @return string
     */
    public function encodeJson($data, $keepLayouts = false)
    {
        require_once 'Zend/Json.php';
        $data = Zend_Json::encode($data);

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        if (!$keepLayouts) {
            require_once 'Zend/Layout.php';
            $layout = Zend_Layout::getMvcInstance();
            if ($layout instanceof Zend_Layout) {
                $layout->disableLayout();
            }
        }

        return $data;
    }

    /**
     * Encode JSON response and immediately send
     * 
     * @param  mixed $data 
     * @param  bool $keepLayouts 
     * @return void
     */
    public function sendJson($data, $keepLayouts = false)
    {
        $data = $this->encodeJson($data, $keepLayouts);
        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }

    /**
     * Strategy pattern: call helper as helper broker method
     *
     * Allows encoding JSON. If $sendNow is true, immediately sends JSON 
     * response. 
     * 
     * @param  mixed $data 
     * @param  bool $sendNow 
     * @param  bool $keepLayouts 
     * @return string|void
     */
    public function direct($data, $sendNow = false, $keepLayouts = false)
    {
        if ($sendNow) {
            return $this->sendJson($data, $keepLayouts);
        }
        return $this->encodeJson($data, $keepLayouts);
    }
}