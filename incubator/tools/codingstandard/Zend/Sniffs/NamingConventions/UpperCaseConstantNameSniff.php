<?php
/**
 * Zend Framework Coding Standard
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
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Zend_Sniffs_NamingConventions_UpperCaseConstantNameSniff
 *
 * Ensures that constant names are all uppercase.
 *
 * @category   Zend
 * @package    Zend_CodingStandard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Sniffs_NamingConventions_UpperCaseConstantNameSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);
    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $constName = $tokens[$stackPtr]['content'];

        // If this token is in a heredoc, ignore it.
        if ($phpcsFile->hasCondition($stackPtr, T_START_HEREDOC) === true) {
            return;
        }

        // If the next non-whitespace token after this token
        // is not an opening parenthesis then it is not a function call.
        $openBracket = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            $functionKeyword = $phpcsFile->findPrevious(array(T_WHITESPACE, T_COMMA, T_COMMENT, T_STRING), ($stackPtr - 1), null, true);

            $declarations = array(
                             T_FUNCTION,
                             T_CLASS,
                             T_INTERFACE,
                             T_IMPLEMENTS,
                             T_EXTENDS,
                             T_INSTANCEOF,
                             T_NEW,
                            );
            if (in_array($tokens[$functionKeyword]['code'], $declarations) === true) {
                // This is just a declaration; no constants here.
                return;
            }

            if ($tokens[$functionKeyword]['code'] === T_CONST) {
                // This is a class constant.
                if (strtoupper($constName) !== $constName) {
                    $error = 'Class constants must be uppercase; expected '.strtoupper($constName)." but found $constName";
                    $phpcsFile->addError($error, $stackPtr);
                }

                return;
            }

            // Is this a class name?
            $nextPtr = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
            if ($tokens[$nextPtr]['code'] === T_DOUBLE_COLON) {
                return;
            }

            // Is this a type hint?
            if ($tokens[$nextPtr]['code'] === T_VARIABLE) {
                return;
            } else if ($phpcsFile->isReference($nextPtr) === true) {
                return;
            }

            // Is this a member var name?
            $prevPtr = $phpcsFile->findPrevious(array(T_WHITESPACE), ($stackPtr - 1), null, true);
            if ($tokens[$prevPtr]['code'] === T_OBJECT_OPERATOR) {
                return;
            }

            // This is a real constant.
            if (strtoupper($constName) !== $constName) {
                $error = 'Constants must be uppercase; expected '.strtoupper($constName)." but found $constName";
                $phpcsFile->addError($error, $stackPtr);
            }

        } else if (strtolower($constName) === 'define' || strtolower($constName) === 'constant') {

            /*
                This may be a "define" or "constant" function call.
            */

            // The next non-whitespace token must be the constant name.
            $constPtr = $phpcsFile->findNext(array(T_WHITESPACE), ($openBracket + 1), null, true);
            if ($tokens[$constPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING) {
                return;
            }

            $constName = $tokens[$constPtr]['content'];
            if (strtoupper($constName) !== $constName) {
                $error = 'Constants must be uppercase; expected '.strtoupper($constName)." but found $constName";
                $phpcsFile->addError($error, $stackPtr);
            }
        }//end if

    }//end process()


}//end class