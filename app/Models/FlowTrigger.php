<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * What fires a flow. MVP only implements keyword matching against an inbound
 * message's body; button/template_reply matching is schema support for later.
 */
class FlowTrigger extends Model
{
    use HasFactory, BelongsToTenant;

    public const TYPE_KEYWORD = 'keyword';
    public const TYPE_BUTTON = 'button';
    public const TYPE_TEMPLATE_REPLY = 'template_reply';
    public const TYPE_ANY = 'any';

    protected $fillable = [
        'tenant_id', 'flow_id', 'match_type', 'value', 'exact_match',
    ];

    protected $casts = [
        'exact_match' => 'boolean',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ChatbotFlow::class, 'flow_id');
    }

    /** Whether an inbound message's text body fires this trigger. */
    public function matches(?string $body): bool
    {
        if ($this->match_type === self::TYPE_ANY) {
            return true;
        }

        if ($this->match_type !== self::TYPE_KEYWORD || $this->value === null || $body === null) {
            return false;
        }

        $needle = Str::lower(trim($this->value));
        $haystack = Str::lower(trim($body));

        if ($needle === '') {
            return false;
        }

        return $this->exact_match ? $haystack === $needle : Str::contains($haystack, $needle);
    }
}
