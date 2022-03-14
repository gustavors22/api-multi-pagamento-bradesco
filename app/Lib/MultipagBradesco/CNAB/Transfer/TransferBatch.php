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
        string $agreement,
        string $batch,
        bool $sameBank,
        TransferAccount $company,
        array $transfers
    ): string {
        $lines = [Header::render($agreement, "$batch", $sameBank, $company)];
        $sequence = 0;
        $total = 0;

        foreach ($transfers as $transfer) {
            $total += $transfer->getAmount();
            $sequence++;
            $lines[] = SegmentA::render($transfer, $batch, "{$sequence}", $sameBank);
            $lines[] = SegmentB::render($transfer, (int)$batch, ++$sequence);
        }

        $lines[] = Trailer::render((int)$batch, $sequence, $total);

        return CNAB::join($lines, "\r\n");
    }
}
