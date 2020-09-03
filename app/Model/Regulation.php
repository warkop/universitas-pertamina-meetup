<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Regulation extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'regulation';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $with = ['regulationFile', 'institution'];

    public function regulationFile()
    {
        return $this->hasMany(RegulationFile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('regulation')
        ->select(
            'regulation.*',
            'institution.name as institution_name'
        )
        ->leftJoin('institution','institution.id', '=', 'institution_id')
        ->whereNull('regulation.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('regulation.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('institution.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
