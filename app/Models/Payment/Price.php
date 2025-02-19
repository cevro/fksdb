<?php

namespace FKSDB\Models\Payment;

use FKSDB\Models\Payment\PriceCalculator\UnsupportedCurrencyException;

class Price {

    public const CURRENCY_EUR = 'eur';
    public const CURRENCY_CZK = 'czk';

    private string $currency;

    private float $amount;

    public function __construct(float $amount, string $currency) {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @throws \LogicException
     */
    public function add(Price $price): void {
        if ($this->currency !== $price->getCurrency()) {
            throw new \LogicException('Currencies are not a same');
        }
        $this->amount += $price->getAmount();
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function addAmount(float $amount): void {
        $this->amount += $amount;
    }

    /**
     * @return string[]
     */
    public static function getAllCurrencies(): array {
        return [self::CURRENCY_CZK, self::CURRENCY_EUR];
    }

    /**
     * @throws UnsupportedCurrencyException
     */
    public static function getLabel(string $currency): string {
        switch ($currency) {
            case self::CURRENCY_EUR:
                return '€';
            case self::CURRENCY_CZK:
                return 'Kč';
            default:
                throw new UnsupportedCurrencyException($currency);
        }
    }

    /**
     * @throws UnsupportedCurrencyException
     */
    public function __toString(): string {
        return \sprintf('%1.2f %s', $this->amount, self::getLabel($this->currency));
    }
}
