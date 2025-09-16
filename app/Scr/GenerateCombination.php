<?php

namespace App\Scr;

class GenerateCombination
{
    protected $combination = [];

    /**
     * Undocumented function
     *
     * @param array $rooms
     * @return void
     */
    public function createCombintions(array $rooms)
    {
        foreach ($rooms as $inx => $room) {
            
            if($result = $this->combine($rooms,$room,$inx)){

                $this->combination[] = $result;
            }
        }

        return $this->combination;
    }

    /**
     * Undocumented function
     *
     * @param array $datos
     * @param array $arrFilters
     * @param integer $inx
     * @return array
     */
    private function combine(array $datos,array $arrFilters,int $inx): array
    {
        $arrRooms = [];

        foreach ($arrFilters as $inxValue => $dat) {

            if( $result = $this->search($datos,$dat,$inxValue,$inx)){

                $arrRooms[] = $result;
            }
        }

        return $arrRooms;
    }

    /**
     * Undocumented function
     *
     * @param array $datos
     * @param array $value
     * @param integer $index
     * @return array
     */
    private function search(array $datos, array $value, int $inxValue,int $index): array
    {
        $arrRooms = [];
        
        foreach($datos as $inx => $rooms){

            if($index >= $inx){

                continue;
            }
            
            $arrRooms[$index][$inx][] = $value;

            foreach($rooms as $inxRooms => $room){
                
                if($inxValue == $inxRooms){

                    continue;
                }
                
                $arrRooms[$index][$inx][] = $room;
            }
        }
        

        return $arrRooms;
    }
}
