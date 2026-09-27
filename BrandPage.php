<?php

class BrandPage
{
    public static function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));
        $host = preg_replace('/^www\./', '', $host);
        $host = explode(':', $host)[0];

        return $host;
    }

    public static function mainDomainFromHost(string $host): string
    {
        $host = self::normalizeHost($host);
        $parts = explode('.', $host);

        if (count($parts) < 2) {
            return $host;
        }

        return implode('.', array_slice($parts, -2));
    }

    public static function hash(array $brand): int
    {
        return abs((int) crc32((string) ($brand['slug'] ?? 'brand')));
    }

    public static function latinName(array $brand): string
    {
        $name = trim((string) ($brand['brand'] ?? ''));

        if ($name !== '') {
            return $name;
        }

        $slug = (string) ($brand['slug'] ?? 'brand');

        return ucfirst($slug);
    }

    public static function transliterateToRussian(string $text): string
    {
        $text = strtolower(trim($text));

        $patterns = [
            'sh' => 'ш',
            'ch' => 'ч',
            'th' => 'т',
            'ph' => 'ф',
            'ck' => 'к',
            'qu' => 'кв',
            'oo' => 'у',
            'ee' => 'и',
            'ea' => 'и',
            'ie' => 'ай',
            'ai' => 'ей',
            'ay' => 'ей',
            'oy' => 'ой',
            'ow' => 'оу',
            'ng' => 'нг',
            'online' => 'онлайн',
            'stake' => 'стейк',
            'casi' => 'кази',
        ];

        foreach ($patterns as $en => $ru) {
            $text = str_replace($en, $ru, $text);
        }

        $map = [
            'a' => 'а',
            'b' => 'б',
            'c' => 'к',
            'd' => 'д',
            'e' => 'е',
            'f' => 'ф',
            'g' => 'г',
            'h' => 'х',
            'i' => 'и',
            'j' => 'дж',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'q' => 'к',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'у',
            'v' => 'в',
            'w' => 'в',
            'x' => 'кс',
            'y' => 'й',
            'z' => 'з',
        ];

        foreach ($map as $en => $ru) {
            $text = str_replace($en, $ru, $text);
        }

        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    public static function cyrillicName(array $brand): string
    {
        $slug = (string) ($brand['slug'] ?? '');

        return self::transliterateToRussian($slug);
    }

    public static function category(int $hash): string
    {
        $map = ['top', 'new', 'mobile', 'live'];

        return $map[$hash % count($map)];
    }

    public static function facts(int $hash): array
    {
        $licenses = [
            'Curacao',
            'Malta',
            'Isle of Man',
            'Kahnawake',
        ];

        $speedMap = [
            'Высокая',
            'Средняя',
            'Очень высокая',
        ];

        return [
            'year' => 2000 + ($hash % 24),
            'license' => $licenses[$hash % count($licenses)],
            'min_deposit' => 5 + ($hash % 50),
            'bonus' => 100 + ($hash % 400),
            'support' => '24/7',
            'mobile' => 'Да',
            'speed' => $speedMap[$hash % count($speedMap)],
            'rating_value' => number_format(
                4 + (($hash % 10) / 10),
                1,
                '.',
                ''
            ),
            'rating_count' => 100 + ($hash % 500),
        ];
    }

    public static function theme(int $hash): array
    {
        $themes = [
            [
                'name' => 'violet',
                'primary' => '#6C5CE7',
                'accent' => '#A29BFE',
                'bg' => '#F4F6FB',
                'surface' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            [
                'name' => 'emerald',
                'primary' => '#0F9D58',
                'accent' => '#34D399',
                'bg' => '#F3FBF7',
                'surface' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            [
                'name' => 'amber',
                'primary' => '#D97706',
                'accent' => '#FBBF24',
                'bg' => '#FFF8EB',
                'surface' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            [
                'name' => 'slate',
                'primary' => '#334155',
                'accent' => '#64748B',
                'bg' => '#F8FAFC',
                'surface' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            [
                'name' => 'rose',
                'primary' => '#E11D48',
                'accent' => '#FB7185',
                'bg' => '#FFF1F2',
                'surface' => '#FFFFFF',
                'text' => '#1F2937',
            ],
        ];

        return $themes[$hash % count($themes)];
    }

    public static function faviconUrl(array $brand): string
    {
        $slug = strtolower((string) ($brand['slug'] ?? ''));

        return "https://www.google.com/s2/favicons?sz=64&domain={$slug}.com";
    }

    public static function logoUrl(array $brand): string
    {
        $slug = strtolower((string) ($brand['slug'] ?? ''));

        return "https://logo.clearbit.com/{$slug}.com?size=300";
    }

    public static function seo(
        array $brand,
        string $mainDomain,
        string $page = 'home'
    ): array {
        $latin = self::latinName($brand);
        $ru = self::cyrillicName($brand);
        $slug = (string) ($brand['slug'] ?? '');
        $year = date('Y');

        switch ($page) {
            case 'review':
                return [
                    'title' => "{$latin} — обзор, лицензия и особенности в {$year} году",
                    'description' => "{$latin} ({$ru}) — подробный обзор платформы: регистрация, безопасность, мобильная версия, платежи, лицензия и основные особенности сервиса.",
                    'canonical' => "https://{$slug}.{$mainDomain}/review",
                ];

            case 'bonus':
                return [
                    'title' => "{$latin} — бонусы и условия активации в {$year} году",
                    'description' => "{$latin} ({$ru}) — информация о бонусах, приветственном предложении, условиях получения и ключевых особенностях платформы.",
                    'canonical' => "https://{$slug}.{$mainDomain}/bonus",
                ];

            case 'mirror':
                return [
                    'title' => "{$latin} — зеркало и доступ к платформе в {$year} году",
                    'description' => "{$latin} ({$ru}) — информация об альтернативном доступе, безопасности, мобильной версии и особенностях использования платформы.",
                    'canonical' => "https://{$slug}.{$mainDomain}/mirror",
                ];

            default:
                return [
                    'title' => "{$latin} — обзор платформы, бонусы, игры и особенности в {$year} году",
                    'description' => "{$latin} ({$ru}) — обзор платформы: бонусы, игры, мобильная версия, регистрация, платежи, безопасность и FAQ.",
                    'canonical' => "https://{$slug}.{$mainDomain}/",
                ];
        }
    }

    /**
     * Validates a configured ASCII domain, not its ownership.
     * Pass a domain from trusted site configuration or verified routing.
     */
    public static function normalizeVariantDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = rtrim($domain, '.');

        if (
            $domain === ''
            || strlen($domain) > 253
            || filter_var($domain, FILTER_VALIDATE_IP) !== false
            || filter_var(
                $domain,
                FILTER_VALIDATE_DOMAIN,
                FILTER_FLAG_HOSTNAME
            ) === false
        ) {
            throw new InvalidArgumentException(
                'A valid configured domain is required for text variants.'
            );
        }

        return $domain;
    }

    /**
     * The existing visual hash remains unchanged.
     * Full domain names are retained, including subdomains.
     */
    public static function textContext(
        array $brand,
        string $configuredDomain,
        string $page = 'home'
    ): string {
        $domain = self::normalizeVariantDomain($configuredDomain);
        $slug = trim((string) ($brand['slug'] ?? ''));

        if ($slug === '') {
            $slug = self::latinName($brand);
        }

        $page = trim($page);

        if ($page === '') {
            throw new InvalidArgumentException(
                'A page identifier is required for text variants.'
            );
        }

        $identity = [
            'brand-main-text-v1',
            $domain,
            $slug,
            $page,
        ];

        $encoded = json_encode(
            $identity,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($encoded === false) {
            throw new InvalidArgumentException(
                'The text context contains invalid encoding.'
            );
        }

        return hash('sha256', $encoded);
    }

    /**
     * A pure deterministic choice needs no writable cache or file locks.
     * Keep block keys and variant order stable across deployments.
     */
    public static function textVariantIndex(
        string $context,
        string $blockKey,
        int $count = 20
    ): int {
        if ($context === '' || trim($blockKey) === '') {
            throw new InvalidArgumentException(
                'Text context and block key must not be empty.'
            );
        }

        if ($count < 1 || $count > 10000) {
            throw new InvalidArgumentException(
                'Variant count must be between 1 and 10000.'
            );
        }

        $digest = hash(
            'sha256',
            $context . "\0" . $blockKey,
            true
        );

        // Byte-wise reduction also works on 32-bit PHP.
        $index = 0;

        for ($i = 0, $length = strlen($digest); $i < $length; $i++) {
            $index = (($index * 256) + ord($digest[$i])) % $count;
        }

        return $index;
    }

    /**
     * Returns plain text. Escape it at the HTML output boundary.
     */
    public static function textVariant(
        string $context,
        string $blockKey,
        array $variants,
        array $variables = []
    ): string {
        if (count($variants) !== 20) {
            throw new InvalidArgumentException(
                'Block "' . $blockKey . '" must contain exactly 20 variants.'
            );
        }

        $variants = array_values($variants);

        foreach ($variants as $variant) {
            if (!is_string($variant) || trim($variant) === '') {
                throw new InvalidArgumentException(
                    'Block "' . $blockKey . '" contains an invalid variant.'
                );
            }
        }

        $index = self::textVariantIndex(
            $context,
            $blockKey,
            count($variants)
        );

        $replace = [];

        foreach ($variables as $name => $value) {
            if (!is_scalar($value) && $value !== null) {
                throw new InvalidArgumentException(
                    'Text variables must contain scalar values or null.'
                );
            }

            $replace['{' . (string) $name . '}'] = (string) $value;
        }

        return strtr($variants[$index], $replace);
    }

    /**
     * Resolves a dictionary of independently keyed text blocks.
     */
    public static function textVariantMap(
        string $context,
        array $blocks,
        array $variables = []
    ): array {
        $result = [];

        foreach ($blocks as $blockKey => $variants) {
            if (!is_string($blockKey) || !is_array($variants)) {
                throw new InvalidArgumentException(
                    'Text blocks must be named arrays of variants.'
                );
            }

            $result[$blockKey] = self::textVariant(
                $context,
                $blockKey,
                $variants,
                $variables
            );
        }

        return $result;
    }

    public static function build(
        array $brand,
        string $host,
        string $page = 'home'
    ): array {
        $mainDomain = self::mainDomainFromHost($host);
        $hash = self::hash($brand);

        return [
            'main_domain' => $mainDomain,
            'hash' => $hash,
            'brand_latin' => self::latinName($brand),
            'brand_cyrillic' => self::cyrillicName($brand),
            'category' => self::category($hash),
            'facts' => self::facts($hash),
            'theme' => self::theme($hash),
            'seo' => self::seo($brand, $mainDomain, $page),
            'favicon_url' => self::faviconUrl($brand),
            'logo_url' => self::logoUrl($brand),
        ];
    }
}
