<?php

declare(strict_types=1);

namespace App\Lib\MultipagBradesco\CNAB;

use App\Lib\MultipagBradesco\BankTransfer;
use App\Lib\MultipagBradesco\CNAB\Transfer\TransferBatch;
use App\Lib\MultipagBradesco\TransferAccount;
use DateTimeImmutable;

class File
{
    /**
     * @param array<BankTransfer> $sameBankTransfers
     * @param array<BankTransfer> $otherBankTransfers
     */
    public static function render(
        string $sequence,
        string $agreement,
        TransferAccount $company,
        DateTimeImmutable $now,
        array $sameBankTransfers,
        array $otherBankTransfers
    ): string {
        /**
         * Each transfer have 2 entries, one for segment A and other for segment B
         * 2 is also added to include file header and file trailer lines
         */
        $numOfTransfers = count($sameBankTransfers) + count($otherBankTransfers);
        $numOfTransferEntries = ($numOfTransfers * 2) + 2;
        $numOfBatches = 0;

        $lines = [FileHeader::render($sequence, $agreement, $company, $now)];

        if (count($sameBankTransfers) > 0) {
            $numOfBatches++;
            $lines[] = TransferBatch::render($agreement, "$numOfBatches", true, $company, $sameBankTransfers);
        }

        if (count($otherBankTransfers) > 0) {
            $numOfBatches++;
            $lines[] = TransferBatch::render($agreement, "$numOfBatches", false, $company, $otherBankTransfers);
        }

        $lines[] = FileTrailer::render($numOfBatches, $numOfTransferEntries);

        return CNAB::join($lines, "\r\n");
    }
}
