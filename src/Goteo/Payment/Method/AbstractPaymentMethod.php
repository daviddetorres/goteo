<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Payment\Method;

use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;
use Goteo\Application\Currency;
use Goteo\Application\Event\FilterInvestEvent;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\PaymentException;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Helper class with some common interface methods
 *
 * Payments using this implementation use the Omnipay library:
 * http://omnipay.thephpleague.com/
 */
abstract class AbstractPaymentMethod implements PaymentMethodInterface
{
    protected ?GatewayInterface $gateway = null;
    protected ?Invest $invest;
    protected ?Request $request;
    protected ?User $user;

    /**
     * @throws PaymentException
     * @throws Config\ConfigException
     */
    public function __construct(User $user = null) {
        $this->user = $user;
        $this->initGateway();
    }

    /**
     * @throws PaymentException
     * @throws Config\ConfigException
     */
    private function initGateway()
    {
        $factory = new GatewayFactory();
        $this->gateway = $factory->create($this->getGatewayName());

        if (!in_array(GatewayInterface::class, class_implements($this->gateway))) {
            throw new PaymentException("Error on retrieving Omnipay Gateway Class. It must implement Omnipay\Common\GatewayInterface!");
        }

        foreach ($this->gateway->getDefaultParameters() as $var => $val) {
            $config = Config::get('payments.' . $this->getIdNonStatic() . '.' . $var);
            $method = "set" . ucfirst($var);
            if($config && method_exists($this->gateway, $method)) {
                $this->gateway->$method($config);
            }
        }
    }

    /**
     * @deprecated Use getIdNonStatic() instead
     * Returns the id of the method (max 20 chars long)
     * @throws PaymentException
     */
    static public function getId(): string
    {
        $parts = explode('\\', get_called_class());
        $c = end($parts);
        $c = strtolower(str_replace('PaymentMethod', '', $c));

        if (empty($c)) {
            throw new PaymentException('Method getId() must return a valid string');
        }

        return $c;
    }

    /**
     * Returns the id of the method (max 20 chars long)
     * @throws PaymentException
     */
    public function getIdNonStatic(): string
    {
        $parts = explode('\\', get_called_class());
        $c = end($parts);
        $c = strtolower(str_replace('PaymentMethod', '', $c));

        if (empty($c)) {
            throw new PaymentException('Method getIdNonStatic() must return a valid string');
        }

        return $c;
    }

    public function getName(): string
    {
        return Text::get('invest-' . $this->getIdNonStatic() . '-method');
    }

    public function getDesc(): string
    {
        return $this->getName();
    }

    public function getIcon(): string
    {
        return SRC_URL . '/assets/img/pay/' . $this->getIdNonStatic() . '.png';
    }

    /**
     * {@inheritdoc}
     */
    public function isActive($amount = 0): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic($amount = 0): bool
    {
        return true;
    }

    public function setUser(User $user): AbstractPaymentMethod
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setInvest(Invest $invest): AbstractPaymentMethod
    {
        $this->invest = $invest;
        return $this;
    }

    public function getInvest(): Invest
    {
        return $this->invest;
    }

    /**
     * Sets the Request in order to be able to create a proper gateway request
     */
    public function setRequest(Request $request): AbstractPaymentMethod
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Gives a chance to change the Response where to redirect after a $method->completePurchase() situation
     */
    public function getDefaultHttpResponse(ResponseInterface $response): ?Response
    {
        return null;
    }

    /**
     * Called when user pushes the button "pay"
     */
    public function purchase(): ResponseInterface
    {
        $gateway = $this->getGateway();
        $gateway->setCurrency(Currency::getDefault('id'));
        return $gateway->purchase([
            'amount' => (float) $this->getTotalAmount(),
            'description' => $this->getInvestDescription(),
            'returnUrl' => $this->getCompleteUrl(),
            'cancelUrl' => $this->getCompleteUrl(),
        ])->send();
    }

