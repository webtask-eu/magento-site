<?php

declare(strict_types=1);

namespace StripeIntegration\Payments\Helper;

use StripeIntegration\Payments\Exception\GenericException;

class SetupIntent
{
    private $config;
    private $helper;
    private $customer;
    private $remoteAddress;
    private $httpHeader;
    private $paymentMethodFactory;

    public function __construct(
        \StripeIntegration\Payments\Model\Stripe\PaymentMethodFactory $paymentMethodFactory,
        \StripeIntegration\Payments\Model\Config $config,
        \StripeIntegration\Payments\Helper\Generic $helper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\HTTP\Header $httpHeader
    ) {
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->config = $config;
        $this->helper = $helper;
        $this->customer = $helper->getCustomerModel();
        $this->remoteAddress = $remoteAddress;
        $this->httpHeader = $httpHeader;
    }

    public function getCreateParams($quote, $order)
    {
        if ($order)
        {
            $description = $this->helper->getOrderDescription($order);
        }
        else if ($quote)
        {
            $description = $this->helper->getQuoteDescription($quote);
        }
        else
        {
            throw new GenericException("Could not create SetupIntent. No order or quote provided.");
        }

        if (!$this->customer->getStripeId())
        {
            $this->customer->createStripeCustomerIfNotExists(false, $order);
        }

        $paymentMethodId = $order->getPayment()->getAdditionalInformation("token");
        $paymentMethod = $this->paymentMethodFactory->create()->fromPaymentMethodId($paymentMethodId)->getStripeObject();

        $params = [
            "use_stripe_sdk" => true,
            "automatic_payment_methods" => [ 'enabled' => 'true' ],
            "payment_method" => $order->getPayment()->getAdditionalInformation("token"),
            "customer" => $this->customer->getStripeId(),
            "description" => $description,
            "metadata" => $this->config->getMetadata($order),
            "confirm" => true,
            "usage" => "off_session",
            "return_url" => $this->helper->getUrl("stripe/payment/index"),
            "mandate_data" => $this->getMandateData($paymentMethod)
        ];

        $customerEmail = $order->getCustomerEmail();
        if ($customerEmail && $this->config->isReceiptEmailsEnabled())
            $params["receipt_email"] = $customerEmail;

        return $params;
    }

    public function getConfirmParams($order)
    {
        $paymentMethodId = $order->getPayment()->getAdditionalInformation("token");
        $paymentMethod = $this->paymentMethodFactory->create()->fromPaymentMethodId($paymentMethodId)->getStripeObject();

        $params = [
            "use_stripe_sdk" => true,
            "payment_method" => $order->getPayment()->getAdditionalInformation("token"),
            "return_url" => $this->helper->getUrl("stripe/payment/index"),
            "mandate_data" => $this->getMandateData($paymentMethod)
        ];

        return $params;
    }

    private function getMandateData($paymentMethod)
    {
        $remoteAddress = $this->remoteAddress->getRemoteAddress();
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $unsupportedMethods = ['afterpay_clearpay', 'paypal', 'blik'];

        if (!$remoteAddress || !$userAgent || empty($paymentMethod->type) || in_array($paymentMethod->type, $unsupportedMethods))
        {
            return [];
        }

        $mandateData = [
            "customer_acceptance" => [
                "type" => "online",
                "online" => [
                    "ip_address" => $remoteAddress,
                    "user_agent" => $userAgent,
                ]
            ]
        ];

        return $mandateData;
    }
}