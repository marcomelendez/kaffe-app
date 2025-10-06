<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoUnit extends Model
{
    use HasFactory;

    private $location = [];
    public function findByName(string $name)
    {
        $result = [];
        $geoUnits = $this->where('name','LIKE','%'.$name.'%')->get();

        foreach($geoUnits as $geoUnit){
            $result[] = $this->getParent($geoUnit->id);
        }

        return $result;
    }

    public function getParent(int $id)
    {
        $geoUnit = $this->find($id);
        $this->location[$geoUnit->type] = ['id'=>$geoUnit->id, 'name'=>$geoUnit->name];
        if($geoUnit->parent_id){
            $this->getParent($geoUnit->parent_id);
        }
        return $this->location;
    }

    public function getLocation()
    {
        $parents = $this->getParent($this->id);

        $country = null;
        $region = null;
        $province = null;
        $zone     = null;
        $municipalities = null;

        foreach($parents as $code => $parent){

            if($code == "CO") $country = $parent['name'];
            if($code == "RE") $region = $parent['name'];
            if($code == "PR") $province = $parent['name'];
            if($code == "ZO") $zone = $parent['name'];
            if($code == "MU") $municipalities = $parent['name'];
        }

        $result = collect([$zone, $municipalities, $region, $province,$country]);

        $resultFiltered = $result->filter(function($item){
            return !empty($item);
        });

        return $resultFiltered->implode(" ");
    }

    public function getZone()
    {
        $parents = $this->getParent($this->id);
        return $parents['ZO']['name'] ?? null;
    }

    public function getCountry()
    {
        $parents = $this->getParent($this->id);
        return $parents['CO']['name'] ?? null;
    }

    public function getProvince()
    {
        $parents = $this->getParent($this->id);
        return $parents['PR']['name'] ?? null;
    }

    public function getRegion()
    {
        $parents = $this->getParent($this->id);
        return $parents['RE']['name'] ?? null;
    }
}
