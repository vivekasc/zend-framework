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
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Locale */
require_once 'Zend/Locale.php';

/** Zend_Translate_Exception */
require_once 'Zend/Translate/Exception.php';

/** Zend_Translate_Adapter */
require_once 'Zend/Translate/Adapter.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate_Adapter_Tbx extends Zend_Translate_Adapter {
    // Internal variables
    private $_file        = false;
    private $_cleared     = array();
    private $_langset     = null;
    private $_term        = null;
    private $_content     = null;
    private $_defined     = false;

    /**
     * Generates the tbx adapter
     * This adapter reads with php's xml_parser
     *
     * @param  string              $data     Translation data
     * @param  string|Zend_Locale  $locale   OPTIONAL Locale/Language to set, identical with locale identifier,
     *                                       see Zend_Locale for more information
     */
    public function __construct($data, $locale = null)
    {
        parent::__construct($data, $locale);
    }


    /**
     * Load translation data (TBX file reader)
     *
     * @param  string  $filename  TBX file to add, full path must be given for access
     * @param  string  $locale    Locale has no effect for TBX because TBX defines all languages within
     *                            the source file
     * @param  array   $option    OPTIONAL Options to use
     * @throws Zend_Translation_Exception
     */
    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $options = array_merge($this->_options, $options);

        if ($options['clear']) {
            $this->_translate = array();
        }

        if ((in_array('defined_language', $options)) and !empty($options['defined_language'])) {
            $this->_defined = true;
        }

        if (!is_readable($filename)) {
            throw new Zend_Translate_Exception('Translation file \'' . $filename . '\' is not readable.');
        }

        $this->_file = xml_parser_create();
        xml_set_object($this->_file, $this);
        xml_parser_set_option($this->_file, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->_file, "_startElement", "_endElement");
        xml_set_character_data_handler($this->_file, "_contentElement");

        if (!xml_parse($this->_file, file_get_contents($filename))) {
            throw new Zend_Translate_Exception(sprintf('XML error: %s at line %d',
                      xml_error_string(xml_get_error_code($this->_file)),
                      xml_get_current_line_number($this->_file)));
            xml_parser_free($this->_file);
        }

        if ($this->_defined !== true) {
            foreach ($this->_translate as $key => $value) {
                if (!in_array($key, $this->_languages)) {
                    $this->_languages[$key] = $key;
                }
            }
        }
    }

    private function _startElement($file, $name, $attrib)
    {
        switch(strtolower($name)) {
            case 'langset':
                $this->_term = null;
                if (array_key_exists('xml:lang', $attrib)) {
                    $this->_langset = $attrib['xml:lang'];
                    if (!array_key_exists($this->_langset, $this->_translate)) {
                        $this->_translate[$this->_langset] = array();
                    }
                    if (!array_key_exists($this->_langset, $this->_languages) and ($this->_defined === true)) {
                        $this->_languages[$this->_langset] = $this->_langset;
                    }
                }
                break;
            case 'term':
                $this->_content = null;
                break;
            default:
                break;
        }
    }

    private function _endElement($file, $name)
    {
        switch (strtolower($name)) {
            case 'langset':
                $this->_langset = null;
                break;
            case 'term':
                if (empty($this->_term)) {
                    $this->_term = $this->_content;
                }
                if (!empty($this->_content) or !array_key_exists($this->_term, $this->_translate[$this->_langset])) {
                    $this->_translate[$this->_langset][$this->_term] = $this->_content;
                }
                break;
            default:
                break;
        }
    }

    private function _contentElement($file, $data)
    {
        if ($this->_langset !== null) {
            $this->_content .= $data;
        }
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return "Tbx";
    }
}