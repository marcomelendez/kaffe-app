<?php

namespace App\Models;

use App\Scr\Occupancy;
use App\Scr\Rate;
use Codebyray\ReviewRateable\Traits\ReviewRateable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;

use App\Traits\LoggableTrait;
use App\Traits\FormTrait;
use App\Traits\SearchableTrait;
use App\Traits\VisitableTrait;
use App\Traits\BookableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;


use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use URL;

class Property extends Model implements TranslatableContract, HasMedia
{
    // use LoggableTrait;
    // use VisitableTrait;
    use HasFactory;
    use Translatable;
    use InteractsWithMedia;

    protected $rates = [];

    public $translatedAttributes = ['title', 'description','short_description', 'directions', 'other_conditions'];

    public $fillable = [
        'multi_unit',
        'provider_id',
        'category_id',
        'property_type_id',
        'location_id',
        'name',
        'real_name',
        'slug',
        'videos',
        'postal_code',
        'latlng',
        'property_id',
        'last_calendar_update',
        'checkin_from',
        'checkin_to',
        'checkout_from',
        'checkout_to',
        'confirmation_percentage',
        'rooms',
        'bathrooms',
        'adtl_beds',
        'min_occupancy',
        'max_occupancy',
        'adtl_pax',
        'adtl_pax_price',
        'min_nights',
        'days_in_advance',
        'instant_booking',
        'recommended',
        'default_state',
        'default_rate',
        'deposit',
        'commission',
        'published'
    ];

