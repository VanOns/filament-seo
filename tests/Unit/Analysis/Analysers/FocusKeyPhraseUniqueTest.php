<?php

use Illuminate\Database\Eloquent\Model;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyPhraseUnique;
use VanOns\FilamentSeo\Concerns\HasSeoFields;
use VanOns\FilamentSeo\Contracts\Seoable;

it('skips the uniqueness check when no focus keyphrase is set', function () {
    $record = new class () extends Model implements Seoable {
        use HasSeoFields;

        protected $casts = ['seo' => 'array'];
    };

    expect((new FocusKeyPhraseUnique())->analyse($record))->toBeNull();
});
