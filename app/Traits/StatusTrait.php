<?php

namespace App\Traits;

use App\Models\Ignug\State;

trait StatusTrait
{
    public function scopeDeletedStatus($query)
    {
        return $query->where('state_id', State::firstWhere('code', State::DELETED)->id);
    }

    public function scopeAllStatus($query)
    {
        return $query->where('state_id', State::firstWhere('code', State::ACTIVE)->id)
            ->orWhere('state_id', State::firstWhere('code', State::DELETED)->id);
    }

}
