<?php

use App\Enums\QuestionLanguage;

test('question language enum has correct values', function () {
    $expectedLanguages = ['python', 'java', 'javascript', 'c++', 'c#'];
    
    foreach ($expectedLanguages as $language) {
        $enum = QuestionLanguage::from($language);
        expect($enum->value)->toBe($language);
    }
});

test('question language enum has all expected cases', function () {
    expect(QuestionLanguage::Python->value)->toBe('python');
    expect(QuestionLanguage::Java->value)->toBe('java');
    expect(QuestionLanguage::JavaScript->value)->toBe('javascript');
    expect(QuestionLanguage::CPlusPlus->value)->toBe('c++');
    expect(QuestionLanguage::CSharp->value)->toBe('c#');
});
