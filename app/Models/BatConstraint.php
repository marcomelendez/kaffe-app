<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Roomify\Bat\Constraint\MinMaxDaysConstraint;
use Roomify\Bat\Constraint\CheckInDayConstraint;
use Roomify\Bat\Constraint\DateConstraint;

class BatConstraint extends Model
{
    protected $table = 'bat_constraints';
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'checkin_date', 'checkout_date', 'start_date', 'end_date'];
    
    public function constrainable()
    {
    	return $this->morphTo();
    }


    public function getBatConstraint($units = [])
    {
        switch ($this->constraint_type) {
            case 'checkin_day':
                return new CheckInDayConstraint($units, $this->checkin_day);
                break;
            case 'date_constraint':
                return new DateConstraint($units, $this->start_date, $this->end_date);
            break;  
            case 'min_max_days':              
                return new MinMaxDaysConstraint($units, $this->min_days, $this->max_days, $this->start_date, $this->end_date);
                break;
            default:
            	return null;
            	break;
        }   
    }
}
