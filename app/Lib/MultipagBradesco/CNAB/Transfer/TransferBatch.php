<?php

declare(strict_types=1);

namespace App\Lib\MultipagBradesco\CNAB\Transfer;

use App\Lib\MultipagBradesco\BankTransfer;
use App\Lib\MultipagBradesco\TransferAccount;
use App\Lib\MultipagBradesco\CNAB\CNAB;
use App\Lib\MultipagBradesco\CNAB\Transfer\BankTransferBatchHeader as Header;
use App\Lib\MultipagBradesco\CNAB\Transfer\BankTransferBatchTrailer as Trailer;
use App\Lib\MultipagBradesco\CNAB\Transfer\BankTransferSegmentABatchDetail as SegmentA;
use App\Lib\MultipagBradesco\CNAB\Transfer\BankTransferSegmentBBatchDetail as SegmentB;

class TransferBatch
{
    public static function render(
        int $agreement,
        int $batch,
        bool $sameBank,
        TransferAccount $company,
        BankTransfer ...$transfers
    ): string {
        $lines = [Header::render($agreement, $batch, $sameBank, $company)];
        $sequence = 0;
        $total = 0;

        foreach ($transfers as $transfer) {
            $total += $transfer->getAmount();
            $lines[] = SegmentA::render($transfer, $batch, ++$sequence, $sameBank);
            $lines[] = SegmentB::render($transfer, $batch, ++$sequence);
        }

        $lines[] = Trailer::render($batch, $sequence, $total);

        return CNAB::join($lines, "\r\n");
    }
}
