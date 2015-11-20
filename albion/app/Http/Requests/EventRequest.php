<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EventRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'newEventName' => 'required|min:6|unique:events,eventName'
                ];
            }
            case 'PUT':
            {
                return [
                    'modifiedEventName' => 'required|min:6|unique:events,eventName'
                ];
            }
            case 'PATCH':
            {
                return [
                    'modifiedEventName' => 'required|min:6|unique:events,eventName'
                ];
            }
            default:break;
        }


    }
}
