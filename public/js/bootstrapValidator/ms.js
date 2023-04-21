(function($) {
    /**
     * Default English package. It's included in the dist, so you do NOT need to include it to your head tag
     * The only reason I put it here is that you can clone it, and translate it into your language
     */
    $.fn.bootstrapValidator.i18n = $.extend(true, $.fn.bootstrapValidator.i18n, {
        base64: {
            'default': '_Please enter a valid base 64 encoded'
        },
        between: {
            'default': '_Please enter a value between %s and %s',
            notInclusive: '_Please enter a value between %s and %s strictly'
        },
        callback: {
            'default': '_Please enter a valid value'
        },
        choice: {
            'default': '_Please enter a valid value',
            less: '_Please choose %s options at minimum',
            more: '_Please choose %s options at maximum',
            between: '_Please choose %s - %s options'
        },
        color: {
            'default': '_Please enter a valid color'
        },
        creditCard: {
            'default': '_Please enter a valid credit card number'
        },
        cusip: {
            'default': '_Please enter a valid CUSIP number'
        },
        cvv: {
            'default': '_Please enter a valid CVV number'
        },
        date: {
            'default': '_Please enter a valid date',
            min: '_Please enter a date after %s',
            max: '_Please enter a date before %s',
            range: '_Please enter a date in the range %s - %s'
        },
        different: {
            'default': '_Please enter a different value'
        },
        digits: {
             'default': '_Please enter only digits'
        },
        ean: {
            'default': '_Please enter a valid EAN number'
        },
        emailAddress: {
            'default': 'Sila masukkan alamat e-mel yang sah'
        },
        file: {
            'default': '_Please choose a valid file'
        },
        greaterThan: {
            'default': 'Sila masukkan nilai lebih besar atau sama dengan %s',
            notInclusive: '_Please enter a value greater than %s'
        },
        grid: {
            'default': '_Please enter a valid GRId number'
        },
        hex: {
            'default': '_Please enter a valid hexadecimal number'
        },
        hexColor: {
            'default': '_Please enter a valid hex color'
        },
        iban: {
            'default': '_Please enter a valid IBAN number',
            countryNotSupported: '_The country code %s is not supported',
            country: '_Please enter a valid IBAN number in %s',
            countries: {
                AD: 'Andorra',
                AE: 'United Arab Emirates',
                AL: 'Albania',
                AO: 'Angola',
                AT: 'Austria',
                AZ: 'Azerbaijan',
                BA: 'Bosnia and Herzegovina',
                BE: 'Belgium',
                BF: 'Burkina Faso',
                BG: 'Bulgaria',
                BH: 'Bahrain',
                BI: 'Burundi',
                BJ: 'Benin',
                BR: 'Brazil',
                CH: 'Switzerland',
                CI: 'Ivory Coast',
                CM: 'Cameroon',
                CR: 'Costa Rica',
                CV: 'Cape Verde',
                CY: 'Cyprus',
                CZ: 'Czech Republic',
                DE: 'Germany',
                DK: 'Denmark',
                DO: 'Dominican Republic',
                DZ: 'Algeria',
                EE: 'Estonia',
                ES: 'Spain',
                FI: 'Finland',
                FO: 'Faroe Islands',
                FR: 'France',
                GB: 'United Kingdom',
                GE: 'Georgia',
                GI: 'Gibraltar',
                GL: 'Greenland',
                GR: 'Greece',
                GT: 'Guatemala',
                HR: 'Croatia',
                HU: 'Hungary',
                IE: 'Ireland',
                IL: 'Israel',
                IR: 'Iran',
                IS: 'Iceland',
                IT: 'Italy',
                JO: 'Jordan',
                KW: 'Kuwait',
                KZ: 'Kazakhstan',
                LB: 'Lebanon',
                LI: 'Liechtenstein',
                LT: 'Lithuania',
                LU: 'Luxembourg',
                LV: 'Latvia',
                MC: 'Monaco',
                MD: 'Moldova',
                ME: 'Montenegro',
                MG: 'Madagascar',
                MK: 'Macedonia',
                ML: 'Mali',
                MR: 'Mauritania',
                MT: 'Malta',
                MU: 'Mauritius',
                MZ: 'Mozambique',
                NL: 'Netherlands',
                NO: 'Norway',
                PK: 'Pakistan',
                PL: 'Poland',
                PS: 'Palestine',
                PT: 'Portugal',
                QA: 'Qatar',
                RO: 'Romania',
                RS: 'Serbia',
                SA: 'Saudi Arabia',
                SE: 'Sweden',
                SI: 'Slovenia',
                SK: 'Slovakia',
                SM: 'San Marino',
                SN: 'Senegal',
                TN: 'Tunisia',
                TR: 'Turkey',
                VG: 'Virgin Islands, British'
            }
        },
        id: {
            'default': '_Please enter a valid identification number',
            countryNotSupported: '_The country code %s is not supported',
            country: '_Please enter a valid identification number in %s',
            countries: {
                BA: 'Bosnia and Herzegovina',
                BG: 'Bulgaria',
                BR: 'Brazil',
                CH: 'Switzerland',
                CL: 'Chile',
                CN: 'China',
                CZ: 'Czech Republic',
                DK: 'Denmark',
                EE: 'Estonia',
                ES: 'Spain',
                FI: 'Finland',
                HR: 'Croatia',
                IE: 'Ireland',
                IS: 'Iceland',
                LT: 'Lithuania',
                LV: 'Latvia',
                ME: 'Montenegro',
                MK: 'Macedonia',
                NL: 'Netherlands',
                RO: 'Romania',
                RS: 'Serbia',
                SE: 'Sweden',
                SI: 'Slovenia',
                SK: 'Slovakia',
                SM: 'San Marino',
                TH: 'Thailand',
                ZA: 'South Africa'
            }
        },
        identical: {
            'default': '_Please enter the same value'
        },
        imei: {
            'default': '_Please enter a valid IMEI number'
        },
        imo: {
            'default': '_Please enter a valid IMO number'
        },
        integer: {
            'default': '_Please enter a valid number'
        },
        ip: {
            'default': '_Please enter a valid IP address',
            ipv4: '_Please enter a valid IPv4 address',
            ipv6: '_Please enter a valid IPv6 address'
        },
        isbn: {
            'default': '_Please enter a valid ISBN number'
        },
        isin: {
            'default': '_Please enter a valid ISIN number'
        },
        ismn: {
            'default': '_Please enter a valid ISMN number'
        },
        issn: {
            'default': '_Please enter a valid ISSN number'
        },
        lessThan: {
            'default': '_Please enter a value less than or equal to %s',
            notInclusive: '_Please enter a value less than %s'
        },
        mac: {
            'default': '_Please enter a valid MAC address'
        },
        meid: {
            'default': '_Please enter a valid MEID number'
        },
        notEmpty: {
            'default': 'Sila masukkan nilai'
        },
        numeric: {
            'default': '_Please enter a valid float number'
        },
        phone: {
            'default': '_Please enter a valid phone number',
            countryNotSupported: '_The country code %s is not supported',
            country: '_Please enter a valid phone number in %s',
            countries: {
                BR: 'Brazil',
                CN: 'China',
                CZ: 'Czech Republic',
                DE: 'Germany',
                DK: 'Denmark',
                ES: 'Spain',
                FR: 'France',
                GB: 'United Kingdom',
                MA: 'Morocco',
                PK: 'Pakistan',
                RO: 'Romania',
                RU: 'Russia',
                SK: 'Slovakia',
                TH: 'Thailand',
                US: 'USA',
                VE: 'Venezuela'
            }
        },
        regexp: {
            'default': '_Please enter a value matching the pattern'
        },
        remote: {
            'default': '_Please enter a valid value'
        },
        rtn: {
            'default': '_Please enter a valid RTN number'
        },
        sedol: {
            'default': '_Please enter a valid SEDOL number'
        },
        siren: {
            'default': '_Please enter a valid SIREN number'
        },
        siret: {
            'default': '_Please enter a valid SIRET number'
        },
        step: {
            'default': '_Please enter a valid step of %s'
        },
        stringCase: {
            'default': '_Please enter only lowercase characters',
            upper: '_Please enter only uppercase characters'
        },
        stringLength: {
            'default': '_Please enter a value with valid length',
            less: 'Sila masukkan kurang daripada %s aksara',
            more: '_Please enter more than %s characters',
            between: '_Please enter value between %s and %s characters long'
        },
        uri: {
            'default': 'Sila masukkan URL yang sah'
        },
        uuid: {
            'default': '_Please enter a valid UUID number',
            version: '_Please enter a valid UUID version %s number'
        },
        vat: {
            'default': '_Please enter a valid VAT number',
            countryNotSupported: '_The country code %s is not supported',
            country: '_Please enter a valid VAT number in %s',
            countries: {
                AT: 'Austria',
                BE: 'Belgium',
                BG: 'Bulgaria',
                BR: 'Brazil',
                CH: 'Switzerland',
                CY: 'Cyprus',
                CZ: 'Czech Republic',
                DE: 'Germany',
                DK: 'Denmark',
                EE: 'Estonia',
                ES: 'Spain',
                FI: 'Finland',
                FR: 'France',
                GB: 'United Kingdom',
                GR: 'Greece',
                EL: 'Greece',
                HU: 'Hungary',
                HR: 'Croatia',
                IE: 'Ireland',
                IS: 'Iceland',
                IT: 'Italy',
                LT: 'Lithuania',
                LU: 'Luxembourg',
                LV: 'Latvia',
                MT: 'Malta',
                NL: 'Netherlands',
                NO: 'Norway',
                PL: 'Poland',
                PT: 'Portugal',
                RO: 'Romania',
                RU: 'Russia',
                RS: 'Serbia',
                SE: 'Sweden',
                SI: 'Slovenia',
                SK: 'Slovakia',
                VE: 'Venezuela',
                ZA: 'South Africa'
            }
        },
        vin: {
            'default': '_Please enter a valid VIN number'
        },
        zipCode: {
            'default': '_Please enter a valid postal code',
            countryNotSupported: '_The country code %s is not supported',
            country: '_Please enter a valid postal code in %s',
            countries: {
                AT: 'Austria',
                BR: 'Brazil',
                CA: 'Canada',
                CH: 'Switzerland',
                CZ: 'Czech Republic',
                DE: 'Germany',
                DK: 'Denmark',
                FR: 'France',
                GB: 'United Kingdom',
                IE: 'Ireland',
                IT: 'Italy',
                MA: 'Morocco',
                NL: 'Netherlands',
                PT: 'Portugal',
                RO: 'Romania',
                RU: 'Russia',
                SE: 'Sweden',
                SG: 'Singapore',
                SK: 'Slovakia',
                US: 'USA'
            }
        }
    });
}(window.jQuery));