    // public function editable()
    // {
    //     return array(
    //         [
    //             'name' => 'slug',
    //             'type' => 'text',
    //             'label' => '*Slug',
    //             'value' => $this->slug,
    //             'rules' => 'required'
    //         ],[
    //             'name' => 'name',
    //             'type' => 'text',
    //             'label' => '*Name',
    //             'value' => $this->name,
    //             'rules' => 'required'
    //         ],[
    //             'name' => 'real_name',
    //             'type' => 'text',
    //             'label' => 'Real Name',
    //             'value' => $this->real_name,
    //             'rules' => ''
    //         ],[
    //             'name' => 'title',
    //             'type' => 'text',
    //             'label' => '*Title',
    //             'value' => $this->title,
    //             'rules' => 'required'
    //         ],[
    //             'name' => 'category_id',
    //             'type' => 'related_singleselect',
    //             'related' => Category::all()->pluck('name', 'id'),
    //             'value' => $this->category_id,
    //             'label' => '*Category',
    //             'rules' => 'required'
    //         ],[
    //             'name' => 'provider_id',
    //             'type' => 'related_singleselect',
    //             'related' => Provider::all()->pluck('name', 'id'),
    //             'value' => $this->category_id,
    //             'label' => '* Provider',
    //             'rules' => 'required'
    //         ],[
    //             'name' => 'upload_photos',
    //             'type' => 'related_photos',
    //             'related' => '',
    //             'value' => '',
    //             'label' => '',
    //             'rules' => ''
    //         ],[
    //             'name' => 'short_description',
    //             'type' => 'editor',
    //             'value' => $this->short_description,
    //             'label' => 'Short Description',
    //             'rules' => ''
    //         ],[
    //             'name' => 'description',
    //             'type' => 'editor',
    //             'value' => $this->description,
    //             'label' => 'Description',
    //             'rules' => ''
    //         ],[
    //             'name' => 'directions',
    //             'type' => 'editor',
    //             'value' => $this->directions,
    //             'label' => 'Directions',
    //             'rules' => ''
    //         ],[
    //             'name' => 'location_id',
    //             'type' => 'related_singleselect',
    //             'related' => \App\Models\Municipality::all()->pluck('name', 'id'),
    //             'value' => $this->location_id,
    //             'label' => 'Location',
    //             'rules' => ''
    //         ],[
    //             'name' => 'postal_code',
    //             'type' => 'text',
    //             'value' => $this->postal_code,
    //             'label' => 'Postal Code',
    //             'rules' => ''
    //         ],[
    //             'name' => 'latlng',
    //             'value' => $this->latlng,
    //             'type' => 'map',
    //             'label' => 'Location Coordinates',
    //             'rules' => '',
    //         ],[
    //             'name' => 'tags',
    //             'type' => 'related_tags',
    //             'related' => Tag::all()->pluck('name', 'id'),
    //             'label' => 'Tags',
    //             'rules' => ''
    //         ],[
    //             'name' => 'amenities',
    //             'type' => 'related_multiselect',
    //             'related' => Amenity::all()->pluck('name', 'id'),
    //             'label' => 'Included amenities',
    //             'rules' => ''
    //         ],[
    //             'name' => 'plan',
    //             'type' => 'related_multiselect',
    //             'related' => Plan::all()->pluck('name', 'id'),
    //             'label' => 'Include plan',
    //             'rules' => ''
    //         ],[
    //             'name' => 'commission',
    //             'type' => 'text',
    //             'value' => $this->commission,
    //             'label' => '* Commission',
    //             'rules' => 'required|numeric'
    //         ],[
    //             'name' => 'checkin_from',
    //             'type' => 'timepicker',
    //             'value' => $this->checkin_from,
    //             'label' => 'Checkin from',
    //             'rules' => ''
    //         ],[
    //             'name' => 'checkin_to',
    //             'type' => 'timepicker',
    //             'value' => $this->checkin_to,
    //             'label' => 'Checkin to',
    //             'rules' => ''
    //         ],[
    //             'name' => 'checkout_from',
    //             'type' => 'timepicker',
    //             'value' => $this->checkout_from,
    //             'label' => 'Checkout from',
    //             'rules' => ''
    //         ],[
    //             'name' => 'checkout_to',
    //             'type' => 'timepicker',
    //             'value' => $this->checkout_to,
    //             'label' => 'Checkout to',
    //             'rules' => ''
    //         ],[
    //             'name' => 'confirmation_percentage',
    //             'type' => 'text',
    //             'value' => $this->confirmation_percentage,
    //             'label' => 'Confirmation percentage',
    //             'rules' => ''
    //         ],[
    //             'name' => 'other_conditions',
    //             'type' => 'editor',
    //             'value' => $this->other_conditions,
    //             'label' => 'Other conditions',
    //             'rules' => ''
    //         ],[
    //             'name' => 'videos',
    //             'type' => 'textarea',
    //             'value' => $this->videos,
    //             'label' => 'videos',
    //             'rules' => ''
    //         ],[
    //             'name' => 'active',
    //             'type' => 'checkbox_toggle',
    //             'value' => $this->active,
    //             'label' => 'Active',
    //             'rules' => ''
    //         ],[
    //             'name' => 'published',
    //             'type' => 'checkbox_toggle',
    //             'value' => $this->published,
    //             'label' => 'Published',
    //             'rules' => ''
    //         ],[
    //             'name' => 'rooms',
    //             'type' => 'text',
    //             'value' => $this->rooms,
    //             'label' => 'Rooms',
    //             'rules' => ''
    //         ],[
    //             'name' => 'bathrooms',
    //             'type' => 'text',
    //             'value' => $this->bathrooms,
    //             'label' => 'Bathrooms',
    //             'rules' => ''
    //         ],[
    //             'name' => 'adtl_beds',
    //             'type' => 'text',
    //             'value' => $this->adtl_beds,
    //             'label' => 'Additional Beds',
    //             'rules' => ''
    //         ],[
    //             'name' => 'min_occupancy',
    //             'type' => 'text',
    //             'value' => $this->min_occupancy,
    //             'label' => 'Min Occupancy',
    //             'rules' => 'required|numeric'
    //         ],[
    //             'name' => 'max_occupancy',
    //             'type' => 'text',
    //             'value' => $this->max_occupancy,
    //             'label' => 'Max Occupancy',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'min_nights',
    //             'type' => 'text',
    //             'value' => $this->min_nights,
    //             'label' => 'Minimum nights (default)',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'days_in_advance',
    //             'type' => 'text',
    //             'value' => $this->days_in_advance,
    //             'label' => 'Days in advance',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'multi_unit',
    //             'type' => 'checkbox_toggle',
    //             'value' => $this->multi_unit,
    //             'label' => 'Multi-unit?',
    //             'rules' => ''
    //         ],[
    //             'name' => 'instant_booking',
    //             'type' => 'checkbox_toggle',
    //             'value' => $this->instant_booking,
    //             'label' => 'Reserva inmediata?',
    //             'rules' => ''
    //         ],[
    //             'name' => 'recommended',
    //             'type' => 'checkbox_toggle',
    //             'value' => $this->recommended,
    //             'label' => 'Recomendado?',
    //             'rules' => ''
    //         ],[
    //             'name' => 'deposit',
    //             'type' => 'text',
    //             'value' => $this->deposit,
    //             'label' => 'Deposit',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'discount',
    //             'type' => 'text',
    //             'value' => $this->discount,
    //             'label' => 'Discount',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'default_rate',
    //             'type' => 'text',
    //             'value' => $this->default_rate,
    //             'label' => 'Default Rate',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'default_state',
    //             'type' => 'text',
    //             'value' => $this->default_state,
    //             'label' => 'Default State',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'adtl_pax',
    //             'type' => 'text',
    //             'value' => $this->adtl_pax,
    //             'label' => 'Additional Pax',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'adtl_pax_price',
    //             'type' => 'text',
    //             'value' => $this->adtl_pax_price,
    //             'label' => 'Additional Pax Price',
    //             'rules' => 'numeric'
    //         ],[
    //             'name' => 'ical_url',
    //             'type' => 'text',
    //             'label' => 'Ruta Ical',
    //             'value' => $this->ical_url,
    //             'rules' => ''
    //         ],[
    //             'name' => 'ical_update_at',
    //             'type' => 'datepicker',
    //             'label' => 'Actualización Ical',
    //             'value' => $this->ical_update_at,
    //             'rules' => ''
    //         ],[
    //             'name' => 'classification',
    //             'type' => 'related_singleselect',
    //             'related' => \Config('eres.classification'),
    //             'value' => $this->classification,
    //             'label' => 'Clasificacion',
    //             'rules' => ''
    //         ]
    //     );
    // }

