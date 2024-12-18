<?php

// app/Models/DocumentPermissionMap.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document_Permission_Map extends DBLibrary
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primarykey = 'id';
    protected $table = 'document_permission_map';
    protected $fillable = [
        'description',
    ];

    public function documentPermission(): HasMany
    {
        return $this->hasMany(Document_Permission::class, 'id', 'document_permission');
    }
}
