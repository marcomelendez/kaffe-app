<?php

namespace App\Imports;

use App\Models\Property;
use App\Models\Provider;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PropertyCsvImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $provider = Provider::firstOrCreate([
            'name' => $row['provider_name'],
            'dni' => $row['dni'],
        ], [
            'address' => $row['address'] ?? null,
            'phone_number' => $row['phone_number'] ?? null,
            'email' => $row['email'] ?? null,
            'email_secundary' => $row['email_secondary'] ?? null,
            'province_code' => $row['province_code'] ?? null,
            'contact_person' => $row['contact_person'] ?? null,
            'highlights' => $row['highlights'] ?? null,
        ]);

        // Crear la propiedad
        $property = Property::create([
            'multi_unit' => filter_var($row['multi_unit'], FILTER_VALIDATE_BOOLEAN),
            'provider_id' => $provider->id,
            'category_id' => $row['category_id'],
            'property_type_id' => $row['property_type_id'],
            'location_id' => $row['location_id'],
            'name' => $row['name'],
            'real_name' => $row['real_name'] ?? null,
            'slug' => $row['slug'],
            'videos' => $row['videos'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'latlng' => $row['latlng'] ?? null,
            'owner_id' => $row['owner_id'] ?? null,
            'last_calendar_update' => $row['last_calendar_update'] ?? null,
            'checkin_from' => $row['checkin_from'] ?? null,
            'checkin_to' => $row['checkin_to'] ?? null,
            'checkout_from' => $row['checkout_from'] ?? null,
            'checkout_to' => $row['checkout_to'] ?? null,
            'confirmation_percentage' => $row['confirmation_percentage'] ?? null,
            'rooms' => $row['rooms'],
            'bathrooms' => $row['bathrooms'],
            'adtl_beds' => $row['additional_beds'] ?? null,
            'min_occupancy' => $row['min_occupancy'],
            'max_occupancy' => $row['max_occupancy'],
            'adtl_pax' => $row['additional_pax'] ?? null,
            'adtl_pax_price' => $row['additional_pax_price'] ?? null,
            'min_nights' => $row['min_nights'],
            'days_in_advance' => $row['days_in_advance'],
            'instant_booking' => filter_var($row['instant_booking'], FILTER_VALIDATE_BOOLEAN),
            'default_state' => $row['default_state'],
            'default_rate' => $row['default_rate'] ?? null,
            'deposit' => $row['deposit'] ?? null,
            'commission' => $row['commission'],
            'published' => filter_var($row['published'], FILTER_VALIDATE_BOOLEAN),
            'recommended' => filter_var($row['recommended'], FILTER_VALIDATE_BOOLEAN),
            'active' => filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            'description' => $row['description'] ?? null,
            'short_description' => $row['short_description'] ?? null,
            'owner_highlights' => $row['owner_highlights'] ?? null,
            'directions' => $row['directions'] ?? null,
            'other_conditions' => $row['other_conditions'] ?? null,
        ]);

            // Agregar la traducción inicial en español
            // PropertyTranslation::create([
            //     'property_id' => $property->id,
            //     'title' => $row['title'],
            //     'description' => $row['description'] ?? null,
            //     'short_description' => $row['short_description'] ?? null,
            //     'owner_highlights' => $row['owner_highlights'] ?? null,
            //     'directions' => $row['directions'] ?? null,
            //     'other_conditions' => $row['other_conditions'] ?? null,
            //     'locale' => 'es',
            // ]);

        return $property;

        // return new Property([
        //     //
        // ]);
    }
}
