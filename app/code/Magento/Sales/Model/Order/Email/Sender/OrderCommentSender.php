<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\NotifySender;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderCommentSender
 */
class OrderCommentSender extends NotifySender
{
    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Template $templateContainer
     * @param OrderCommentIdentity $identityContainer
     * @param Order\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Template $templateContainer,
        OrderCommentIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager
    ) {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger);
        $this->addressRenderer = $addressRenderer;
        $this->eventManager = $eventManager;
    }

    /**
     * Send email to customer
     *
     * @param Order $order
     * @param bool $notify
     * @param string $comment
     * @return bool
     */
    public function send(Order $order, $notify = true, $comment = '')
    {
        if ($order->getShippingAddress()) {
            $formattedShippingAddress = $this->addressRenderer->format($order->getShippingAddress(), 'html');
        } else {
            $formattedShippingAddress = '';
        }
        $formattedBillingAddress = $this->addressRenderer->format($order->getBillingAddress(), 'html');

        $transport = new \Magento\Framework\Object(
            ['template_vars' =>
                 [
                     'order'                    => $order,
                     'comment'                  => $comment,
                     'billing'                  => $order->getBillingAddress(),
                     'store'                    => $order->getStore(),
                     'formattedShippingAddress' => $formattedShippingAddress,
                     'formattedBillingAddress'  => $formattedBillingAddress,
                 ]
            ]
        );

        $this->eventManager->dispatch(
            'email_order_comment_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport->getTemplateVars());

        return $this->checkAndSend($order, $notify);
    }
}
