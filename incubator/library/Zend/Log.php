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
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Log_Exception */
require_once 'Zend/Log/Exception.php';

/** Zend_Log_Filter_Priority */
require_once 'Zend/Log/Filter/Priority.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */ 
class Zend_Log
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

    /**
     * @var array of priorities where the keys are the
     * priority numbers and the values are the priority names
     */
    private $_priorities = array();

    /**
     * @var array of Zend_Log_Writer_Abstract
     */
    private $_writers = array();

    /**
     * @var array of Zend_Log_Filter_Interface
     */
    private $_filters = array();

    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null  $writer  default writer
     */
    public function __construct($writer = null)
    {
        $r = new ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());

        if ($writer !== null) {
            $this->addWriter($writer);
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     *   $log->priorityName('message')
     *     instead of
     *   $log->log('message', Zend_Log::PRIORITY_NAME)
     *
     * @param  string  $method  priority name
     * @param  string  $params  message to log
     * @return void
     * @throws InvalidArgumentException
     */
    public function __call($method, $params)
    {
        $priority = strtoupper($method);
        if (($priority = array_search($priority, $this->_priorities)) !== false) {
            $this->log(array_shift($params), $priority);
        } else {
            throw new InvalidArgumentException('Bad log priority');
        }
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return void
     * @throws InvalidArgumentException
     */
    public function log($message, $priority)
    {
        foreach ($this->_filters as $filter) {
            if (!$filter->accept($message, $priority)) {
                return;
            }
        }

        if (empty($this->_writers)) {
            throw new Zend_Log_Exception('No writers were added');
        }

        if (! isset($this->_priorities[$priority])) {
            throw new InvalidArgumentException('Bad log priority');
        }

        foreach ($this->_writers as $writer) {
            $writer->log($message, $priority);
        }
    }

    /**
     * Add a custom priority
     *
     * @param  string   $name      Name of priority
     * @param  integer  $priority  Numeric priority
     * @throws InvalidArgumentException
     */
    public function addPriority($name, $priority)
    {
        // Priority names must be uppercase for predictability.
        $name = strtoupper($name);

        if (isset($this->_priorities[$priority])
            || array_search($name, $this->_priorities)) {
            throw new InvalidArgumentException('Existing priorities cannot be overwritten');
        }

        $this->_priorities[$priority] = $name;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any of the writers, it
     * must be accepted by all filters added with this method.
     * 
     * @param  Zend_Log_Filter_Interface  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new Zend_Log_Filter_Priority($filter);
        }

        $this->_filters[] = $filter;
    }

    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  Zend_Log_Writer_Abstract $writer
     * @return void
     */
    public function addWriter($writer)
    {
        $this->_writers[] = $writer;
    }

}