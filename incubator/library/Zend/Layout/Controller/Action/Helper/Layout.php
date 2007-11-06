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
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Helper for interacting with Zend_Layout objects
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout_Controller_Action_Helper_Layout extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Layout
     */
    protected $_layout;

    /**
     * Constructor
     * 
     * @param  Zend_Layout $layout 
     * @return void
     */
    public function __construct(Zend_Layout $layout = null)
    {
        if (null !== $layout) {
            $this->setLayout($layout);
        }
    }

    /**
     * Get layout object
     * 
     * @return Zend_Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            require_once 'Zend/Controller/Front.php';
            $front = Zend_Controller_Front::getInstance();
            if ($front->hasPlugin('Zend_Layout_Controller_Plugin_Layout')) {
                $plugin = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');
                $this->_layout = $plugin->getLayout();
            } else {
                // Implicitly sets layout object
                require_once 'Zend/Layout.php';
                $layout = new Zend_Layout();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     * 
     * @param  Zend_Layout $layout 
     * @return Zend_Layout_Controller_Action_Helper_Layout
     */
    public function setLayout(Zend_Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Strategy pattern; call object as method
     *
     * Returns layout object
     * 
     * @return Zend_Layout
     */
    public function direct()
    {
        return $this->getLayout();
    }
}