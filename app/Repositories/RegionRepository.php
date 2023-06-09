<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Repositories;

use App\Models\Region\RegionCountry;
use App\Models\Region\RegionCity;

class RegionRepository
{

    public static function groups()
    {
        //地區資訊
        $rows = RegionCountry::query()
                ->orderBy('name')
                ->where('country_code', 'USA')
                ->with([
                    'states' => function($query) {
                        $query->orderBy('state');
                        $query->select('state', 'state_code', 'cn_state', 'country_code');
                        $query->with('cities:city,city_code,cn_city,state_code');
                    }
                ])
                ->select('name', 'cname', 'country_code')
                ->get();
        return $rows;
    }

    public static function getCountry($country_code)
    {
        return self::countries()[$country_code] ?? null;
    }

    public static function getState($country_code, $state_code)
    {
        return self::states()[$country_code][$state_code] ?? null;
    }

    /**
     * 計算稅收費用
     * @param type $amount 金額
     * @param type $country_code 國家
     */
    public static function getTaxFee($amount, $country_code = null)
    {
        if (!$country_code || $amount <= 0)
        {
            return 0;
        }
        $model = RegionCountry::where('iso2', $country_code)->first();
        $tax_config = $model->more['tax_config'] ?? null;
        if (!$tax_config)
        {
            return 0;
        }
        $rate = $tax_config['rate'] ?? 0;
        $order_amount = $tax_config['order_amount'] ?? 0;
        if ($amount < $order_amount)
        {
            return 0;
        }
        $fee = $amount * floatval($rate);
        if ($fee < 0)
        {
            return 0;
        }
        return AppRepository::dedimalTruncate($fee);
    }

    /**
     * 國家簡碼 => 國家名稱
     * @return type
     */
    public static function countries()
    {
        $rows = RegionCountry::query()->orderBy('en_name')->get();
        $data = [];
        foreach ($rows as $row)
        {
            $data[$row->iso2] = $row->en_name;
        }
        return $data;
    }

