<?php

namespace App\Filament\Resources\PagesResource\Pages;

use App\Filament\Resources\PagesResource;
use App\Models\InstructorBalance;
use App\Models\Payout;
use Filament\Pages\Page;

class FinanceDashboard extends Page
{
    protected static ?string $navigationIcon =
        'heroicon-o-banknotes';

    protected static string $view =
        'filament.pages.finance-dashboard';

    public function getViewData(): array
    {
        return [

            'balances' => InstructorBalance::query()
                ->with('instructor')
                ->latest()
                ->get(),

            'payouts' => Payout::query()
                ->with('instructor')
                ->latest()
                ->take(20)
                ->get(),
        ];
    }
}
