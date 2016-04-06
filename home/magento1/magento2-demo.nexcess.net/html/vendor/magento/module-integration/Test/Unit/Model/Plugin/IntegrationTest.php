<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Test\Unit\Model\Plugin;

use Magento\Integration\Model\Integration;

/**
 * Unit test for \Magento\Integration\Model\Plugin\Integration
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * API setup plugin
     *
     * @var \Magento\Integration\Model\Plugin\Integration
     */
    protected $integrationPlugin;

    /**
     * @var \Magento\Integration\Api\IntegrationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var  \Magento\Authorization\Model\Acl\AclRetriever|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclRetrieverMock;

    /**
     * @var \Magento\Integration\Api\AuthorizationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $integrationAuthServiceMock;

    public function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Integration\Model\IntegrationService', [], [], '', false);
        $this->integrationAuthServiceMock = $this->getMock(
            'Magento\Integration\Api\AuthorizationServiceInterface',
            ['removePermissions', 'grantAllPermissions', 'grantPermissions'],
            [],
            '',
            false
        );
        $this->aclRetrieverMock = $this->getMock(
            'Magento\Authorization\Model\Acl\AclRetriever',
            ['getAllowedResourcesByUser'],
            [],
            '',
            false
        );
        $this->integrationPlugin = new \Magento\Integration\Model\Plugin\Integration(
            $this->integrationAuthServiceMock,
            $this->aclRetrieverMock
        );
    }

    public function testAfterDelete()
    {
        $integrationId = 1;
        $integrationsData = [
            Integration::ID => $integrationId,
            Integration::NAME => 'TestIntegration1',
            Integration::EMAIL => 'test-integration1@magento.com',
            Integration::ENDPOINT => 'http://endpoint.com',
            Integration::SETUP_TYPE => 1,
        ];

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('removePermissions')
            ->with($integrationId);
        $this->integrationPlugin->afterDelete($this->subjectMock, $integrationsData);
    }

    public function testAfterCreateAllResources()
    {
        $integrationId = 1;
        $integrationModelMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->getMock();
        $integrationModelMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationModelMock->expects($this->once())
            ->method('getData')
            ->with('all_resources')
            ->will($this->returnValue(1));

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('grantAllPermissions')
            ->with($integrationId);

        $this->integrationPlugin->afterCreate($this->subjectMock, $integrationModelMock);
    }

    public function testAfterCreateSomeResources()
    {
        $integrationId = 1;
        $integrationModelMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->getMock();
        $integrationModelMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationModelMock->expects($this->at(1))
            ->method('getData')
            ->with('all_resources')
            ->will($this->returnValue(null));
        $integrationModelMock->expects($this->at(2))
            ->method('getData')
            ->with('resource')
            ->will($this->returnValue(['testResource']));
        $integrationModelMock->expects($this->at(4))
            ->method('getData')
            ->with('resource')
            ->will($this->returnValue(['testResource']));

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('grantPermissions')
            ->with($integrationId, ['testResource']);

        $this->integrationPlugin->afterCreate($this->subjectMock, $integrationModelMock);
    }

    public function testAfterCreateNoResource()
    {
        $integrationId = 1;
        $integrationModelMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->getMock();
        $integrationModelMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationModelMock->expects($this->at(1))
            ->method('getData')
            ->with('all_resources')
            ->will($this->returnValue(null));
        $integrationModelMock->expects($this->at(2))
            ->method('getData')
            ->with('resource')
            ->will($this->returnValue(null));

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('grantPermissions')
            ->with($integrationId, []);

        $this->integrationPlugin->afterCreate($this->subjectMock, $integrationModelMock);
    }

    public function testAfterUpdateAllResources()
    {
        $integrationId = 1;
        $integrationModelMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->getMock();
        $integrationModelMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationModelMock->expects($this->once())
            ->method('getData')
            ->with('all_resources')
            ->will($this->returnValue(1));

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('grantAllPermissions')
            ->with($integrationId);

        $this->integrationPlugin->afterUpdate($this->subjectMock, $integrationModelMock);
    }

    public function testAfterGet()
    {
        $integrationId = 1;
        $integrationModelMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->getMock();
        $integrationModelMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationModelMock->expects($this->once())
            ->method('setData')
            ->with('resource', ['testResource']);

        $this->aclRetrieverMock->expects($this->once())
            ->method('getAllowedResourcesByUser')
            ->with(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_INTEGRATION, $integrationId)
            ->will($this->returnValue(['testResource']));

        $this->integrationPlugin->afterGet($this->subjectMock, $integrationModelMock);
    }
}
