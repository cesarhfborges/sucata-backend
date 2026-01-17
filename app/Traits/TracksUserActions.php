<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait TracksUserActions
{
    protected static function bootTracksUserActions(): void
    {
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $model->created_by ??= Auth::id();
                $model->updated_by ??= Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function (Model $model) {
            if (Auth::check() && $model->isFillable('deleted_by')) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }
}
