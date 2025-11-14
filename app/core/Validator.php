<?php
namespace App\Core;

class Validator
{
    public static function make(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $rulesArr = explode('|', $ruleString);
            $value = trim($data[$field] ?? '');
            foreach ($rulesArr as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field] = 'Ushbu maydon majburiy.';
                }
                if (str_starts_with($rule, 'max:')) {
                    $limit = (int)substr($rule, 4);
                    if (mb_strlen($value) > $limit) {
                        $errors[$field] = "Uzunlik {$limit} belgidan oshmasligi kerak.";
                    }
                }
                if ($rule === 'url' && $value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$field] = 'URL notog\'ri.';
                }
                if ($rule === 'numeric' && $value && !is_numeric($value)) {
                    $errors[$field] = 'Faqat raqam kiritish mumkin.';
                }
            }
        }
        return $errors;
    }
}
