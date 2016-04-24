<?php

namespace uuf6429\Rune\Context;

use uuf6429\Rune\Rule\GenericRule;
use uuf6429\Rune\Util\TypeInfoClass;
use uuf6429\Rune\Util\TypeInfoMember;

class DynamicContextDescriptorTest extends \PHPUnit_Framework_TestCase
{
    public function testUnsupportedContext()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Context must be or extends DynamicContext.'
        );
        new DynamicContextDescriptor(new \stdClass());
    }

    /**
     * @dataProvider typeInfoDataProvider
     */
    public function testTypeInfo($variables, $functions, $expectedVTI, $expectedFTI, $expectedDTI)
    {
        $context = new DynamicContext($variables, $functions);
        $descriptor = $context->getContextDescriptor();

        $this->assertEquals($expectedVTI, $descriptor->getVariableTypeInfo());
        $this->assertEquals($expectedFTI, $descriptor->getFunctionTypeInfo());
        $this->assertEquals($expectedDTI, $descriptor->getDetailedTypeInfo());
    }

    public function typeInfoDataProvider()
    {
        return [
            'Simple scalar values test' => [
                '$variables' => [
                    'name' => 'Joe',
                    'age' => 20,
                    'married' => false,
                    'salary' => 600.59,
                    'children' => [],
                ],
                '$functions' => [],
                '$expectedVTI' => [
                    'name' => new TypeInfoMember('name', ['string']),
                    'age' => new TypeInfoMember('age', ['integer']),
                    'married' => new TypeInfoMember('married', ['boolean']),
                    'salary' => new TypeInfoMember('salary', ['double']),
                    'children' => new TypeInfoMember('children', ['array']),
                ],
                '$expectedFTI' => [],
                '$expectedDTI' => [],
            ],
            'GenericRule object test' => [
                '$variables' => ['rule' => new GenericRule(0, '', '')],
                '$functions' => [],
                '$expectedVTI' => [
                    'rule' => new TypeInfoMember('rule', ['uuf6429\Rune\Rule\GenericRule']),
                ],
                '$expectedFTI' => [],
                '$expectedDTI' => [
                    'uuf6429\Rune\Rule\GenericRule' => new TypeInfoClass(
                        'uuf6429\Rune\Rule\GenericRule',
                        [
                            new TypeInfoMember('getID', ['method'], '<div class="cm-signature"><span class="type"></span> <span class="name">getID</span>(<span class="args"></span>)</span></div>'),
                            new TypeInfoMember('getName', ['method'], '<div class="cm-signature"><span class="type"></span> <span class="name">getName</span>(<span class="args"></span>)</span></div>'),
                            new TypeInfoMember('getCondition', ['method'], '<div class="cm-signature"><span class="type"></span> <span class="name">getCondition</span>(<span class="args"></span>)</span></div>'),
                        ]
                    ),
                ],
            ],
            'Functions and methods test' => [
                '$variables' => [],
                '$functions' => [
                    'round' => 'round',
                    'now' => [new \DateTime(), 'getTimestamp'],
                ],
                '$expectedVTI' => [],
                '$expectedFTI' => [
                    'round' => new TypeInfoMember('round', ['callable']),
                    'now' => new TypeInfoMember('now', ['callable']),
                ],
                '$expectedDTI' => [],
            ],
        ];
    }
}
