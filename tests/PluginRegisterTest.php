<?php

class PluginRegisterTest extends PHPUnit\Framework\TestCase
{
  private $model;
  private $concept;
  private $mockpr;

  protected function setUp() : void
  {
    $this->mockpr = $this->getMockBuilder('PluginRegister')->setConstructorArgs(array(array('test-plugin2',
                                                                                            'global-plugin-Bravo',
                                                                                            'imaginary-plugin',
                                                                                            'test-plugin1',
                                                                                            'global-plugin-alpha',
                                                                                            'global-plugin-charlie',
                                                                                            'test-plugin3'
                                                                                            )))
                                                           ->setMethods(['getPlugins'])
                                                           ->getMock();
    $this->stubplugs = array ('imaginary-plugin' => array ( 'js' => array ( 0 => 'imaginaryPlugin.js', ),
                                                 'css' => array ( 0 => 'stylesheet.css', ),
                                                 'templates' => array ( 0 => 'template.html', ),
                                                 'callback' => array ( 0 => 'imaginaryPlugin')
                        ),
                        'test-plugin1' => array ( 'js' => array ( 0 => 'plugin1.js', 1 => 'second.min.js'),
                                                 'css' => array ( 0 => 'stylesheet.css', ),
                                                 'templates' => array ( 0 => 'template.html', ),
                                                 'callback' => array ( 0 => 'callplugin1')
                        ),
                        'test-plugin2' => array ( 'js' => array ( 0 => 'plugin2.js', ),
                                                 'css' => array ( 0 => 'stylesheet.css', ),
                                                 'templates' => array ( 0 => 'template.html', ),
                                                 'callback' => array ( 0 => 'callplugin2')
                        ),
                        'test-plugin3' => array ( 'js' => array ( 0 => 'plugin3.js', ),
                                                 'css' => array ( 0 => 'stylesheet.css', ),
                                                 'templates' => array ( 0 => 'template.html', ),
                                                 'callback' => array ( 0 => 'callplugin3')
                        ),
                        'only-css' => array ( 'css' => array ( 0 => 'super.css')),
                        'global-plugin-alpha' => array('js' => array('alpha.js'),
                                                  'callback' => array ( 0 => 'alpha')),
                        'global-plugin-Bravo' => array('js' => array('Bravo.js'),
                                                  'callback' => array ( 0 => 'bravo')),
                        'global-plugin-charlie' => array('js' => array('charlie.js'),
                                                  'callback' => array ( 0 => 'charlie')));
    $this->mockpr->method('getPlugins')->will($this->returnValue($this->stubplugs));
    $this->model = new Model(new GlobalConfig('/../tests/testconfig.ttl'));
    $this->vocab = $this->model->getVocabulary('test');
  }

  /**
   * @covers PluginRegister::__construct
   */
  public function testConstructor()
  {
    $plugins = new PluginRegister();
    $this->assertInstanceOf('PluginRegister', $plugins);
  }

  /**
   * @covers PluginRegister::getPlugins
   * @covers PluginRegister::getPluginsJS
   */
  public function testGetPluginsJS()
  {
    $plugins = new PluginRegister();
    $this->assertEquals(array(), $plugins->getPluginsJS());
  }

  /**
   * @covers PluginRegister::getPlugins
   * @covers PluginRegister::getPluginsJS
   * @covers PluginRegister::filterPlugins
   * @covers PluginRegister::filterPluginsByName
   */
  public function testGetPluginsJSWithName()
  {
    $this->assertEquals($this->mockpr->getPluginsJS()['test-plugin1'],
                        array('plugins/test-plugin1/plugin1.js', 'plugins/test-plugin1/second.min.js'));
  }

  /**
   * @covers PluginRegister::getPlugins
   * @covers PluginRegister::getPluginsJS
   * @covers PluginRegister::filterPlugins
   * @covers PluginRegister::filterPluginsByName
   */
  public function testGetPluginsJSWithGlobalPlugin()
  {
    $this->assertEquals($this->mockpr->getPluginsJS()['global-plugin-alpha'],
                        array('plugins/global-plugin-alpha/alpha.js'));
  }

  /**
   * @covers PluginRegister::getPluginsCSS
   */
  public function testGetPluginsCSS()
  {
    $plugins = new PluginRegister();
    $this->assertEquals(array(), $plugins->getPluginsCSS());
  }

  /**
   * @covers PluginRegister::getPluginsCSS
   * @covers PluginRegister::filterPlugins
   * @covers PluginRegister::filterPluginsByName
   */
  public function testGetPluginsCSSWithName()
  {
    $plugins = new PluginRegister();
    $this->assertEquals($this->mockpr->getPluginsCSS()['test-plugin1'],
                        array('plugins/test-plugin1/stylesheet.css'));
  }

  /**
   * @covers PluginRegister::getPluginsTemplates
   */
  public function testGetPluginsTemplates()
  {
    $plugins = new PluginRegister();
    $this->assertEquals(array(), $plugins->getPluginsTemplates());
  }

  /**
   * @covers PluginRegister::getTemplates
   */
  public function testGetTemplates()
  {
    $plugins = new PluginRegister();
    $this->assertEquals(array(), $plugins->getTemplates());
  }

  /**
   * @covers PluginRegister::getCallbacks
   */
  public function testGetCallbacks()
  {
    $this->assertEquals($this->mockpr->getCallbacks(),
                        array('callplugin2', 'bravo', 'imaginaryPlugin', 'callplugin1', 'alpha', 'charlie', 'callplugin3')
    );
  }

}
