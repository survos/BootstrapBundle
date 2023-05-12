<?php

namespace Survos\BootstrapBundle\Tests;

use PHPUnit\Framework\TestCase;
use Survos\BootstrapBundle\Service\ContextService;

class ContextServiceTest extends TestCase
{
    public function testOptions()
    {
        $options = [
            'test_option1' => 'Test1',
            'test_option2' => 'Test2'
        ];

        $contextService = new ContextService($options);

        $this->assertSame($options, $contextService->getOptions());
        $this->assertSame($options['test_option1'], $contextService->getOption('test_option1'));
        $this->assertSame($options['test_option2'], $contextService->getOption('test_option2'));
    }
}
