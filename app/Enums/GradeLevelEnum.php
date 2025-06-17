<?php
namespace App\Enums;

enum GradeLevelEnum: string
{
    // Elementary
    case FIRST_ELEMENTARY   = '1_ELEMENTARY';
    case SECOND_ELEMENTARY  = '2_ELEMENTARY';
    case THIRD_ELEMENTARY   = '3_ELEMENTARY';
    case FOURTH_ELEMENTARY  = '4_ELEMENTARY';
    case FIFTH_ELEMENTARY   = '5_ELEMENTARY';
    case SIXTH_ELEMENTARY   = '6_ELEMENTARY';
    case SEVENTH_ELEMENTARY = '7_ELEMENTARY';
    case EIGHTH_ELEMENTARY  = '8_ELEMENTARY';
    case NINTH_ELEMENTARY   = '9_ELEMENTARY';

    // High School
    case FIRST_HIGH_SCHOOL   = '1_HIGH_SCHOOL';
    case SECOND_HIGH_SCHOOL  = '2_HIGH_SCHOOL';
    case THIRD_HIGH_SCHOOL   = '3_HIGH_SCHOOL';

    public function educationStage(): string
    {
        return explode('_', $this->value)[1] ?? 'UNKNOWN';
    }

    public function gradeNumber(): int
    {
        return (int) explode('_', $this->value)[0];
    }

    public function label(string $locale = 'en'): string
    {
        return match ($locale) {
            'pt_BR' => match ($this) {
                self::FIRST_ELEMENTARY => '1º ano EF',
                self::SECOND_ELEMENTARY => '2º ano EF',
                self::THIRD_ELEMENTARY => '3º ano EF',
                self::FOURTH_ELEMENTARY => '4º ano EF',
                self::FIFTH_ELEMENTARY => '5º ano EF',
                self::SIXTH_ELEMENTARY => '6º ano EF',
                self::SEVENTH_ELEMENTARY => '7º ano EF',
                self::EIGHTH_ELEMENTARY => '8º ano EF',
                self::NINTH_ELEMENTARY => '9º ano EF',
                self::FIRST_HIGH_SCHOOL => '1º ano EM',
                self::SECOND_HIGH_SCHOOL => '2º ano EM',
                self::THIRD_HIGH_SCHOOL => '3º ano EM',
            },
            'en' => match ($this) {
                self::FIRST_ELEMENTARY => '1st Grade',
                self::SECOND_ELEMENTARY => '2nd Grade',
                self::THIRD_ELEMENTARY => '3rd Grade',
                self::FOURTH_ELEMENTARY => '4th Grade',
                self::FIFTH_ELEMENTARY => '5th Grade',
                self::SIXTH_ELEMENTARY => '6th Grade',
                self::SEVENTH_ELEMENTARY => '7th Grade',
                self::EIGHTH_ELEMENTARY => '8th Grade',
                self::NINTH_ELEMENTARY => '9th Grade',
                self::FIRST_HIGH_SCHOOL => '10th Grade',
                self::SECOND_HIGH_SCHOOL => '11th Grade',
                self::THIRD_HIGH_SCHOOL => '12th Grade',
            },
            default => $this->value,
        };
    }
}
