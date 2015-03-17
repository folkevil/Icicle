<?php
namespace Icicle\Tests\Loop;

use EventBase;
use EventConfig;
use Icicle\Loop\EventLoop;
use Icicle\Loop\Events\EventFactoryInterface;
use Icicle\Socket\Stream;

/**
 * @requires extension event
 */
class EventLoopTest extends AbstractLoopTest
{
    protected static $base;
    
    public static function setUpBeforeClass()
    {
        $config = new EventConfig();
        $config->requireFeatures(EventConfig::FEATURE_O1);
        
        self::$base = new EventBase($config);
    }
    
    public static function tearDownAfterClass()
    {
        self::$base->free();
    }
    
    public function createLoop(EventFactoryInterface $eventFactory)
    {
        return new EventLoop($eventFactory, self::$base);
    }
    
    public function testEnabled()
    {
        $this->assertSame(extension_loaded('event'), EventLoop::enabled());
    }
}
