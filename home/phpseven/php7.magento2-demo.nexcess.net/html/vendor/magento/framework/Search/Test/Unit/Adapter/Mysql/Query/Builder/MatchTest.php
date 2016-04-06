<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Search\Test\Unit\Adapter\Mysql\Query\Builder;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class MatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scoreBuilder;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match
     */
    private $match;

    /**
     * @var \Magento\Framework\DB\Helper\Mysql\Fulltext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fulltextHelper;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->scoreBuilder = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\ScoreBuilder')
            ->setMethods(['addCondition'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resolver = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface')
            ->setMethods(['resolve'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->fulltextHelper = $this->getMockBuilder('Magento\Framework\DB\Helper\Mysql\Fulltext')
            ->disableOriginalConstructor()
            ->getMock();

        $this->match = $helper->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match',
            ['resolver' => $this->resolver, 'fulltextHelper' => $this->fulltextHelper]
        );
    }

    public function testBuildQuery()
    {
        /** @var Select|\PHPUnit_Framework_MockObject_MockObject $select */
        $select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods(['getMatchQuery', 'match', 'where'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->fulltextHelper->expects($this->once())
            ->method('getMatchQuery')
            ->with($this->equalTo(['some_field' => 'some_field']), $this->equalTo('-some_value*'))
            ->will($this->returnValue('matchedQuery'));
        $select->expects($this->once())
            ->method('where')
            ->with('matchedQuery')
            ->willReturnSelf();

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->willReturnCallback(function ($fieldList) {
                $resolvedFields = [];
                foreach ($fieldList as $column) {
                    $field = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\Field\FieldInterface')
                        ->disableOriginalConstructor()
                        ->getMockForAbstractClass();
                    $field->expects($this->any())
                        ->method('getColumn')
                        ->willReturn($column);
                    $resolvedFields[] = $field;
                }
                return $resolvedFields;
            });

        /** @var \Magento\Framework\Search\Request\Query\Match|\PHPUnit_Framework_MockObject_MockObject $query */
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Match')
            ->setMethods(['getMatches', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())
            ->method('getValue')
            ->willReturn('some_value ');
        $query->expects($this->once())
            ->method('getMatches')
            ->willReturn([['field' => 'some_field']]);

        $this->scoreBuilder->expects($this->once())
            ->method('addCondition');

        $result = $this->match->build($this->scoreBuilder, $select, $query, BoolExpression::QUERY_CONDITION_NOT);

        $this->assertEquals($select, $result);
    }
}