    /**
     * 國家簡碼 => [
     *      省/市簡碼 => 省/市名稱
     * ]
     * @param type $country_code
     * @return type
     */
    public static function states($country_code = null)
    {
        $data = [
            'US' => [
                'AL' => 'Alabama',
                'AK' => 'Alaska',
                'AS' => 'American Samoa',
                'AZ' => 'Arizona',
                'AR' => 'Arkansas',
                'AF' => 'Armed Forces Africa',
                'AA' => 'Armed Forces Americas',
                'AC' => 'Armed Forces Canada',
                'AE' => 'Armed Forces Europe',
                'AM' => 'Armed Forces Middle East',
                'AP' => 'Armed Forces Pacific',
                'CA' => 'California',
                'CO' => 'Colorado',
                'CT' => 'Connecticut',
                'DE' => 'Delaware',
                'DC' => 'District of Columbia',
                'FM' => 'Federated States Of Micronesia',
                'FL' => 'Florida',
                'GA' => 'Georgia',
                'GU' => 'Guam',
                'HI' => 'Hawaii',
                'ID' => 'Idaho',
                'IL' => 'Illinois',
                'IN' => 'Indiana',
                'IA' => 'Iowa',
                'KS' => 'Kansas',
                'KY' => 'Kentucky',
                'LA' => 'Louisiana',
                'ME' => 'Maine',
                'MH' => 'Marshall Islands',
                'MD' => 'Maryland',
                'MA' => 'Massachusetts',
                'MI' => 'Michigan',
                'MN' => 'Minnesota',
                'MS' => 'Mississippi',
                'MO' => 'Missouri',
                'MT' => 'Montana',
                'NE' => 'Nebraska',
                'NV' => 'Nevada',
                'NH' => 'New Hampshire',
                'NJ' => 'New Jersey',
                'NM' => 'New Mexico',
                'NY' => 'New York',
                'NC' => 'North Carolina',
                'ND' => 'North Dakota',
                'MP' => 'Northern Mariana Islands',
                'OH' => 'Ohio',
                'OK' => 'Oklahoma',
                'OR' => 'Oregon',
                'PW' => 'Palau',
                'PA' => 'Pennsylvania',
                'PR' => 'Puerto Rico',
                'RI' => 'Rhode Island',
                'SC' => 'South Carolina',
                'SD' => 'South Dakota',
                'TN' => 'Tennessee',
                'TX' => 'Texas',
                'UT' => 'Utah',
                'VT' => 'Vermont',
                'VI' => 'Virgin Islands',
                'VA' => 'Virginia',
                'WA' => 'Washington',
                'WV' => 'West Virginia',
                'WI' => 'Wisconsin',
                'WY' => 'Wyoming',
            ],
            'CA' => [
                'AB' => 'Alberta',
                'BC' => 'British Columbia',
                'MB' => 'Manitoba',
                'NL' => 'Newfoundland and Labrador',
                'NB' => 'New Brunswick',
                'NS' => 'Nova Scotia',
                'NT' => 'Northwest Territories',
                'NU' => 'Nunavut',
                'ON' => 'Ontario',
                'PE' => 'Prince Edward Island',
                'QC' => 'Quebec',
                'SK' => 'Saskatchewan',
                'YT' => 'Yukon Territory',
            ],
            'DE' => [
                'NDS' => 'Niedersachsen',
                'BAW' => 'Baden-Württemberg',
                'BAY' => 'Bayern',
                'BER' => 'Berlin',
                'BRG' => 'Brandenburg',
                'BRE' => 'Bremen',
                'HAM' => 'Hamburg',
                'HES' => 'Hessen',
                'MEC' => 'Mecklenburg-Vorpommern',
                'NRW' => 'Nordrhein-Westfalen',
                'RHE' => 'Rheinland-Pfalz',
                'SAR' => 'Saarland',
                'SAS' => 'Sachsen',
                'SAC' => 'Sachsen-Anhalt',
                'SCN' => 'Schleswig-Holstein',
                'THE' => 'Thüringen',
            ],
            'AT' => [
                'WI' => 'Wien',
                'NO' => 'Niederösterreich',
                'OO' => 'Oberösterreich',
                'SB' => 'Salzburg',
                'KN' => 'Kärnten',
                'ST' => 'Steiermark',
                'TI' => 'Tirol',
                'BL' => 'Burgenland',
                'VB' => 'Voralberg',
            ],
            'CH' => [
                'AG' => 'Aargau',
                'AI' => 'Appenzell Innerrhoden',
                'AR' => 'Appenzell Ausserrhoden',
                'BE' => 'Bern',
                'BL' => 'Basel-Landschaft',
                'BS' => 'Basel-Stadt',
                'FR' => 'Freiburg',
                'GE' => 'Genf',
                'GL' => 'Glarus',
                'GR' => 'Graubünden',
                'JU' => 'Jura',
                'LU' => 'Luzern',
                'NE' => 'Neuenburg',
                'NW' => 'Nidwalden',
                'OW' => 'Obwalden',
                'SG' => 'St. Gallen',
                'SH' => 'Schaffhausen',
                'SO' => 'Solothurn',
                'SZ' => 'Schwyz',
                'TG' => 'Thurgau',
                'TI' => 'Tessin',
                'UR' => 'Uri',
                'VD' => 'Waadt',
                'VS' => 'Wallis',
                'ZG' => 'Zug',
                'ZH' => 'Zürich',
            ],
            'ES' => [
                'A Coruсa' => 'A Coruña',
                'Alava' => 'Alava',
                'Albacete' => 'Albacete',
                'Alicante' => 'Alicante',
                'Almeria' => 'Almeria',
                'Asturias' => 'Asturias',
                'Avila' => 'Avila',
                'Badajoz' => 'Badajoz',
                'Baleares' => 'Baleares',
                'Barcelona' => 'Barcelona',
                'Burgos' => 'Burgos',
                'Caceres' => 'Caceres',
                'Cadiz' => 'Cadiz',
                'Cantabria' => 'Cantabria',
                'Castellon' => 'Castellon',
                'Ceuta' => 'Ceuta',
                'Ciudad Real' => 'Ciudad Real',
                'Cordoba' => 'Cordoba',
                'Cuenca' => 'Cuenca',
                'Girona' => 'Girona',
                'Granada' => 'Granada',
                'Guadalajara' => 'Guadalajara',
                'Guipuzcoa' => 'Guipuzcoa',
                'Huelva' => 'Huelva',
                'Huesca' => 'Huesca',
                'Jaen' => 'Jaen',
                'La Rioja' => 'La Rioja',
                'Las Palmas' => 'Las Palmas',
                'Leon' => 'Leon',
                'Lleida' => 'Lleida',
                'Lugo' => 'Lugo',
                'Madrid' => 'Madrid',
                'Malaga' => 'Malaga',
                'Melilla' => 'Melilla',
                'Murcia' => 'Murcia',
                'Navarra' => 'Navarra',
                'Ourense' => 'Ourense',
                'Palencia' => 'Palencia',
                'Pontevedra' => 'Pontevedra',
                'Salamanca' => 'Salamanca',
                'Santa Cruz de Tenerife' => 'Santa Cruz de Tenerife',
                'Segovia' => 'Segovia',
                'Sevilla' => 'Sevilla',
                'Soria' => 'Soria',
                'Tarragona' => 'Tarragona',
                'Teruel' => 'Teruel',
                'Toledo' => 'Toledo',
                'Valencia' => 'Valencia',
                'Valladolid' => 'Valladolid',
                'Vizcaya' => 'Vizcaya',
                'Zamora' => 'Zamora',
                'Zaragoza' => 'Zaragoza',
            ],
            'FR' => [
                '1' => 'Ain',
                '2' => 'Aisne',
                '3' => 'Allier',
                '4' => 'Alpes-de-Haute-Provence',
                '5' => 'Hautes-Alpes',
                '6' => 'Alpes-Maritimes',
                '7' => 'Ardèche',
                '8' => 'Ardennes',
                '9' => 'Ariège',
                '10' => 'Aube',
                '11' => 'Aude',
                '12' => 'Aveyron',
                '13' => 'Bouches-du-Rhône',
                '14' => 'Calvados',
                '15' => 'Cantal',
                '16' => 'Charente',
                '17' => 'Charente-Maritime',
                '18' => 'Cher',
                '19' => 'Corrèze',
                '2A' => 'Corse-du-Sud',
                '2B' => 'Haute-Corse',
                '21' => 'Côte-d\'Or',
                '22' => 'Côtes-d\'Armor',
                '23' => 'Creuse',
                '24' => 'Dordogne',
                '25' => 'Doubs',
                '26' => 'Drôme',
                '27' => 'Eure',
                '28' => 'Eure-et-Loir',
                '29' => 'Finistère',
                '30' => 'Gard',
                '31' => 'Haute-Garonne',
                '32' => 'Gers',
                '33' => 'Gironde',
                '34' => 'Hérault',
                '35' => 'Ille-et-Vilaine',
                '36' => 'Indre',
                '37' => 'Indre-et-Loire',
                '38' => 'Isère',
                '39' => 'Jura',
                '40' => 'Landes',
                '41' => 'Loir-et-Cher',
                '42' => 'Loire',
                '43' => 'Haute-Loire',
                '44' => 'Loire-Atlantique',
                '45' => 'Loiret',
                '46' => 'Lot',
                '47' => 'Lot-et-Garonne',
                '48' => 'Lozère',
                '49' => 'Maine-et-Loire',
                '50' => 'Manche',
                '51' => 'Marne',
                '52' => 'Haute-Marne',
                '53' => 'Mayenne',
                '54' => 'Meurthe-et-Moselle',
                '55' => 'Meuse',
                '56' => 'Morbihan',
                '57' => 'Moselle',
                '58' => 'Nièvre',
                '59' => 'Nord',
                '60' => 'Oise',
                '61' => 'Orne',
                '62' => 'Pas-de-Calais',
                '63' => 'Puy-de-Dôme',
                '64' => 'Pyrénées-Atlantiques',
                '65' => 'Hautes-Pyrénées',
                '66' => 'Pyrénées-Orientales',
                '67' => 'Bas-Rhin',
                '68' => 'Haut-Rhin',
                '69' => 'Rhône',
                '70' => 'Haute-Saône',
                '71' => 'Saône-et-Loire',
                '72' => 'Sarthe',
                '73' => 'Savoie',
                '74' => 'Haute-Savoie',
                '75' => 'Paris',
                '76' => 'Seine-Maritime',
                '77' => 'Seine-et-Marne',
                '78' => 'Yvelines',
                '79' => 'Deux-Sèvres',
                '80' => 'Somme',
                '81' => 'Tarn',
                '82' => 'Tarn-et-Garonne',
                '83' => 'Var',
                '84' => 'Vaucluse',
                '85' => 'Vendée',
                '86' => 'Vienne',
                '87' => 'Haute-Vienne',
                '88' => 'Vosges',
                '89' => 'Yonne',
                '90' => 'Territoire-de-Belfort',
                '91' => 'Essonne',
                '92' => 'Hauts-de-Seine',
                '93' => 'Seine-Saint-Denis',
                '94' => 'Val-de-Marne',
                '95' => 'Val-d\'Oise',
            ],
            'RO' => [
                'AB' => 'Alba',
                'AR' => 'Arad',
                'AG' => 'Argeş',
                'BC' => 'Bacău',
                'BH' => 'Bihor',
                'BN' => 'Bistriţa-Năsăud',
                'BT' => 'Botoşani',
                'BV' => 'Braşov',
                'BR' => 'Brăila',
                'B' => 'Bucureşti',
                'BZ' => 'Buzău',
                'CS' => 'Caraş-Severin',
                'CL' => 'Călăraşi',
                'CJ' => 'Cluj',
                'CT' => 'Constanţa',
                'CV' => 'Covasna',
                'DB' => 'Dâmboviţa',
                'DJ' => 'Dolj',
                'GL' => 'Galaţi',
                'GR' => 'Giurgiu',
                'GJ' => 'Gorj',
                'HR' => 'Harghita',
                'HD' => 'Hunedoara',
                'IL' => 'Ialomiţa',
                'IS' => 'Iaşi',
                'IF' => 'Ilfov',
                'MM' => 'Maramureş',
                'MH' => 'Mehedinţi',
                'MS' => 'Mureş',
                'NT' => 'Neamţ',
                'OT' => 'Olt',
                'PH' => 'Prahova',
                'SM' => 'Satu-Mare',
                'SJ' => 'Sălaj',
                'SB' => 'Sibiu',
                'SV' => 'Suceava',
                'TR' => 'Teleorman',
                'TM' => 'Timiş',
                'TL' => 'Tulcea',
                'VS' => 'Vaslui',
                'VL' => 'Vâlcea',
                'VN' => 'Vrancea',
            ],
            'FI' => [
                'Lappi' => 'Lappi',
                'Pohjois-Pohjanmaa' => 'Pohjois-Pohjanmaa',
                'Kainuu' => 'Kainuu',
                'Pohjois-Karjala' => 'Pohjois-Karjala',
                'Pohjois-Savo' => 'Pohjois-Savo',
                'Etelä-Savo' => 'Etelä-Savo',
                'Etelä-Pohjanmaa' => 'Etelä-Pohjanmaa',
                'Pohjanmaa' => 'Pohjanmaa',
                'Pirkanmaa' => 'Pirkanmaa',
                'Satakunta' => 'Satakunta',
                'Keski-Pohjanmaa' => 'Keski-Pohjanmaa',
                'Keski-Suomi' => 'Keski-Suomi',
                'Varsinais-Suomi' => 'Varsinais-Suomi',
                'Etelä-Karjala' => 'Etelä-Karjala',
                'Päijät-Häme' => 'Päijät-Häme',
                'Kanta-Häme' => 'Kanta-Häme',
                'Uusimaa' => 'Uusimaa',
                'Itä-Uusimaa' => 'Itä-Uusimaa',
                'Kymenlaakso' => 'Kymenlaakso',
                'Ahvenanmaa' => 'Ahvenanmaa',
            ],
            'EE' => [
                'EE-37' => 'Harjumaa',
                'EE-39' => 'Hiiumaa',
                'EE-44' => 'Ida-Virumaa',
                'EE-49' => 'Jõgevamaa',
                'EE-51' => 'Järvamaa',
                'EE-57' => 'Läänemaa',
                'EE-59' => 'Lääne-Virumaa',
                'EE-65' => 'Põlvamaa',
                'EE-67' => 'Pärnumaa',
                'EE-70' => 'Raplamaa',
                'EE-74' => 'Saaremaa',
                'EE-78' => 'Tartumaa',
                'EE-82' => 'Valgamaa',
                'EE-84' => 'Viljandimaa',
                'EE-86' => 'Võrumaa',
            ],
            'LV' => [
                'LV-DGV' => 'Daugavpils',
                'LV-JEL' => 'Jelgava',
                'Jēkabpils' => 'Jēkabpils',
                'LV-JUR' => 'Jūrmala',
                'LV-LPX' => 'Liepāja',
                'LV-LE' => 'Liepājas novads',
                'LV-REZ' => 'Rēzekne',
                'LV-RIX' => 'Rīga',
                'LV-RI' => 'Rīgas novads',
                'Valmiera' => 'Valmiera',
                'LV-VEN' => 'Ventspils',
                'Aglonas novads' => 'Aglonas novads',
                'LV-AI' => 'Aizkraukles novads',
                'Aizputes novads' => 'Aizputes novads',
                'Aknīstes novads' => 'Aknīstes novads',
                'Alojas novads' => 'Alojas novads',
                'Alsungas novads' => 'Alsungas novads',
                'LV-AL' => 'Alūksnes novads',
                'Amatas novads' => 'Amatas novads',
                'Apes novads' => 'Apes novads',
                'Auces novads' => 'Auces novads',
                'Babītes novads' => 'Babītes novads',
                'Baldones novads' => 'Baldones novads',
                'Baltinavas novads' => 'Baltinavas novads',
                'LV-BL' => 'Balvu novads',
                'LV-BU' => 'Bauskas novads',
                'Beverīnas novads' => 'Beverīnas novads',
                'Brocēnu novads' => 'Brocēnu novads',
                'Burtnieku novads' => 'Burtnieku novads',
                'Carnikavas novads' => 'Carnikavas novads',
                'Cesvaines novads' => 'Cesvaines novads',
                'Ciblas novads' => 'Ciblas novads',
                'LV-CE' => 'Cēsu novads',
                'Dagdas novads' => 'Dagdas novads',
                'LV-DA' => 'Daugavpils novads',
                'LV-DO' => 'Dobeles novads',
                'Dundagas novads' => 'Dundagas novads',
                'Durbes novads' => 'Durbes novads',
                'Engures novads' => 'Engures novads',
                'Garkalnes novads' => 'Garkalnes novads',
                'Grobiņas novads' => 'Grobiņas novads',
                'LV-GU' => 'Gulbenes novads',
                'Iecavas novads' => 'Iecavas novads',
                'Ikšķiles novads' => 'Ikšķiles novads',
                'Ilūkstes novads' => 'Ilūkstes novads',
                'Inčukalna novads' => 'Inčukalna novads',
                'Jaunjelgavas novads' => 'Jaunjelgavas novads',
                'Jaunpiebalgas novads' => 'Jaunpiebalgas novads',
                'Jaunpils novads' => 'Jaunpils novads',
                'LV-JL' => 'Jelgavas novads',
                'LV-JK' => 'Jēkabpils novads',
                'Kandavas novads' => 'Kandavas novads',
                'Kokneses novads' => 'Kokneses novads',
                'Krimuldas novads' => 'Krimuldas novads',
                'Krustpils novads' => 'Krustpils novads',
                'LV-KR' => 'Krāslavas novads',
                'LV-KU' => 'Kuldīgas novads',
                'Kārsavas novads' => 'Kārsavas novads',
                'Lielvārdes novads' => 'Lielvārdes novads',
                'LV-LM' => 'Limbažu novads',
                'Lubānas novads' => 'Lubānas novads',
                'LV-LU' => 'Ludzas novads',
                'Līgatnes novads' => 'Līgatnes novads',
                'Līvānu novads' => 'Līvānu novads',
                'LV-MA' => 'Madonas novads',
                'Mazsalacas novads' => 'Mazsalacas novads',
                'Mālpils novads' => 'Mālpils novads',
                'Mārupes novads' => 'Mārupes novads',
                'Naukšēnu novads' => 'Naukšēnu novads',
                'Neretas novads' => 'Neretas novads',
                'Nīcas novads' => 'Nīcas novads',
                'LV-OG' => 'Ogres novads',
                'Olaines novads' => 'Olaines novads',
                'Ozolnieku novads' => 'Ozolnieku novads',
                'LV-PR' => 'Preiļu novads',
                'Priekules novads' => 'Priekules novads',
                'Priekuļu novads' => 'Priekuļu novads',
                'Pārgaujas novads' => 'Pārgaujas novads',
                'Pāvilostas novads' => 'Pāvilostas novads',
                'Pļaviņu novads' => 'Pļaviņu novads',
                'Raunas novads' => 'Raunas novads',
                'Riebiņu novads' => 'Riebiņu novads',
                'Rojas novads' => 'Rojas novads',
                'Ropažu novads' => 'Ropažu novads',
                'Rucavas novads' => 'Rucavas novads',
                'Rugāju novads' => 'Rugāju novads',
                'Rundāles novads' => 'Rundāles novads',
                'LV-RE' => 'Rēzeknes novads',
                'Rūjienas novads' => 'Rūjienas novads',
                'Salacgrīvas novads' => 'Salacgrīvas novads',
                'Salas novads' => 'Salas novads',
                'Salaspils novads' => 'Salaspils novads',
                'LV-SA' => 'Saldus novads',
                'Saulkrastu novads' => 'Saulkrastu novads',
                'Siguldas novads' => 'Siguldas novads',
                'Skrundas novads' => 'Skrundas novads',
                'Skrīveru novads' => 'Skrīveru novads',
                'Smiltenes novads' => 'Smiltenes novads',
                'Stopiņu novads' => 'Stopiņu novads',
                'Strenču novads' => 'Strenču novads',
                'Sējas novads' => 'Sējas novads',
                'LV-TA' => 'Talsu novads',
                'LV-TU' => 'Tukuma novads',
                'Tērvetes novads' => 'Tērvetes novads',
                'Vaiņodes novads' => 'Vaiņodes novads',
                'LV-VK' => 'Valkas novads',
                'LV-VM' => 'Valmieras novads',
                'Varakļānu novads' => 'Varakļānu novads',
                'Vecpiebalgas novads' => 'Vecpiebalgas novads',
                'Vecumnieku novads' => 'Vecumnieku novads',
                'LV-VE' => 'Ventspils novads',
                'Viesītes novads' => 'Viesītes novads',
                'Viļakas novads' => 'Viļakas novads',
                'Viļānu novads' => 'Viļānu novads',
                'Vārkavas novads' => 'Vārkavas novads',
                'Zilupes novads' => 'Zilupes novads',
                'Ādažu novads' => 'Ādažu novads',
                'Ērgļu novads' => 'Ērgļu novads',
                'Ķeguma novads' => 'Ķeguma novads',
                'Ķekavas novads' => 'Ķekavas novads',
            ],
            'LT' => [
                'LT-AL' => 'Alytaus Apskritis',
                'LT-KU' => 'Kauno Apskritis',
                'LT-KL' => 'Klaipėdos Apskritis',
                'LT-MR' => 'Marijampolės Apskritis',
                'LT-PN' => 'Panevėžio Apskritis',
                'LT-SA' => 'Šiaulių Apskritis',
                'LT-TA' => 'Tauragės Apskritis',
                'LT-TE' => 'Telšių Apskritis',
                'LT-UT' => 'Utenos Apskritis',
                'LT-VL' => 'Vilniaus Apskritis',
            ],
            'CN' => [
                'BJ' => '北京市',
                'SH' => '上海市',
                'TJ' => '天津市',
                'CQ' => '重慶市',
                'HEB' => '河北省',
                'SAX' => '山西省',
                'LN' => '遼寧省',
                'JL' => '吉林省',
                'HLJ' => '黑龍江省',
                'JS' => '江蘇省',
                'ZJ' => '浙江省',
                'AH' => '安徽省',
                'FJ' => '福建省',
                'JX' => '江西省',
                'SD' => '山東省',
                'HEN' => '河南省',
                'HUB' => '湖北省',
                'HUN' => '湖南省',
                'GD' => '廣東省',
                'HN' => '海南省',
                'SC' => '四川省',
                'HZ' => '貴州省',
                'YN' => '雲南省',
                'SNX' => '陜西省',
                'GS' => '甘肅省',
                'QH' => '青海省',
                'TW' => '臺灣省',
                'GX' => '廣西壯族自治區',
                'NMG' => '內蒙古自治區',
                'XZ' => '西藏自治區',
                'NX' => '寧夏回族自治區',
                'XJ' => '新疆維吾爾自治區',
                'XG' => '香港特別行政區',
            ],
        ];
        if (!$country_code)
        {
            return $data;
        }
        return $data[$country_code] ?? [];
    }

}
