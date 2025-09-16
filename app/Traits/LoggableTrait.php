<?php namespace App\Traits;

use App\Models\UserActivityLog;

trait LoggableTrait
{

	public static function bootLoggableTrait()
	{
		static::created(function($item) {
			$reflection = new \ReflectionClass($item);
			// $loggable = strtolower($reflection->getShortName());
			$user = \Auth::user();
			$activity = new UserActivityLog;
			$activity->user()->associate($user);
			$activity->loggable_id = $item->id;
			$activity->loggable_type = $reflection->getName();
			$activity->action = 'created';
			$activity->description = "";
			$activity->ip = app('request')->ip();
			$activity->user_agent = app('request')->header('User-Agent');
			$activity->save();
		});

		static::updated(function($item) {
			$reflection = new \ReflectionClass($item);
			// $loggable = strtolower($reflection->getShortName());
			$user = \Auth::user();
			$activity = new UserActivityLog;
			$activity->user()->associate($user);
			$activity->loggable_id = $item->id;
			$activity->loggable_type = $reflection->getName();
			$activity->action = 'updated';
			$activity->description = "";
			$activity->ip = app('request')->ip();
			$activity->user_agent = app('request')->header('User-Agent');
			$activity->save();
		});		
	}

    public function activities()
    {
        return $this->morphMany('App\Models\UserActivityLog', 'loggable');
    }	
}