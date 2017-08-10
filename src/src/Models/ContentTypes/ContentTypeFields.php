<?php

namespace Wbe\Crud\Models\ContentTypes;

use Illuminate\Database\Eloquent\Collection;
use Wbe\Crud\Models\Translatable;

class ContentTypeFields extends \Eloquent
{
    use Translatable;

    public $timestamps = false;
    protected $table = 'content_type_fields';

    /*static public function getFields($id) {
        return self::find($id)->toArray();
    }*/

/*public function description() {
        return $this->hasOne(ContentTypeFieldsDescription::class, 'content_type_field_id', 'id');
    }*/

    /**
     * Отримати поля для типу контенту $content_type, із опціональним фільтром $custom_where
     * @param $content_type int ID типу контенту
     * @param array $custom_where
     * @return Collection
     */
    static public function getFieldsFromDB($content_type, $custom_where = [])
    {
        $where = [['content_type_id', '=', $content_type]]; // ['grid_show', '=', '1']
        $where = array_merge($where, $custom_where);

        // from crud_content_type_fields
        return ContentTypeFields::where($where)
            ->orderBy('sort')
            ->get()
            ->keyBy('name');

        //if ($lang_id) {
        /*$ct_fields = $ct_fields->addSelect(['CCTFD.name as caption'])->leftJoin('crud_content_type_fields_description AS CCTFD', [
            ['CCTFD.content_id', '=', 'crud_content_type_fields.id'],
            ['CCTFD.lang_id', '=', \DB::raw(2)] //$lang_id
        ]);*/
        //}

    }

    public function contentType()
    {
        return $this->belongsTo('Wbe\Crud\Models\ContentType', 'content_type_id', 'id');
    }

}