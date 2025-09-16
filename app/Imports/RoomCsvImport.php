<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\RoomOccupancy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoomCsvImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $room = Room::firstOrCreate([
            'property_id'  => $row['property_id'],
            'name' => $row['type']
        ],[
            'name'           => $row['type'],
            'description'    => $row['description'],
            'status'         => $row['status'] ?? null
        ]);

        $roomOccupancy = RoomOccupancy::create([
            'room_id'        => $room->id,
            'code'           => $row['code'],
            'name'           => $row['name'],
            'max_adults'     => $row['max_adults'],
            'max_children'   => $row['max_children'] ?? null,
            'total_capacity' => $row['total_capacity']
        ]);

        return $room;
    }
}
