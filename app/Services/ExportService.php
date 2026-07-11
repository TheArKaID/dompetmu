<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Ledger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ExportService
{
    /**
     * Export a collection of Ledger entries to a DompetKu-compatible CSV string.
     *
     * Format: Kategori,Rekening,Jumlah,Tanggal,Catatan,Tipe
     *
     * Transfer rows are expanded to 2-3 CSV rows (Kirim Saldo / Terima Saldo / Biaya Admin).
     */
    public function toCsvString(Collection|SupportCollection $ledgers): string
    {
        $rows = [['Kategori', 'Rekening', 'Jumlah', 'Tanggal', 'Catatan', 'Tipe']];

        // Group transfer rows by reference_id so we can handle them as a unit
        $transferGroups   = [];
        $processedRefIds  = [];

        foreach ($ledgers as $ledger) {
            $type = $ledger->transaction_type;

            // Collect transfer groups
            if (in_array($type, [
                TransactionType::TRANSFER_OUT,
                TransactionType::TRANSFER_IN,
                TransactionType::ADMIN_FEE,
            ], true) && $ledger->reference_id) {
                $transferGroups[$ledger->reference_id][] = $ledger;
                continue;
            }

            // Regular row
            $rows[] = $this->buildRegularRow($ledger);
        }

        // Expand transfer groups into 2-3 CSV rows each
        foreach ($transferGroups as $refId => $group) {
            if (in_array($refId, $processedRefIds, true)) {
                continue;
            }

            foreach ($group as $entry) {
                $type = $entry->transaction_type;

                if ($type === TransactionType::TRANSFER_OUT) {
                    $rows[] = [
                        'Kirim Saldo',
                        $entry->account->name ?? '',
                        number_format($entry->amount, 0, ',', '.'),
                        $entry->transaction_date->format('Y-m-d H:i'),
                        $entry->note ?? '',
                        '-',
                    ];
                } elseif ($type === TransactionType::TRANSFER_IN) {
                    $rows[] = [
                        'Terima Saldo',
                        $entry->account->name ?? '',
                        number_format($entry->amount, 0, ',', '.'),
                        $entry->transaction_date->format('Y-m-d H:i'),
                        $entry->note ?? '',
                        '+',
                    ];
                } elseif ($type === TransactionType::ADMIN_FEE) {
                    $rows[] = [
                        'Biaya Admin',
                        $entry->account->name ?? '',
                        number_format($entry->amount, 0, ',', '.'),
                        $entry->transaction_date->format('Y-m-d H:i'),
                        $entry->note ?? 'Biaya admin transfer',
                        '-',
                    ];
                }
            }

            $processedRefIds[] = $refId;
        }

        return $this->arrayToCsv($rows);
    }

    /** Build a single CSV row from a regular (non-transfer) ledger entry */
    private function buildRegularRow(Ledger $ledger): array
    {
        $type = $ledger->transaction_type;

        // Determine category column
        $kategori = match ($type) {
            TransactionType::INCOME       => $ledger->category?->name ?? 'Pemasukan',
            TransactionType::EXPENSE      => $ledger->category?->name ?? 'Pengeluaran',
            TransactionType::DEBT         => 'Hutang',
            TransactionType::DEBT_PAYMENT => 'Bayar Hutang',
            TransactionType::LOAN         => 'Beri Pinjaman',
            TransactionType::LOAN_PAYMENT => 'Terima Bayar Hutang',
            default                       => $type->label(),
        };

        $tipe = $ledger->is_mutation_in ? '+' : '-';

        return [
            $kategori,
            $ledger->account->name ?? '',
            number_format($ledger->amount, 0, ',', '.'),
            $ledger->transaction_date->format('Y-m-d H:i'),
            $ledger->note ?? '',
            $tipe,
        ];
    }

    /** Convert a 2D array to a properly escaped CSV string */
    private function arrayToCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
