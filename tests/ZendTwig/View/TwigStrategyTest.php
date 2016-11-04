<?php

namespace ZendTwig\Test\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\ViewEvent;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigStrategy;

class TwigStrategyTest extends TestCase
{
    /**
     * Check that correct render was selected
     */
    public function testSelectRenderer()
    {
        $model = $this->getMockBuilder('Zend\View\Model\ModelInterface')->getMock();
        $model->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        /**
         * @var \Zend\View\Model\ModelInterface $model
         */
        $event = new ViewEvent();
        $event->setModel($model);

        /**
         * @var \ZendTwig\View\TwigStrategy $strategy
         */
        $sm       = Bootstrap::getInstance()->getServiceManager();
        $strategy = $sm->get(TwigStrategy::class);
        $renderA  = $sm->get(TwigRenderer::class);
        $renderB  = $strategy->selectRender($event);

        $this->assertSame($renderA, $renderB);
    }

    /**
     * Check that response was injected
     */
    public function testInjectResponse()
    {
        $expected = "<span>value1</span><span>value2</span>\n";
        $model    = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('View/testInjectResponse');

        /**
         * @var \Zend\View\View $view
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals($expected, $result);
    }
}