<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Ledger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LedgerService
{
    // ──────────────────────────────────────────────────────────────────────────
    // Standard Transaction (Income, Expense, Debt, Loan variants)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a single standard ledger entry.
     *
     * @param  array{
     *   transaction_type: string,
     *   account_id: string,
     *   category_id?: string|null,
     *   amount: float,
     *   transaction_date: string,
     *   note?: string|null,
     *   contact_name?: string|null,
     * } $data
     */
    public function createStandard(array $data, ?UploadedFile $photo = null): Ledger
    {
        $type = TransactionType::from($data['transaction_type']);

        $photoPath = null;
        if ($photo) {
            $photoPath = $photo->store('transaction-photos', 'public');
        }

        return Ledger::create([
            'transaction_type' => $type,
            'account_id'       => $data['account_id'],
            'category_id'      => $type->requiresCategory() ? ($data['category_id'] ?? null) : null,
            'amount'           => (float) $data['amount'],
            'is_mutation_in'   => $type->isMutationIn(),
            'contact_name'     => $type->requiresContact() ? ($data['contact_name'] ?? null) : null,
            'transaction_date' => $data['transaction_date'],
            'note'             => $data['note'] ?? null,
            'photo_path'       => $photoPath,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Transfer (generates 2–3 ledger rows atomically)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a transfer between accounts.
     * Generates TRANSFER_OUT + TRANSFER_IN (+ optional ADMIN_FEE) rows,
     * all bound by the same reference_id.
     *
     * @param  array{
     *   from_account_id: string,
     *   to_account_id: string,
     *   amount: float,
     *   admin_fee?: float,
     *   transaction_date: string,
     *   note?: string|null,
     * } $data
     * @return Ledger[]
     */
    public function createTransfer(array $data): array
    {
        $referenceId = (string) Str::uuid();
        $ledgers = [];

        DB::transaction(function () use ($data, $referenceId, &$ledgers) {
            // Row 1: Transfer Out (debit from source account)
            $ledgers[] = Ledger::create([
                'transaction_type' => TransactionType::TRANSFER_OUT,
                'account_id'       => $data['from_account_id'],
                'reference_id'     => $referenceId,
                'amount'           => (float) $data['amount'],
                'is_mutation_in'   => false,
                'transaction_date' => $data['transaction_date'],
                'note'             => $data['note'] ?? null,
            ]);

            // Row 2: Transfer In (credit to destination account)
            $ledgers[] = Ledger::create([
                'transaction_type' => TransactionType::TRANSFER_IN,
                'account_id'       => $data['to_account_id'],
                'reference_id'     => $referenceId,
                'amount'           => (float) $data['amount'],
                'is_mutation_in'   => true,
                'transaction_date' => $data['transaction_date'],
                'note'             => $data['note'] ?? null,
            ]);

            // Row 3 (optional): Admin fee deducted from source account
            $adminFee = (float) ($data['admin_fee'] ?? 0);
            if ($adminFee > 0) {
                $ledgers[] = Ledger::create([
                    'transaction_type' => TransactionType::ADMIN_FEE,
                    'account_id'       => $data['from_account_id'],
                    'reference_id'     => $referenceId,
                    'amount'           => $adminFee,
                    'is_mutation_in'   => false,
                    'transaction_date' => $data['transaction_date'],
                    'note'             => 'Biaya admin transfer',
                ]);
            }
        });

        return $ledgers;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete (handles transfer group deletion via reference_id)
    // ──────────────────────────────────────────────────────────────────────────

    public function deleteLedger(Ledger $ledger): void
    {
        if ($ledger->reference_id) {
            // Delete all rows belonging to the same transfer group
            Ledger::where('reference_id', $ledger->reference_id)->delete();
        } else {
            $ledger->delete();
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CSV Import — DompetKu Compatibility
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Import transactions from a DompetKu-compatible CSV file.
     * Format: Kategori, Rekening, Jumlah, Tanggal, Catatan, Tipe
     *
     * @return array{imported: int, total: int, errors: string[]}
     */
    public function importFromCsv(UploadedFile $file): array
    {
        $content  = file_get_contents($file->getRealPath());
        $content  = preg_replace('/\r\n|\r/', "\n", $content); // Normalize line endings

        $lines   = array_filter(explode("\n", trim($content)));
        $lines   = array_values($lines);

        if (count($lines) < 2) {
            return ['imported' => 0, 'total' => 0, 'errors' => ['File CSV kosong atau tidak valid.']];
        }

        $headers = array_map('trim', str_getcsv(array_shift($lines)));

        // Build associative rows
        $rows = [];
        foreach ($lines as $line) {
            $cols = str_getcsv($line);
            if (count($cols) < 6) {
                continue;
            }
            $row = array_combine($headers, array_map('trim', $cols));
            // Clean amount: "5,000,000" → 5000000.00
            $row['Jumlah'] = (float) str_replace(',', '', trim($row['Jumlah'] ?? '0', '"'));
            $rows[] = $row;
        }

        $total           = count($rows);
        $processedIdxs   = [];
        $errors          = [];
        $imported        = 0;

        // ── Step 1: Detect Transfer Pairs ─────────────────────────────────────
        $outRows = [];
        $inRows  = [];

        foreach ($rows as $idx => $row) {
            $cat  = trim($row['Kategori'] ?? '');
            $tipe = trim($row['Tipe'] ?? '');

            if ($cat === 'Kirim Saldo' && $tipe === '-') {
                $outRows[$idx] = $row;
            } elseif ($cat === 'Terima Saldo' && $tipe === '+') {
                $inRows[$idx] = $row;
            }
        }

        $usedInIdxs = [];

        foreach ($outRows as $outIdx => $out) {
            foreach ($inRows as $inIdx => $in) {
                if (in_array($inIdx, $usedInIdxs, true)) {
                    continue;
                }

                if ($out['Jumlah'] === $in['Jumlah'] && $out['Tanggal'] === $in['Tanggal']) {
                    // Matched pair → create TRANSFER
                    try {
                        $refId      = (string) Str::uuid();
                        $outAccount = Account::firstOrCreate(['name' => $out['Rekening']]);
                        $inAccount  = Account::firstOrCreate(['name' => $in['Rekening']]);

                        DB::transaction(function () use ($out, $in, $outAccount, $inAccount, $refId) {
                            Ledger::create([
                                'transaction_type' => TransactionType::TRANSFER_OUT,
                                'account_id'       => $outAccount->id,
                                'reference_id'     => $refId,
                                'amount'           => $out['Jumlah'],
                                'is_mutation_in'   => false,
                                'transaction_date' => $out['Tanggal'],
                                'note'             => $out['Catatan'] ?: null,
                            ]);

                            Ledger::create([
                                'transaction_type' => TransactionType::TRANSFER_IN,
                                'account_id'       => $inAccount->id,
                                'reference_id'     => $refId,
                                'amount'           => $in['Jumlah'],
                                'is_mutation_in'   => true,
                                'transaction_date' => $in['Tanggal'],
                                'note'             => $in['Catatan'] ?: null,
                            ]);
                        });

                        $imported += 2;
                    } catch (\Throwable $e) {
                        $errors[] = 'Transfer row error: '.$e->getMessage();
                    }

                    $processedIdxs[] = $outIdx;
                    $processedIdxs[] = $inIdx;
                    $usedInIdxs[]    = $inIdx;
                    break;
                }
            }
        }

        // ── Step 2: Process Remaining Regular Rows ────────────────────────────
        foreach ($rows as $idx => $row) {
            if (in_array($idx, $processedIdxs, true)) {
                continue;
            }

            try {
                $categoryName = trim($row['Kategori'] ?? '');
                $tipe         = trim($row['Tipe'] ?? '+');
                $isMutationIn = $tipe === '+';

                // Special category-to-type mapping
                [$type, $categoryModel] = $this->mapCategoryToType($categoryName, $isMutationIn);

                $account = Account::firstOrCreate(['name' => trim($row['Rekening'])]);

                Ledger::create([
                    'transaction_type' => $type,
                    'account_id'       => $account->id,
                    'category_id'      => $categoryModel?->id,
                    'amount'           => $row['Jumlah'],
                    'is_mutation_in'   => $type->isMutationIn(),
                    'transaction_date' => $row['Tanggal'],
                    'note'             => $row['Catatan'] ?: null,
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$idx}: ".$e->getMessage();
            }
        }

        return ['imported' => $imported, 'total' => $total, 'errors' => $errors];
    }

    /**
     * Map a DompetKu category name + tipe to a TransactionType and optional Category model.
     *
     * @return array{0: TransactionType, 1: Category|null}
     */
    private function mapCategoryToType(string $categoryName, bool $isMutationIn): array
    {
        // Special mappings from old DompetKu category names
        $specialMap = [
            'Tagih Hutang'         => TransactionType::LOAN_PAYMENT,
            'Terima Bayar Hutang'  => TransactionType::LOAN_PAYMENT,
            'Beri Pinjaman'        => TransactionType::LOAN,
            'Hutang'               => TransactionType::DEBT,
            'Bayar Hutang'         => TransactionType::DEBT_PAYMENT,
        ];

        if (isset($specialMap[$categoryName])) {
            $type = $specialMap[$categoryName];
            return [$type, null];
        }

        // Regular income/expense
        $type         = $isMutationIn ? TransactionType::INCOME : TransactionType::EXPENSE;
        $categoryType = $isMutationIn ? 'INCOME' : 'EXPENSE';

        $category = Category::firstOrCreate(
            ['name' => $categoryName, 'type' => $categoryType]
        );

        return [$type, $category];
    }
}
