<?php

namespace App\Models;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\SortableTrait;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    use HasUlids;
    use SortableTrait;

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }

    public function buildSortQuery()
    {
        return static::query()->where('assignment_id', $this->assignment_id);
    }

    /**
     * The assignment that the question belongs to.
     *
     * @return BelongsTo<Assignment, Question>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * The submissions that belong to the question.
     *
     * @return HasMany<Submission, Question>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'language' => QuestionLanguage::class,
            'type'     => QuestionType::class,
            'level'    => QuestionLevel::class,
        ];
    }
}
