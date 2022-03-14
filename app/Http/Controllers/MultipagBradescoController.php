<?php

namespace App\Http\Controllers;

use App\Lib\MultipagBradesco\AccountType;
use App\Lib\MultipagBradesco\Address;
use App\Lib\MultipagBradesco\BankDetails;
use App\Lib\MultipagBradesco\BankTransfer;
use App\Lib\MultipagBradesco\CNAB\File;
use App\Lib\MultipagBradesco\TaxNumber;
use App\Lib\MultipagBradesco\TransferAccount;
use DateTimeImmutable;
use Illuminate\Http\Request;

class MultipagBradescoController extends Controller
{
    public function makeTransaction(Request $request)
    {
        $data = $request->all();

        $account = new TransferAccount(
            $data['company']['name'],
            new TaxNumber($data['company']['tax_number']),
            new Address(
                $data['address']['street'],
                $data['address']['number'],
                $data['address']['additionalDetails'],
                $data['address']['neighborhood'],
                $data['address']['city'],
                $data['address']['postalCode'],
                $data['address']['state']
            ),
            new BankDetails(
                $data['bank']['bankCode'],
                $data['bank']['branchCode'],
                $data['bank']['accountNumber'],
                new AccountType($data['bank']['accountType'])
            )
        );

        $sameBankTransfers = [];

        foreach ($data['sameBankTransfers'] as $bankData) {
            $transferAccount = new TransferAccount(
                $bankData['transferAccount']['name'],
                new TaxNumber($bankData['transferAccount']['taxNumber']),

                new Address(
                    $bankData['transferAccount']['address']['street'],
                    $bankData['transferAccount']['address']['number'],
                    $bankData['transferAccount']['address']['additionalDetails'],
                    $bankData['transferAccount']['address']['neighborhood'],
                    $bankData['transferAccount']['address']['city'],
                    $bankData['transferAccount']['address']['postalCode'],
                    $bankData['transferAccount']['address']['state']
                ),

                new BankDetails(
                    $bankData['transferAccount']['bank']['bankCode'],
                    $bankData['transferAccount']['bank']['branchCode'],
                    $bankData['transferAccount']['bank']['accountNumber'],
                    new AccountType($bankData['transferAccount']['bank']['accountType'])
                )
            );

            $sameBankTransfers[] = new BankTransfer($bankData['transferId'], $bankData['amount'], new DateTimeImmutable(), $transferAccount);
        }

        $otherBankTransfers = [];

        foreach($data['otherBankTransfers'] as $bankData) {
            $transferAccount = new TransferAccount(
                $bankData['transferAccount']['name'],
                new TaxNumber($bankData['transferAccount']['taxNumber']),

                new Address(
                    $bankData['transferAccount']['address']['street'],
                    $bankData['transferAccount']['address']['number'],
                    $bankData['transferAccount']['address']['additionalDetails'],
                    $bankData['transferAccount']['address']['neighborhood'],
                    $bankData['transferAccount']['address']['city'],
                    $bankData['transferAccount']['address']['postalCode'],
                    $bankData['transferAccount']['address']['state']
                ),

                new BankDetails(
                    $bankData['transferAccount']['bank']['bankCode'],
                    $bankData['transferAccount']['bank']['branchCode'],
                    $bankData['transferAccount']['bank']['accountNumber'],
                    new AccountType($bankData['transferAccount']['bank']['accountType'])
                )
            );

            $otherBankTransfers[] = new BankTransfer($bankData['transferId'], $bankData['amount'], new DateTimeImmutable(), $transferAccount);
        }

        $file = File::render(
            $data['sequence'],
            $data['agreement'],
            $account,
            new DateTimeImmutable(),
            $sameBankTransfers,
            $otherBankTransfers
        );

        return response($file);

    }
}
