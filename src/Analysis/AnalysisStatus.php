<?php

namespace VanOns\FilamentSeo\Analysis;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

enum AnalysisStatus: string
{
    case PASSED = 'passed';
    case WARNING = 'warning';
    case FAILED = 'failed';

    case INFO = 'info';

    public function icon(): Heroicon
    {
        return match($this) {
            AnalysisStatus::PASSED => Heroicon::CheckCircle,
            AnalysisStatus::WARNING => Heroicon::ExclamationCircle,
            AnalysisStatus::FAILED => Heroicon::XCircle,
            AnalysisStatus::INFO => Heroicon::InformationCircle,
        };
    }

    public function color(): array
    {
        return match($this) {
            AnalysisStatus::PASSED => Color::Green,
            AnalysisStatus::WARNING => Color::Yellow,
            AnalysisStatus::FAILED => Color::Red,
            AnalysisStatus::INFO => Color::Blue,
        };
    }
}
