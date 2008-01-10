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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Form
 * 
 * @category   Aend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form implements Iterator
{
    public function __construct($options = null)
    {
    }

    public function setOptions(array $options)
    {
    }

    public function setConfig(Zend_Config $config)
    {
    }

 
    // Loaders 
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader, $type)
    {
    }

    public function getPluginLoader($type)
    {
    }

    public function addPrefixPath($prefix, $path, $type = null) 
    {
    }


    // Form metadata:
    public function addAttrib($key, $value)
    {
    }

    public function addAttribs(array $attribs)
    {
    }

    public function setAttribs(array $attribs)
    {
    }

    public function getAttrib($key)
    {
    }

    public function getAttribs()
    {
    }

    public function removeAttrib($key)
    {
    }

    public function clearAttribs()
    {
    }

 
    // Element interaction: 
    public function addElement($element, $name = null)
    {
    }

    public function addElements(array $elements)
    {
    }

    public function setElements(array $elements)
    {
    }

    public function getElement($name)
    {
    }

    public function getElements()
    {
    }

    public function removeElement($name)
    {
    }

    public function setDefaults(array $defaults)
    {
    }

    public function setDefault($name, $value)
    {
    }

    public function getValue($name)
    {
    }

    public function getValues()
    {
    }

    public function getUnfilteredValue($name)
    {
    }

    public function getUnfilteredValues()
    {
    }

    public function __get($name)
    {
    }

 
    // Element groups: 
    public function addGroup(Zend_Form $form, $name, $order = null)
    {
    }

    public function addGroups(array $groups)
    {
    }

    public function setGroups(array $groups)
    {
    }

    public function getGroup($name)
    {
    }

    public function getGroups()
    {
    }

    public function removeGroup($name)
    {
    }

    public function clearGroups()
    {
    }


    // Display groups:
    public function addDisplayGroup(array $elements, $name, $order = null)
    {
    }

    public function addDisplayGroups(array $groups)
    {
    }

    public function setDisplayGroups(array $groups)
    {
    }

    public function getDisplayGroup($name)
    {
    }

    public function getDisplayGroups()
    {
    }

    public function removeDisplayGroup($name)
    {
    }

    public function clearDisplayGroups()
    {
    }

     
    // Processing 

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     * 
     * @param  array $values 
     * @return Zend_Form
     */
    public function populate(array $values)
    {
    }

    public function isValid(array $data)
    {
    }

    public function isValidPartial(array $data)
    {
    }

    public function processAjax($request)
    {
    }

    public function persistData()
    {
    }

    public function getErrors($name = null)
    {
    }

    public function getMessages($name = null)
    {
    }

 
    // Rendering 
    public function setView(Zend_View_Interface $view)
    {
    }

    public function getView()
    {
    }

    public function addDecorator($decorator, $options = array())
    {
    }

    public function addDecorators(array $decorator)
    {
    }

    public function setDecorators(array $decorator)
    {
    }

    public function getDecorator($name)
    {
    }

    public function getDecorators()
    {
    }

    public function removeDecorator($name)
    {
    }

    public function clearDecorators()
    {
    }

    public function render(Zend_View_Interface $view = null)
    {
    }

    public function __toString()
    {
    }

 
    // Localization: 
    public function setTranslator(Zend_Translate_Adapter $translator)
    {
    }

    public function getTranslator()
    {
    }

 
    // For iteration, countable: 
    public function current()
    {
    }

    public function key()
    {
    }

    public function next()
    {
    }

    public function rewind()
    {
    }

    public function valid()
    {
    }

    public function count()
    {
    }
}