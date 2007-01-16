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
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Exception
 */
require_once 'Zend/Exception.php';

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_TimeSync_Exception extends Zend_Exception
{
    protected $_exceptions;
    protected $_current;

    public function addException(Zend_TimeSync_Exception $exception)
    {
        $this->_exceptions[] = $exception;
    }

    public function get()
    {
        return $this->_exceptions;
    }
}
