<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DateTime;
use DateTimeZone;

class AppModel extends Model
{
	protected $js_dates = [];

    public function jsDate2SqlDate($jsDate) {
        //the date coming from the form might be a JS timestamp, in that case must be devided by 1000 and converted
        //or a full JS date, in that case it will contain hifens, so also needs converting
        $date = new DateTime(null, new DateTimeZone('UTC'));
        if (substr_count($jsDate, ':')>0)
            $date->setDate(substr($jsDate, 0, 4), substr($jsDate, 5, 2), substr($jsDate, 8, 2));
        else
            $date->setTimestamp($jsDate/1000);
        return $date->format('Y-m-d');
    }

    //converts Y-m-d into javascript timestamp
    public function sqlDate2Js($date) {
        if ($date) {
            $date = new DateTime($date, new DateTimeZone('UTC'));
            return $date->getTimestamp()*1000;
        }
    }

    //converts dates coming from javascript into SQL format Y-m-d
    public function fill(array $attributes) {
    	foreach ($this->js_dates as $js_date) {
    		if (array_key_exists($js_date, $attributes))
    			$attributes[$js_date] = $this->jsDate2SqlDate($attributes[$js_date]);
    	}
    	return parent::fill($attributes);
    }

    //convert dates from database to JS
    public function toArray(){
    	foreach ($this->js_dates as $js_date) {
    		$this->$js_date = $this->sqlDate2Js($this->$js_date);
    	}
    	return parent::toArray();
    }
}
