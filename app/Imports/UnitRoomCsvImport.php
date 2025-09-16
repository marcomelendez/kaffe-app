<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\UnitRoom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitRoomCsvImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $roomUnit = UnitRoom::firstOrCreate([
            'room_occupancy_id' => $row['room_id'],
            'plan_id' => $row['plan_id']
        ]);

        return $roomUnit;
    }
}
