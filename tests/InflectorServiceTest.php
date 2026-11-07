<?php

use Doctrine\Inflector\Language;
use Paliari\Doctrine\InflectorService;
use PHPUnit\Framework\TestCase;

class InflectorServiceTest extends TestCase
{

    public function testContextIsCachedByLanguage()
    {
        $portuguese = InflectorService::getContext();
        $this->assertSame($portuguese, InflectorService::getContext(Language::PORTUGUESE));
        $this->assertNotSame($portuguese, InflectorService::getContext(Language::ENGLISH));
        $this->assertSame(InflectorService::getContext(Language::ENGLISH), InflectorService::getContext(Language::ENGLISH));
    }

    public function testInflections()
    {
        $inflector = InflectorService::getContext();
        $this->assertSame('lengthOf', $inflector->camelize('length_of'));
        $this->assertSame('GreaterThan', $inflector->classify('greater_than'));
        $this->assertSame('my_model', $inflector->tableize('MyModel'));
    }

}
