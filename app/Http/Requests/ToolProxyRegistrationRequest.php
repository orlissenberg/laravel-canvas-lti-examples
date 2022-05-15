<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToolProxyRegistrationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        // https://laravel.com/docs/master/validation#available-validation-rules
        return [
            'lti_message_type' => 'required',
            'lti_version' => 'in:LTI-2p0',
            'reg_key' => 'required',
            'reg_password' => 'required',
            'tc_profile_url' => 'required',
            'launch_presentation_return_url' => 'required',
            'tool_proxy_guid' => 'required',
            'tool_proxy_url' => 'required',
            'launch_presentation_document_target' => 'required',
            'ext_tool_consumer_instance_guid' => 'required',
            'ext_api_domain' => 'required',
        ];
    }
}
