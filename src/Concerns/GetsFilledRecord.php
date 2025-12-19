<?php

namespace VanOns\FilamentSeo\Concerns;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\RelationManagers\RelationManager;
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
        } elseif ($operation === 'create' && $livewire instanceof RelationManager) {
            $record = new ($livewire->getOwnerRecord()->{$livewire::getRelationshipName()}()->getQuery()->getModel()::class);
        }

        if (!$record instanceof Seoable) {
            return null;
        }

        /** @var Model&Seoable $record */
        $clone = clone $record;

        $clone->preventSilentlyDiscardingAttributes(false);

        return $clone->fill($get());
    }
}
