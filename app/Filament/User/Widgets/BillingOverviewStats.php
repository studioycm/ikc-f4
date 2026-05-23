<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\PaymentsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class BillingOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

//    protected int|string|array $columnSpan = 2;

    public function getColumnSpan(): int|string|array
    {
        return [
            'xs' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 2,
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'xs' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 2,
        ];
    }

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $prevUser = $this->getCurrentPrevUser();
        $prevUserId = $prevUser?->getKey();
        $paymentsQuery = PrevPayment::query()
            ->when(
                blank($prevUserId),
                fn($query) => $query->whereRaw('1 = 0'),
                function ($query) use ($prevUserId) {
                    $query->where('created_by', '=', $prevUserId);
                },
            );

        return [
            Stat::make(__('Payments'), (clone $paymentsQuery)
                ->count())
                ->color('success')
                ->icon(Heroicon::CreditCard)
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Payments this year'), (clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->count())
                ->icon(Heroicon::OutlinedCreditCard)
                ->color('success')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Paid'), number_format((float)((clone $paymentsQuery)
                ->sum('amount')), 0))
                ->icon(Heroicon::Banknotes)
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Paid this year'), number_format((float)((clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->sum('amount')), 0))
                ->icon(Heroicon::OutlinedBanknotes)
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
        ];
    }
}
