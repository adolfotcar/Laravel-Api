<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Request;
use App\Models\Syslog;
use DateTimeZone;
use DateTime;

class AppController extends Controller {

	protected $data = null;
    protected $meta = [];
    protected $status = 200;

    public function setMeta($title, $data = null){
        if( isset($data) )
        {
            if( $title == 'status' ) $this->status = $data;

            $this->meta[$title] = $data;
        }
        else if( is_array($title) )
        {
            $this->meta = $title;
        }
        else if( isset( $title ) )
        {
            $this->meta[] = $title;
        }
    }

    public function setData($title, $data = null){
        if( isset($data) ) {
            $this->data[$title] = $data;
        }
        else if( is_array($title) ) {
            $this->data = $title;
        }
        else if( isset( $title ) ) {
            $this->data[] = $title;
        }   
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getResult($status=null){
        if (!is_null($status)) $this->status = $status;
        if(!in_array('status', $this->meta)) $this->meta['status'] = $this->status;
        return Response::json([
            'meta' => $this->meta,
            'data' => $this->data
        ], $this->status);
    }

    //returns when an error happens
    public function endError($msg, $status=400) {
        if (!is_array($msg)) $msg = ['message' => $msg];

        $this->setData($msg);
        $this->setMeta($msg);
        return $this->getResult($status);
    }

    public function endNotFound($msg='not_found') {
        return $this->endError($msg, 404);
    }

    public function endDuplicated($msg='duplicate'){
        return $this->endError($msg, 409);
    }

    public function endValidation($msg='validation_failed'){
        return $this->endError($msg, 422);
    }

    public function endSuccess($title='success', $data=null, $code=200) {
        $this->setData($title, $data);
        return $this->getResult($code);
    }

}
