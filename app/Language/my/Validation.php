<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// Validation language settings
return [
    // Core Messages
    'noRuleSets'      => 'No rulesets specified in Validation configuration.',
    'ruleNotFound'    => '{0} is not a valid rule.',
    'groupNotFound'   => '{0} is not a validation rules group.',
    'groupNotArray'   => '{0} rule group must be an array.',
    'invalidTemplate' => '{0} is not a valid Validation template.',

    // Rule Messages
    'alpha'                 => '{field} မှာ စာအက္ခရာသာ ပါဝင်နိုင်ပါတယ်။',
    'alpha_dash'            => '{field} မှာ အက္ခရာ၊ ဂဏန်း၊ underscore နှင့် dash များပါဝင်နိုင်ပါတယ်။',
    'alpha_numeric'         => '{field} မှာ အက္ခရာ၊ ဂဏန်းများ ပါဝင်နိုင်ပါတယ်။',
    'alpha_numeric_punct'   => 'The {field} field may contain only alphanumeric characters, spaces, and  ~ ! # $ % & * - _ + = | : . characters.',
    'alpha_numeric_space'   => '{field} မှာ အက္ခရာ၊ ဂဏန်းနှင့် space တို့ ပါဝင်နိုင်ပါတယ်။',
    'alpha_space'           => 'The {field} field may only contain alphabetical characters and spaces.',
    'decimal'               => '{field} မှာ ဒသမကိန်း ပါရပါမယ်။',
    'differs'               => '{field} မှာ {param} နှင့် တူ၍မရပါ။',
    'equals'                => 'The {field} field must be exactly: {param}.',
    'exact_length'          => '{field} ဟာ {param} နဲ့ စာလုံးအရေအတွက် တူညီမှု့ ရှိရပါမယ်။',
    'greater_than'          => '{field} မှာ {param} ထက် ပိုကြီးတဲ့ ကိန်းဂဏန်းတစ်ခု ပါရပါမယ်။',
    'greater_than_equal_to' => '{field} မှာ {param} ထက် ပိုကြီးတဲ့ သို့ တူညီတဲ့ ကိန်းဂဏန်းတစ်ခု ပါရပါမယ်။',
    'hex'                   => 'The {field} field may only contain hexidecimal characters.',
    'in_list'               => '{field} ဟာ {param} ထဲက တစ်ခု ဖြစ်ရပါမယ်။',
    'integer'               => '{field} မှာ ကိန်းပြည့်တစ်ခု ပါရပါမယ်။',
    'is_natural'            => '{field} မှာ ကိန်းဂဏန်းတွေပဲ ပါရပါမယ်။',
    'is_natural_no_zero'    => '{field} မှာ ဂဏန်းတွေပဲ ပါရမှာဖြစ်ပြီး သုညထက် ကြီးရပါမယ်။',
    'is_not_unique'         => 'The {field} field must contain a previously existing value in the database.',
    'is_unique'             => '{field} မှာ တမူထူးခြားတဲ့ တန်ဖိုးတစ်ခု ပါရမယ်။',
    'less_than'             => '{field} မှာ {param} ထက် နည်းတဲ့ ကိန်း ပါရမယ်။',
    'less_than_equal_to'    => '{field} မှာ {param} ထက် ငယ် သို့မဟုတ် ညီတဲ့ ကိန်းတစ်ခု ပါရမယ်။',
    'matches'               => '{field} ဟာ {param} နဲ့ မကိုက်ညီပါ။',
    'max_length'            => '{field} ဟာ {param} ထက် စာလုံးရေ မများရပါ။',
    'min_length'            => '{field} ဟာ အနည်းဆုံး စာလုံးရေ {param} ရှိရပါမယ်။',
    'not_equals'            => 'The {field} field cannot be: {param}.',
    'not_in_list'           => 'The {field} field must not be one of: {param}.',
    'numeric'               => '{field} ဟာ ဂဏန်းတွေသာ ပါရပါမယ်။',
    'regex_match'           => '{field} ဟာ မှန်ကန်တဲ့ အနေအထားမှာ မရှိပါ။',
    'required'              => '{field} ကို ဖြည့်ဖို့ လိုအပ်ပါတယ်။',
    'required_with'         => 'The {field} field is required when {param} is present.',
    'required_without'      => 'The {field} field is required when {param} is not present.',
    'string'                => 'The {field} field must be a valid string.',
    'timezone'              => 'The {field} field must be a valid timezone.',
    'valid_base64'          => 'The {field} field must be a valid base64 string.',
    'valid_email'           => '{field} မှာ မှန်ကန်တဲ့ email တစ်ခု ပါရပါမယ်။',
    'valid_emails'          => '{field} မှာ မှန်ကန်တဲ့ email အားလုံး ပါရပါမယ်။',
    'valid_ip'              => '{field} မှာ မှန်ကန်တဲ့ IP တစ်ခု ပါရပါမယ်။',
    'valid_url'             => '{field} မှာ မှန်ကန်တဲ့ URL တစ်ခု ပါရပါမယ်။',
    'valid_date'            => 'The {field} field must contain a valid date.',

    // Credit Cards
    'valid_cc_num' => '{field} does not appear to be a valid credit card number.',

    // Files
    'uploaded' => '{field} is not a valid uploaded file.',
    'max_size' => '{field} is too large of a file.',
    'is_image' => '{field} is not a valid, uploaded image file.',
    'mime_in'  => '{field} does not have a valid mime type.',
    'ext_in'   => '{field} does not have a valid file extension.',
    'max_dims' => '{field} is either not an image, or it is too wide or tall.',
];
