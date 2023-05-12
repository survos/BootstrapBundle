<?php

namespace Survos\BootstrapBundle\Tests;

use PHPUnit\Framework\TestCase;
use Survos\BootstrapBundle\Helper\ContextHelper;

class ContextHelperTest extends TestCase
{
    public function testOptions()
    {
        $contextHelper = new ContextHelper();

        $this->assertSame(false, $contextHelper->hasOption('test1'));

        $this->assertSame(null, $contextHelper->getOption('test1'));
        $this->assertSame('0', $contextHelper->getOption('test1', '0'));

        $this->assertInstanceOf(ContextHelper::class, $contextHelper->setOption('test1', 'Test'));
        $this->assertSame('Test', $contextHelper->getOption('test1'));

        $this->assertSame(['test1' => 'Test'], $contextHelper->getOptions());
        $contextHelper->setOption('test2', 'Test2');
        $this->assertSame(['test1' => 'Test', 'test2' => 'Test2'], $contextHelper->getOptions());

        $this->assertSame(true, $contextHelper->hasOption('test1'));
        $this->assertSame(true, $contextHelper->hasOption('test2'));
    }
}
