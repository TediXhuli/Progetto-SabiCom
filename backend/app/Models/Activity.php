<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string name
 * @property string description
 * @property boolean status
 * @property integer user_id
 * @property User user
 */
class Activity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
    ];

    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'status'      => 'boolean',
        'user_id'     => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createItem($data)
    {
        $user = auth()
            ->guard('api')
            ->user();

        return $user->activities()
            ->create($data);
    }

    public function updateItem($data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->status = $data['status'];
        $this->save();
        return $this;
    }
}