    public function reservation()
    {
        return $this->hasMany('App\Models\Reservation');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\GeoUnit', 'location_id');
    }

    public function category()
    {
    	return $this->belongsTo(Category::class);
    }

    public function photos()
    {
    	return $this->morphMany(Photo::class, 'imageable')->orderBy('cover', 'DESC');
    }

    public function amenities()
    {
        return $this->morphToMany('App\Models\Amenity', 'amenable');
    }

    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function custom_periods()
    {
        return $this->hasMany('App\Models\CustomPeriod')->orderBy('start_date');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'codauto', 'code');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'cpro', 'code');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }

    public function cover()
    {
        $propertyCover = $this->photos->sortByDesc('cover')->first();

        if($propertyCover){
            return Storage::disk('s3')->url('/prod_images/productos/'.$propertyCover->filename);
        }

        return \Illuminate\Support\Facades\URL::to('images/phono_default.png');
    }

    public function roomsAll()
    {
        return $this->hasMany(Room::class);
    }

    public function getByName($name)
    {
        return $this->where('name','=',$name)->first();
    }

    public function getBookableUnit($occupancy)
    {
        return $this->join('rooms', 'properties.id', '=', 'rooms.property_id')
            ->join('room_units as ru', 'ru.room_id', '=', 'rooms.id')
            ->join('plans','ru.plan_id','=','plans.id')
            ->join('plan_translations as pt','pt.plan_id','=','plans.id')
            ->join('bookable_units AS bu',function($join){
                $join->on('bu.bookable_id','=','ru.id');
            })
            ->select('rooms.code as room_code', 'rooms.name as room_name','rooms.max_persons as room_limit','bu.min_occupancy','bu.max_occupancy','rooms.max_persons','bu.id as BookableId','bu.name','pt.id as meal_plan')
            ->where('bu.bookable_type','=',"App\\Models\\RoomUnit")
            ->where('properties.id','=',$this->id)
            ->where('bu.max_occupancy','>=',$occupancy)
            ->where('bu.max_occupancy','<=',$occupancy)
            ->get();
    }

    public function addRates(array $rate)
    {
        $this->rates = $rate;
    }

    public function getRates()
    {
        return $this->rates;
    }
}
