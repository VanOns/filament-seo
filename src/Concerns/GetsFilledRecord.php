<?php

namespace VanOns\FilamentSeo\Concerns;

use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use VanOns\FilamentSeo\Contracts\Seoable;

trait GetsFilledRecord
{
    /**
     * @param Model|null $record
     * @param Get $get
     * @param string $operation
     * @param Component $livewire
     * @return (Model&Seoable)|null
     */
    protected function getFilledRecord(?Model $record, Get $get, string $operation, Component $livewire): ?Model
    {
        if ($operation === 'create' && $livewire instanceof CreateRecord) {
            $record = new ($livewire->getModel());
        }

        if (!$record instanceof Seoable) {
            return null;
        }

        /** @var Model&Seoable $record */
        $clone = clone $record;

        return $clone->fill($get());
    }
}
