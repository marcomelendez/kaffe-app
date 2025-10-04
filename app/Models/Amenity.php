<?php

namespace App\Models;

use App\Traits\FormTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Amenity extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    use FormTrait;

    public $fillable = ['type','icon'];
    public $translatedAttributes = ['name', 'description'];

    /*public function editable()
    {
        return array(
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Name',
                'value' => $this->name,
                'rules' => 'required'
            ],[
                'name' => 'type',
                'type' => 'text_singleselect',
                'related' => ['private_amenity' => 'Private Amenity', 'shared_amenity' => 'Shared Amenity', 'included_service' => 'Included Service'],
                'value' => $this->type,
                'label' => 'Type',
                'rules' => ''
            ],[
                'name' => 'icon',
                'type' => 'text',
                'value' => $this->icon,
                'label' => 'Icon',
                'rules' => ''
            ],[
                'name' => 'description',
                'type' => 'textarea',
                'value' => $this->description,
                'label' => 'Description',
                'rules' => ''
            ]
        );
    } */

    public function properties()
    {
    	return $this->morphedByMany('App\Models\Property', 'amenable');
    }

    public function rooms()
    {
    	return $this->morphedByMany('App\Models\Room', 'amenable');
    }
}
