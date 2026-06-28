<?php

namespace App\Helpers;

class CountryHelper
{
    public static $countries = [
        'in' => ['name_ta' => 'இந்தியா', 'name_en' => 'India'],
        'us' => ['name_ta' => 'அமெரிக்கா', 'name_en' => 'United States'],
        'lk' => ['name_ta' => 'இலங்கை', 'name_en' => 'Sri Lanka'],
        'my' => ['name_ta' => 'மலேசியா', 'name_en' => 'Malaysia'],
        'sg' => ['name_ta' => 'சிங்கப்பூர்', 'name_en' => 'Singapore'],
        'ca' => ['name_ta' => 'கனடா', 'name_en' => 'Canada'],
        'gb' => ['name_ta' => 'ஐக்கிய இராச்சியம்', 'name_en' => 'United Kingdom'],
        'au' => ['name_ta' => 'ஆஸ்திரேலியா', 'name_en' => 'Australia'],
        'fr' => ['name_ta' => 'பிரான்ஸ்', 'name_en' => 'France'],
        'de' => ['name_ta' => 'ஜெர்மனி', 'name_en' => 'Germany'],
        'jp' => ['name_ta' => 'ஜப்பான்', 'name_en' => 'Japan'],
        'ch' => ['name_ta' => 'சுவிட்சர்லாந்து', 'name_en' => 'Switzerland'],
        'it' => ['name_ta' => 'இத்தாலி', 'name_en' => 'Italy'],
        'es' => ['name_ta' => 'ஸ்பெயின்', 'name_en' => 'Spain'],
        'ru' => ['name_ta' => 'ரஷ்யா', 'name_en' => 'Russia'],
        'za' => ['name_ta' => 'தென்னாப்பிரிக்கா', 'name_en' => 'South Africa'],
        'cn' => ['name_ta' => 'சீனா', 'name_en' => 'China'],
        'ae' => ['name_ta' => 'ஐக்கிய அரபு அமீரகம்', 'name_en' => 'United Arab Emirates'],
        'th' => ['name_ta' => 'தாய்லாந்து', 'name_en' => 'Thailand'],
        'eg' => ['name_ta' => 'எகிப்து', 'name_en' => 'Egypt'],
        'br' => ['name_ta' => 'பிரேசில்', 'name_en' => 'Brazil'],
        'nl' => ['name_ta' => 'நெதர்லாந்து', 'name_en' => 'Netherlands'],
        'se' => ['name_ta' => 'சுவீடன்', 'name_en' => 'Sweden'],
        'no' => ['name_ta' => 'நார்வே', 'name_en' => 'Norway'],
        'nz' => ['name_ta' => 'நியூசிலாந்து', 'name_en' => 'New Zealand'],
        'sa' => ['name_ta' => 'சவுதி அரேபியா', 'name_en' => 'Saudi Arabia'],
        'id' => ['name_ta' => 'இந்தோனேசியா', 'name_en' => 'Indonesia'],
        'vn' => ['name_ta' => 'வியட்நாம்', 'name_en' => 'Vietnam'],
        'tr' => ['name_ta' => 'துருக்கி', 'name_en' => 'Turkey'],
        'mv' => ['name_ta' => 'மாலத்தீவு', 'name_en' => 'Maldives'],
        'mx' => ['name_ta' => 'மெக்சிகோ', 'name_en' => 'Mexico'],
        'pk' => ['name_ta' => 'பாகிஸ்தான்', 'name_en' => 'Pakistan'],
        'bd' => ['name_ta' => 'வங்காளதேசம்', 'name_en' => 'Bangladesh'],
        'np' => ['name_ta' => 'நேபாளம்', 'name_en' => 'Nepal'],
        'om' => ['name_ta' => 'ஓமன்', 'name_en' => 'Oman'],
        'qa' => ['name_ta' => 'கத்தார்', 'name_en' => 'Qatar'],
        'kw' => ['name_ta' => 'குவைத்', 'name_en' => 'Kuwait'],
        'bh' => ['name_ta' => 'பஹ்ரைன்', 'name_en' => 'Bahrain'],
        'ie' => ['name_ta' => 'அயர்லாந்து', 'name_en' => 'Ireland'],
        'be' => ['name_ta' => 'பெல்ஜியம்', 'name_en' => 'Belgium'],
        'dk' => ['name_ta' => 'டெンமார்க்', 'name_en' => 'Denmark'],
        'fi' => ['name_ta' => 'பின்லாந்து', 'name_en' => 'Finland'],
        'gr' => ['name_ta' => 'கிரேக்கம்', 'name_en' => 'Greece'],
        'pl' => ['name_ta' => 'போலந்து', 'name_en' => 'Poland'],
        'pt' => ['name_ta' => 'போர்த்துகல்', 'name_en' => 'Portugal'],
        'kr' => ['name_ta' => 'தென் கொரியா', 'name_en' => 'South Korea'],
        'ke' => ['name_ta' => 'கென்யா', 'name_en' => 'Kenya'],
        'mu' => ['name_ta' => 'மொரிஷியஸ்', 'name_en' => 'Mauritius'],
        'sc' => ['name_ta' => 'சீஷெல்ஸ்', 'name_en' => 'Seychelles'],
    ];

    public static function getCountries(): array
    {
        return self::$countries;
    }

    public static function has($code): bool
    {
        if (empty($code)) {
            return false;
        }
        return isset(self::$countries[strtolower($code)]);
    }

    public static function getName($code): string
    {
        $code = strtolower($code);
        if (isset(self::$countries[$code])) {
            return self::$countries[$code]['name_ta'] . ' / ' . self::$countries[$code]['name_en'];
        }
        return strtoupper($code);
    }

    public static function getNameTa($code): string
    {
        $code = strtolower($code);
        return self::$countries[$code]['name_ta'] ?? strtoupper($code);
    }

    public static function getNameEn($code): string
    {
        $code = strtolower($code);
        return self::$countries[$code]['name_en'] ?? strtoupper($code);
    }
}
