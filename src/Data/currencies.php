<?php

declare(strict_types=1);

/**
 * Bundled ISO 4217 currency data.
 *
 * Format per entry: [numericCode, name, scale, symbol]
 *   numericCode — zero-padded 3-digit ISO 4217 numeric code (string)
 *   name        — ISO 4217 English name
 *   scale       — number of minor-unit decimal places (0, 2, 3, or 4)
 *   symbol      — commonly used currency symbol
 *
 * @return array<string, array{0: string, 1: string, 2: int, 3: string}>
 */
return [
    // A
    'AED' => ['784', 'UAE Dirham', 2, 'د.إ'],
    'AFN' => ['971', 'Afghan Afghani', 2, '؋'],
    'ALL' => ['008', 'Albanian Lek', 2, 'L'],
    'AMD' => ['051', 'Armenian Dram', 2, '֏'],
    'ANG' => ['532', 'Netherlands Antillean Guilder', 2, 'ƒ'],
    'AOA' => ['973', 'Angolan Kwanza', 2, 'Kz'],
    'ARS' => ['032', 'Argentine Peso', 2, '$'],
    'AUD' => ['036', 'Australian Dollar', 2, 'A$'],
    'AWG' => ['533', 'Aruban Florin', 2, 'ƒ'],
    'AZN' => ['944', 'Azerbaijani Manat', 2, '₼'],
    // B
    'BAM' => ['977', 'Bosnia-Herzegovina Convertible Mark', 2, 'KM'],
    'BBD' => ['052', 'Barbadian Dollar', 2, 'Bds$'],
    'BDT' => ['050', 'Bangladeshi Taka', 2, '৳'],
    'BGN' => ['975', 'Bulgarian Lev', 2, 'лв'],
    'BHD' => ['048', 'Bahraini Dinar', 3, 'BD'],
    'BMD' => ['060', 'Bermudian Dollar', 2, '$'],
    'BND' => ['096', 'Brunei Dollar', 2, 'B$'],
    'BOB' => ['068', 'Bolivian Boliviano', 2, 'Bs.'],
    'BRL' => ['986', 'Brazilian Real', 2, 'R$'],
    'BSD' => ['044', 'Bahamian Dollar', 2, 'B$'],
    'BTN' => ['064', 'Bhutanese Ngultrum', 2, 'Nu'],
    'BWP' => ['072', 'Botswana Pula', 2, 'P'],
    'BYN' => ['933', 'Belarusian Ruble', 2, 'Br'],
    'BZD' => ['084', 'Belize Dollar', 2, 'BZ$'],
    // C
    'CAD' => ['124', 'Canadian Dollar', 2, 'CA$'],
    'CDF' => ['976', 'Congolese Franc', 2, 'FC'],
    'CHF' => ['756', 'Swiss Franc', 2, 'CHF'],
    'CLF' => ['990', 'Unidad de Fomento', 4, 'CLF'],
    'CLP' => ['152', 'Chilean Peso', 0, '$'],
    'CNY' => ['156', 'Chinese Yuan', 2, '¥'],
    'COP' => ['170', 'Colombian Peso', 2, '$'],
    'CRC' => ['188', 'Costa Rican Colón', 2, '₡'],
    'CUP' => ['192', 'Cuban Peso', 2, '$'],
    'CVE' => ['132', 'Cape Verdean Escudo', 2, '$'],
    'CZK' => ['203', 'Czech Koruna', 2, 'Kč'],
    // D
    'DJF' => ['262', 'Djiboutian Franc', 0, 'Fdj'],
    'DKK' => ['208', 'Danish Krone', 2, 'kr'],
    'DOP' => ['214', 'Dominican Peso', 2, 'RD$'],
    'DZD' => ['012', 'Algerian Dinar', 2, 'دج'],
    // E
    'EGP' => ['818', 'Egyptian Pound', 2, '£'],
    'ERN' => ['232', 'Eritrean Nakfa', 2, 'Nfk'],
    'ETB' => ['230', 'Ethiopian Birr', 2, 'Br'],
    'EUR' => ['978', 'Euro', 2, '€'],
    // F
    'FJD' => ['242', 'Fijian Dollar', 2, 'FJ$'],
    'FKP' => ['238', 'Falkland Islands Pound', 2, '£'],
    // G
    'GBP' => ['826', 'Pound Sterling', 2, '£'],
    'GEL' => ['981', 'Georgian Lari', 2, '₾'],
    'GHS' => ['936', 'Ghanaian Cedi', 2, 'GH₵'],
    'GIP' => ['292', 'Gibraltar Pound', 2, '£'],
    'GMD' => ['270', 'Gambian Dalasi', 2, 'D'],
    'GNF' => ['324', 'Guinean Franc', 0, 'FG'],
    'GTQ' => ['320', 'Guatemalan Quetzal', 2, 'Q'],
    'GYD' => ['328', 'Guyanese Dollar', 2, 'G$'],
    // H
    'HKD' => ['344', 'Hong Kong Dollar', 2, 'HK$'],
    'HNL' => ['340', 'Honduran Lempira', 2, 'L'],
    'HTG' => ['332', 'Haitian Gourde', 2, 'G'],
    'HUF' => ['348', 'Hungarian Forint', 2, 'Ft'],
    // I
    'IDR' => ['360', 'Indonesian Rupiah', 2, 'Rp'],
    'ILS' => ['376', 'Israeli New Shekel', 2, '₪'],
    'INR' => ['356', 'Indian Rupee', 2, '₹'],
    'IQD' => ['368', 'Iraqi Dinar', 3, 'ع.د'],
    'IRR' => ['364', 'Iranian Rial', 2, '﷼'],
    'ISK' => ['352', 'Icelandic Króna', 0, 'kr'],
    // J
    'JMD' => ['388', 'Jamaican Dollar', 2, 'J$'],
    'JOD' => ['400', 'Jordanian Dinar', 3, 'JD'],
    'JPY' => ['392', 'Japanese Yen', 0, '¥'],
    // K
    'KES' => ['404', 'Kenyan Shilling', 2, 'KSh'],
    'KGS' => ['417', 'Kyrgyzstani Som', 2, 'с'],
    'KHR' => ['116', 'Cambodian Riel', 2, '៛'],
    'KMF' => ['174', 'Comorian Franc', 0, 'CF'],
    'KPW' => ['408', 'North Korean Won', 2, '₩'],
    'KRW' => ['410', 'South Korean Won', 0, '₩'],
    'KWD' => ['414', 'Kuwaiti Dinar', 3, 'KD'],
    'KYD' => ['136', 'Cayman Islands Dollar', 2, 'CI$'],
    'KZT' => ['398', 'Kazakhstani Tenge', 2, '₸'],
    // L
    'LAK' => ['418', 'Lao Kip', 2, '₭'],
    'LBP' => ['422', 'Lebanese Pound', 2, 'ل.ل'],
    'LKR' => ['144', 'Sri Lankan Rupee', 2, 'Rs'],
    'LRD' => ['430', 'Liberian Dollar', 2, 'L$'],
    'LSL' => ['426', 'Lesotho Loti', 2, 'L'],
    'LYD' => ['434', 'Libyan Dinar', 3, 'LD'],
    // M
    'MAD' => ['504', 'Moroccan Dirham', 2, 'MAD'],
    'MDL' => ['498', 'Moldovan Leu', 2, 'L'],
    'MGA' => ['969', 'Malagasy Ariary', 0, 'Ar'],
    'MKD' => ['807', 'Macedonian Denar', 2, 'ден'],
    'MMK' => ['104', 'Myanmar Kyat', 2, 'K'],
    'MNT' => ['496', 'Mongolian Tögrög', 2, '₮'],
    'MOP' => ['446', 'Macanese Pataca', 2, 'P'],
    'MRU' => ['929', 'Mauritanian Ouguiya', 2, 'UM'],
    'MUR' => ['480', 'Mauritian Rupee', 2, 'Rs'],
    'MVR' => ['462', 'Maldivian Rufiyaa', 2, 'Rf'],
    'MWK' => ['454', 'Malawian Kwacha', 2, 'MK'],
    'MXN' => ['484', 'Mexican Peso', 2, '$'],
    'MYR' => ['458', 'Malaysian Ringgit', 2, 'RM'],
    'MZN' => ['943', 'Mozambican Metical', 2, 'MT'],
    // N
    'NAD' => ['516', 'Namibian Dollar', 2, 'N$'],
    'NGN' => ['566', 'Nigerian Naira', 2, '₦'],
    'NIO' => ['558', 'Nicaraguan Córdoba', 2, 'C$'],
    'NOK' => ['578', 'Norwegian Krone', 2, 'kr'],
    'NPR' => ['524', 'Nepalese Rupee', 2, 'Rs'],
    'NZD' => ['554', 'New Zealand Dollar', 2, 'NZ$'],
    // O
    'OMR' => ['512', 'Omani Rial', 3, 'ر.ع.'],
    // P
    'PAB' => ['590', 'Panamanian Balboa', 2, 'B/.'],
    'PEN' => ['604', 'Peruvian Sol', 2, 'S/.'],
    'PGK' => ['598', 'Papua New Guinean Kina', 2, 'K'],
    'PHP' => ['608', 'Philippine Peso', 2, '₱'],
    'PKR' => ['586', 'Pakistani Rupee', 2, 'Rs'],
    'PLN' => ['985', 'Polish Złoty', 2, 'zł'],
    'PYG' => ['600', 'Paraguayan Guaraní', 0, '₲'],
    // Q
    'QAR' => ['634', 'Qatari Riyal', 2, 'ر.ق'],
    // R
    'RON' => ['946', 'Romanian Leu', 2, 'lei'],
    'RSD' => ['941', 'Serbian Dinar', 2, 'din'],
    'RUB' => ['643', 'Russian Ruble', 2, '₽'],
    'RWF' => ['646', 'Rwandan Franc', 0, 'RF'],
    // S
    'SAR' => ['682', 'Saudi Riyal', 2, 'ر.س'],
    'SBD' => ['090', 'Solomon Islands Dollar', 2, 'SI$'],
    'SCR' => ['690', 'Seychellois Rupee', 2, 'Rs'],
    'SDG' => ['938', 'Sudanese Pound', 2, 'ج.س.'],
    'SEK' => ['752', 'Swedish Krona', 2, 'kr'],
    'SGD' => ['702', 'Singapore Dollar', 2, 'S$'],
    'SHP' => ['654', 'Saint Helena Pound', 2, '£'],
    'SOS' => ['706', 'Somali Shilling', 2, 'Sh'],
    'SRD' => ['968', 'Surinamese Dollar', 2, '$'],
    'STN' => ['930', 'São Tomé and Príncipe Dobra', 2, 'Db'],
    'SVC' => ['222', 'Salvadoran Colón', 2, '₡'],
    'SYP' => ['760', 'Syrian Pound', 2, '£'],
    'SZL' => ['748', 'Swazi Lilangeni', 2, 'L'],
    // T
    'THB' => ['764', 'Thai Baht', 2, '฿'],
    'TJS' => ['972', 'Tajikistani Somoni', 2, 'SM'],
    'TMT' => ['934', 'Turkmenistan Manat', 2, 'T'],
    'TND' => ['788', 'Tunisian Dinar', 3, 'DT'],
    'TOP' => ['776', 'Tongan Paʻanga', 2, 'T$'],
    'TRY' => ['949', 'Turkish Lira', 2, '₺'],
    'TTD' => ['780', 'Trinidad and Tobago Dollar', 2, 'TT$'],
    'TWD' => ['901', 'New Taiwan Dollar', 2, 'NT$'],
    'TZS' => ['834', 'Tanzanian Shilling', 2, 'TSh'],
    // U
    'UAH' => ['980', 'Ukrainian Hryvnia', 2, '₴'],
    'UGX' => ['800', 'Ugandan Shilling', 0, 'USh'],
    'USD' => ['840', 'US Dollar', 2, '$'],
    'UYU' => ['858', 'Uruguayan Peso', 2, '$U'],
    'UZS' => ['860', 'Uzbekistani Som', 2, 'soʻm'],
    // V
    'VES' => ['928', 'Venezuelan Bolívar Soberano', 2, 'Bs.S'],
    'VND' => ['704', 'Vietnamese Đồng', 0, '₫'],
    'VUV' => ['548', 'Vanuatu Vatu', 0, 'Vt'],
    // W
    'WST' => ['882', 'Samoan Tālā', 2, 'T'],
    // X
    'XAF' => ['950', 'CFA Franc BEAC', 0, 'FCFA'],
    'XCD' => ['951', 'East Caribbean Dollar', 2, 'EC$'],
    'XOF' => ['952', 'CFA Franc BCEAO', 0, 'CFA'],
    'XPF' => ['953', 'CFP Franc', 0, 'Fr'],
    // Y
    'YER' => ['886', 'Yemeni Rial', 2, '﷼'],
    // Z
    'ZAR' => ['710', 'South African Rand', 2, 'R'],
    'ZMW' => ['967', 'Zambian Kwacha', 2, 'ZK'],
];
