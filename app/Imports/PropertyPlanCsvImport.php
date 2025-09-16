<?php

namespace App\Imports;

use App\Models\PlanProperty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PropertyPlanCsvImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $roomUnit = PlanProperty::firstOrCreate([
            'plan_id' => $row['plan_id'],
            'property_id' => $row['property_id']
        ]);

        return $roomUnit;
    }
}
