<?php

namespace App\Containers\AppSection\Project\Models;

use App\Ship\Parents\Models\Model as ParentModel;

final class Project extends ParentModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'budget',
    ];
}