    /**
     * Called when the user returns from the payment gateway or the gateway notifies via notifyUrl
     */
    public function completePurchase(): ResponseInterface
    {
        $gateway = $this->getGateway();
        $gateway->setCurrency(Currency::getDefault('id'));

        return $gateway->completePurchase([
            'amount' => (float) $this->getTotalAmount(),
            'description' => $this->getInvestDescription(),
            'clientIp' => $this->getRequest()->getClientIp(),
            'returnUrl' => $this->getCompleteUrl(),
            'cancelUrl' => $this->getCompleteUrl(),
        ])->send();
    }

    /**
     * {@inheritdoc}
     */
    public function refundable(): bool
    {
        $gateway = $this->getGateway();

        return $gateway->supportsRefund();
    }

    /**
     * {@inheritdoc}
     * @throws PaymentException
     */
    public function refund(): ResponseInterface
    {
        $gateway = $this->getGateway();
        if (!$gateway->supportsRefund()) {
            throw new PaymentException("Refund not supported for method " . strtoupper($this->getIdNonStatic()));
        }
        $invest = $this->getInvest();

        // Any plugin can throw a PaymentException here in order to abort the refund process
        App::dispatch(AppEvents::INVEST_REFUND, new FilterInvestEvent($invest, $this));

        return $gateway->refund([
            'amount' => (float) $this->getTotalAmount(),
            'transactionReference' => $invest->transaction, // some gateway may require extra data saved
        ])->send();
    }

    public function getCompleteUrl(): string
    {
        $request = $this->getRequest();
        $invest = $this->getInvest();

        // Project invest
        if($invest->getProject())
            return $request->getSchemeAndHttpHost() . '/invest/' . $invest->project . '/' . $invest->id . '/complete';
        //Buy credit
        elseif(!$invest->donate_amount)
            return $request->getSchemeAndHttpHost() . '/pool/' . $invest->id . '/complete';
        // Donate to the organization
        else
            return $request->getSchemeAndHttpHost() . '/donate/' . $invest->id . '/complete';
    }

    public function getInvestDescription(): string
    {
        $invest = $this->getInvest();
        $project = $invest->getProject();
        $msg = ''; // TODO: from Text::get()
        if($reward = $invest->getFirstReward()) {
            $msg = $reward->reward . " - ";
        }
        $msg .= $project->name;
        return $msg;
    }

    /**
     * Calculates the total amount, taking into account additional amounts
     */
    public function getTotalAmount() {
        $invest = $this->getInvest();

        // Add to amount project the tip to the organization
        return $invest->amount + $invest->donate_amount;
    }

    public function getGatewayName(): string
    {
        return ucfirst($this->getIdNonStatic());
    }

    public function getGateway(): GatewayInterface
    {
        return $this->gateway;
    }

    /**
     * Calculates banks fee in a generic way, based on settings.yml config and following the Paypal fees rules (which suits many gateways)
     *
     * payments.method.comissions.charged.fixed : fixed amount per transaction on non-refunded invests
     * payments.method.comissions.charged.percent : percent amount per transaction on non-refunded invests
     * payments.method.comissions.refunded.fixed : fixed amount per transaction on refunded invests
     * payments.method.comissions.refunded.percent : percent amount per transaction on refunded invests
     */
    public function calculateCommission(
        $total_invests,
        $total_amount,
        $returned_invests = 0,
        $returned_amount = 0
    ) {
        $commissions = Config::get('payments.' . $this->getIdNonStatic() . '.commissions');
        $fee = 0;
        if($commissions && is_array($commissions)) {
            // Non-refunded
            if($commissions['charged']) {
                $fixed = $commissions['charged']['fixed'] ?: 0;
                $percent = $commissions['charged']['percent'] ?: 0;
                $fee += ($total_amount - $returned_amount) * $percent / 100;
                $fee += ($total_invests - $returned_invests) * $fixed;
            }
            // Refunded
            if($commissions['refunded']) {
                $fixed = $commissions['refunded']['fixed'] ?: 0;
                $percent = $commissions['refunded']['percent'] ?: 0;
                $fee += $returned_amount * $percent / 100;
                $fee += $returned_invests * $fixed;
            }
        }

        return $fee;
    }

    /**
     * Internal payments don't increase raised amounts (pool)
     */
    public function isInternal(): bool
    {
        return false;
    }
}
