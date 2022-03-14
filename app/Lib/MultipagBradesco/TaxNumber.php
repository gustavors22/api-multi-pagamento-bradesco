<?php

declare(strict_types=1);

namespace App\Lib\MultipagBradesco;

use App\Lib\MultipagBradesco\Exception\InvalidTaxNumber;

class TaxNumber
{
    private const TAX_NUMBER_TYPE_CPF = '1';
    private const TAX_NUMBER_TYPE_CNPJ = '2';
    private const CPF_NUMBER_LENGTH = 11;
    private const CNPJ_NUMBER_LENGTH = 14;

    private string $number;
    private string $type;

    /**
     * @throws InvalidTaxNumber
     */
    public function __construct(string $number)
    {
        $sanitized = preg_replace('/[^0-9]/', '', $number);
        $sanitized = "{$sanitized}";

        // $type = match (strlen($sanitized)) {
        //     self::CPF_NUMBER_LENGTH => self::TAX_NUMBER_TYPE_CPF,
        //     self::CNPJ_NUMBER_LENGTH => self::TAX_NUMBER_TYPE_CNPJ,
        //     default => throw new InvalidTaxNumber($number),
        // };

        if(strlen($sanitized) == self::CPF_NUMBER_LENGTH){
            $type = self::TAX_NUMBER_TYPE_CPF;

        }else if(strlen($sanitized) == self::CNPJ_NUMBER_LENGTH){
            $type = self::TAX_NUMBER_TYPE_CNPJ;

        } else {
            throw new InvalidTaxNumber($number);
        }

        $this->number = $sanitized;
        $this->type = $type;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
