<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route.php';

/** PHPUnit2 test case */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_RouteTest extends PHPUnit2_Framework_TestCase
{

    public function testStaticMatch()
    {

        $route = new Zend_Controller_Router_Route('users/all');
        $values = $route->match('users/all');

        $this->assertSame(array(), $values);

    }

    public function testStaticPathShorterThanParts()
    {

        $route = new Zend_Controller_Router_Route('users/a/martel');
        $values = $route->match('users/a');

        $this->assertSame(false, $values);

    }

    public function testStaticPathLongerThanParts()
    {

        $route = new Zend_Controller_Router_Route('users/a');
        $values = $route->match('users/a/martel');

        $this->assertSame(false, $values);

    }

    public function testStaticMatchWithDefaults()
    {

        $route = new Zend_Controller_Router_Route('users/all', array('controller' => 'ctrl'));
        $values = $route->match('users/all');

        $this->assertSame('ctrl', $values['controller']);

    }

    public function testNotMatched()
    {

        $route = new Zend_Controller_Router_Route('users/all');
        $values = $route->match('users/martel');
        
        $this->assertSame(false, $values);
    }
    
    public function testNotMatchedWithVariablesAndDefaults()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action', array('controller' => 'index', 'action' => 'index'));
        $values = $route->match('archive/action/bogus');
        
        $this->assertSame(false, $values);
    }
    
    
    public function testNotMatchedWithVariablesAndStatic() 
    {

        $route = new Zend_Controller_Router_Route('archive/:year/:month');
        $values = $route->match('ctrl/act/2000');

        $this->assertSame(false, $values);

    }

    public function testStaticMatchWithWildcard()
    {

        $route = new Zend_Controller_Router_Route('news/view/*', array('controller' => 'news', 'action' => 'view'));
        $values = $route->match('news/view/show/all/year/2000');

        $this->assertEquals('news', $values['controller']);
        $this->assertEquals('view', $values['action']);
        $this->assertEquals('all', $values['show']);
        $this->assertEquals('2000', $values['year']);

    }

    public function testVariableValues()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year');
        $values = $route->match('ctrl/act/2000');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2000', $values['year']);

    }

    public function testOneVariableValue()
    {

        $route = new Zend_Controller_Router_Route(':action', array('controller' => 'ctrl', 'action' => 'action'));
        $values = $route->match('act');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);

    }

    public function testVariablesWithDefault()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'));
        $values = $route->match('ctrl/act');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2006', $values['year']);

    }

    public function testVariablesWithNullDefault() // Kevin McArthur
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => null));
        $values = $route->match('ctrl/act');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertNull($values['year']);

    }
    
    public function testVariablesWithDefaultAndValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'));
        $values = $route->match('ctrl/act/2000');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2000', $values['year']);

    }

    public function testVariablesWithRequirementAndValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', null, array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2000', $values['year']);

    }

    public function testVariablesWithRequirementAndIncorrectValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', null, array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000t');

        $this->assertSame(false, $values);

    }

    public function testVariablesWithDefaultAndRequirement()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2000', $values['year']);
        
    }

    public function testVariablesWithDefaultAndRequirementAndIncorrectValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000t');

        $this->assertSame(false, $values);

    }

    public function testVariablesWithDefaultAndRequirementAndWithoutValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act');

        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
        $this->assertSame('2006', $values['year']);

    }

    public function testAssemble()
    {

        $route = new Zend_Controller_Router_Route('authors/:name');
        $url = $route->assemble(array('name' => 'martel'));

        $this->assertSame('authors/martel', $url);

    }

    public function testAssembleWithoutValue()
    {

        $route = new Zend_Controller_Router_Route('authors/:name');
        try {
            $url = $route->assemble();   
        } catch (Exception $e) {
            return true;
        }

        $this->fail();

    }

    public function testAssembleWithDefault()
    {

        $route = new Zend_Controller_Router_Route('authors/:name', array('name' => 'martel'));
        $url = $route->assemble();

        $this->assertSame('authors/martel', $url);

    }

    public function testAssembleWithDefaultAndValue()
    {

        $route = new Zend_Controller_Router_Route('authors/:name', array('name' => 'martel'));
        $url = $route->assemble(array('name' => 'mike'));

        $this->assertSame('authors/mike', $url);

    }

    public function testAssembleWithWildcardMap()
    {

        $route = new Zend_Controller_Router_Route('authors/:name/*');
        $url = $route->assemble(array('name' => 'martel'));

        $this->assertSame('authors/martel', $url);

    }

}
