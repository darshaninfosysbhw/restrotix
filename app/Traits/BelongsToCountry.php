<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCountry
{
    /**
     * यह फंक्शन मॉडल को लोड करते ही अपने आप रन होगा
     */
    protected static function bootBelongsToCountry()
    {
        static::addGlobalScope('country_filter', function (Builder $builder) {
            if (session()->has('active_country_id')) {
                // $builder से मॉडल का इंस्टेंस निकालो ताकि getTable() कॉल कर सकें
                $model = $builder->getModel();
                $tableName = $model->getTable();

                $builder->where($tableName . '.country_id', session('active_country_id'));
            }
        });
    }
}
