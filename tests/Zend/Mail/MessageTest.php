<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MessageTest extends PHPUnit_Framework_TestCase
{
    protected $_file;

    public function setUp()
    {
        $this->_file = dirname(__FILE__) . '/_files/mail.txt';
    }

    public function testInvalidFile()
    {
        try {
            $message = new Zend_Mail_Message(array('file' => '/this/file/does/not/exists'));
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while loading unknown file');
    }

    public function testIsMultipart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertTrue($message->isMultipart());
    }

    public function testGetHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetDecodedHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->from, iconv('UTF-8', iconv_get_encoding('internal_encoding'),
                                                                   '"Peter Müller" <peter-mueller@example.com>'));
    }

    public function testGetHeaderAsArray()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->getHeader('subject', 'array'), array('multipart'));
    }

    public function testGetHeaderFromOpenFile()
    {
        $fh = fopen($this->_file, 'r');
        $message = new Zend_Mail_Message(array('file' => $fh));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetFirstPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }

    public function testGetFirstPartTwice()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $message->getPart(1);
        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }


    public function testGetWrongPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        try {
            $message->getPart(-1);
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while fetching unknown part');
    }

    public function testNoHeaderMessage()
    {
        $message = new Zend_Mail_Message(array('file' => __FILE__));

        $this->assertEquals(substr($message->getContent(), 0, 5), '<?php');

        $raw = file_get_contents(__FILE__);
        $raw = "\t" . $raw;
        $message = new Zend_Mail_Message(array('raw' => $raw));

        $this->assertEquals(substr($message->getContent(), 0, 6), "\t<?php");
    }

    public function testMultipleHeader()
    {
        $raw = file_get_contents($this->_file);
        $raw = "sUBject: test\nSubJect: test2\n" . $raw;
        $message = new Zend_Mail_Message(array('raw' => $raw));

        $this->assertEquals($message->getHeader('subject', 'string'),  "test\r\ntest2\r\nmultipart");
        $this->assertEquals($message->getHeader('subject'),  array('test', 'test2', 'multipart'));
    }

    public function testContentTypeDecode()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals(Zend_Mime_Decode::splitContentType($message->ContentType),
                            array('type' => 'multipart/alternative', 'boundary' => 'crazy-multipart'));
    }

    public function testSplitEmptyMessage()
    {
        $this->assertEquals(Zend_Mime_Decode::splitMessageStruct('', 'xxx'), null);
    }

    public function testSplitInvalidMessage()
    {
        try {
            Zend_Mime_Decode::splitMessageStruct("--xxx\n", 'xxx');
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while decoding invalid message');
    }

    public function testInvalidMailHandler()
    {
        try {
            $message = new Zend_Mail_Message(array('handler' => 1));
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while using invalid mail handler');

    }

    public function testMissingId()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => dirname(__FILE__) . '/_files/test.mbox/INBOX'));

        try {
            $message = new Zend_Mail_Message(array('handler' => $mail));
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while mail handler without id');

    }

    public function testIterator()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));
        foreach (new RecursiveIteratorIterator($message) as $num => $part) {
            if ($num == 1) {
                // explicit call of __toString() needed for PHP < 5.2
                $this->assertEquals(substr($part->__toString(), 0, 14), 'The first part');
            }
        }
        $this->assertEquals($part->contentType, 'text/x-vertical');
    }

    public function testDecodeString()
    {
        $is = Zend_Mime_Decode::decodeQuotedPrintable('=?UTF-8?Q?"Peter M=C3=BCller"?= <peter-mueller@example.com>');
        $should = iconv('UTF-8', iconv_get_encoding('internal_encoding'),
                        '"Peter Müller" <peter-mueller@example.com>');
        $this->assertEquals($is, $should);
    }

    public function testSplitHeader()
    {
        $header = 'foo; x=y; y="x"';
        $this->assertEquals(Zend_Mime_Decode::splitHeaderField($header), array('foo', 'x' => 'y', 'y' => 'x'));
        $this->assertEquals(Zend_Mime_Decode::splitHeaderField($header, 'x'), 'y');
        $this->assertEquals(Zend_Mime_Decode::splitHeaderField($header, 'y'), 'x');
        $this->assertEquals(Zend_Mime_Decode::splitHeaderField($header, 'foo', 'foo'), 'foo');
        $this->assertEquals(Zend_Mime_Decode::splitHeaderField($header, 'foo'), null);
    }

    public function testSplitInvalidHeader()
    {
        $header = '';
        try {
            Zend_Mime_Decode::splitHeaderField($header);
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while decoding invalid header field');
    }

    public function testSplitMessage()
    {
        $header = 'Test: test';
        $body   = 'body';
        $newlines = array("\r\n", "\n\r", "\n", "\r");

        foreach ($newlines as $contentEOL) {
            foreach ($newlines as $decodeEOL) {
                $content = $header . $contentEOL . $contentEOL . $body;
                $decoded = Zend_Mime_Decode::splitMessage($content, $decoded_header, $decoded_body, $decodeEOL);
                $this->assertEquals(array('test' => 'test'), $decoded_header);
                $this->assertEquals($body, $decoded_body);
            }
        }
    }

    public function testToplines()
    {
        $message = new Zend_Mail_Message(array('headers' => file_get_contents($this->_file)));
        $this->assertTrue(strpos($message->getToplines(), 'multipart message') === 0);
    }

    public function testNoContent()
    {
        $message = new Zend_Mail_Message(array('raw' => 'Subject: test'));

        try {
            $message->getContent();
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while getting content of message without body');
    }

    public function testEmptyMessage()
    {
        $message = new Zend_Mail_Message(array());
        $this->assertEquals(array(), $message->getHeaders());

        $message = new Zend_Mail_Message(array());
        $subject = null;
        try {
            $subject = $message->subject;
        } catch (Zend_Exception $e) {
            // ok
        }
        if ($subject) {
            $this->fail('no exception raised while getting header from empty message');
        }

        $message = new Zend_Mail_Message(array());
        $part = null;
        try {
            $part = $message->getPart(1);
        } catch (Zend_Exception $e) {
            // ok
        }
        if ($part) {
            $this->fail('no exception raised while getting part from empty message');
        }

        $message = new Zend_Mail_Message(array());
        $this->assertTrue($message->countParts() == 0);
    }

    public function testWrongMultipart()
    {
        $message = new Zend_Mail_Message(array('raw' => "Content-Type: multipart/mixed\r\n\r\ncontent"));

        try {
            $message->getPart(1);
        } catch (Zend_Exception $e) {
            return; // ok
        }
        $this->fail('no exception raised while getting part from message without boundary');
    }

    public function testLateFetch()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => dirname(__FILE__) . '/_files/test.mbox/INBOX'));

        $message = new Zend_Mail_Message(array('handler' => $mail, 'id' => 5));
        $this->assertEquals($message->countParts(), 2);
        $this->assertEquals($message->countParts(), 2);

        $message = new Zend_Mail_Message(array('handler' => $mail, 'id' => 5));
        $this->assertEquals($message->subject, 'multipart');

        $message = new Zend_Mail_Message(array('handler' => $mail, 'id' => 5));
        $this->assertTrue(strpos($message->getContent(), 'multipart message') === 0);
    }

    public function testManualIterator()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 1);

        $message->next();
        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 2);

        $message->next();
        $this->assertFalse($message->valid());

        $message->rewind();
        $this->assertTrue($message->valid());
        $this->assertEquals($message->getChildren(), $message->current());
        $this->assertEquals($message->key(), 1);
    }
}
