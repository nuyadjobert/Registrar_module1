<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CashierService
{
    /**
     * Check if a student has unpaid enrollments/fines.
     *
     * @param int $studentId
     * @return bool  true = has unpaid, false = all paid
     * @throws \Exception if the Cashier API is unreachable
     */
    public function hasUnpaidFees(int $studentId): bool
    {
        if (!env('CASHIER_ENABLED', false)) {
            return false; // Let caller handle local DB check
        }

        try {
            $cashierApiUrl = rtrim(env('CASHIER_API_URL'), '/') . "/api/payments/check-unpaid/{$studentId}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CASHIER_API_KEY'),
                'Accept' => 'application/json',
            ])->get($cashierApiUrl);

            if ($response->failed()) {
                throw new \Exception('Unable to verify payment status with Cashier API.');
            }

            $data = $response->json();
            return !empty($data['has_unpaid_fines']) && $data['has_unpaid_fines'] === true;

        } catch (\Exception $e) {
            throw new \Exception('Cashier API error: ' . $e->getMessage());
        }
    }
}