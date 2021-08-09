<?php

namespace App\Tests;

use App\InvalidTemplateException;
use App\ResultTemplateMismatchException;
use App\ReverseTemplate;
use PHPUnit\Framework\TestCase;

class ReverseTemplateTest extends TestCase
{

    public function testSuccessfulReverseTemplate(): void
    {
        $reverseTemplate = new ReverseTemplate();
        $template = 'Hello, my name is {name}.';
        $templateHtmlEscaped = 'Hello, my name is {{name}}.';

        $res = $reverseTemplate->execute($templateHtmlEscaped, 'Hello, my name is Juni.');

        $this->assertEquals(['name' => 'Juni'], $res);

        $res = $reverseTemplate->execute($templateHtmlEscaped, 'Hello, my name is .');

        $this->assertEquals(['name' => ''], $res);

        $res = $reverseTemplate->execute('Hello, my name is {name}, {{type}}.', 'Hello, my name is Juni, &lt;robot&gt;.');

        $this->assertEquals(['name' => 'Juni', 'type' => '<robot>' ], $res);

        $res = $reverseTemplate->execute($templateHtmlEscaped, 'Hello, my name is &lt;robot&gt;.');

        $this->assertEquals(['name' => '<robot>'], $res);

        $res = $reverseTemplate->execute($template, 'Hello, my name is <robot>.');

        $this->assertEquals(['name' => '<robot>'], $res);
    }

    public function testInvalidTemplate(): void
    {
        $this->expectException(InvalidTemplateException::class);

        $reverseTemplate = new ReverseTemplate();
        $reverseTemplate->execute('Hello, my name is {{name}.', 'Hello, my name is Juni.');
    }

    public function testInvalidTemplateNotFoundVariable(): void
    {
        $this->expectException(InvalidTemplateException::class);

        $reverseTemplate = new ReverseTemplate();
        $reverseTemplate->execute('Hello, my name is name.', 'Hello, my name is Juni.');
    }

    public function testResultTemplateMismatch(): void
    {
        $this->expectException(ResultTemplateMismatchException::class);

        $reverseTemplate = new ReverseTemplate();
        $reverseTemplate->execute('Hello, my name is {{name}}.', 'Hello, my lastname is Juni.');
    }
}
