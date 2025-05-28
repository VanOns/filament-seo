<?php

namespace VanOns\FilamentSeo;

class SeoImage
{
    public function __construct(
        public string $url,
        public int    $width = 1200,
        public int    $height = 630,
        public string $mimeType = 'image/jpeg',
    ) {}
}
