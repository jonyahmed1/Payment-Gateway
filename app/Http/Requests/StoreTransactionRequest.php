<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize() { return $this->user() !== null; }
    public function rules()
    {
        return [
            'mfs_agent_id' => ['required','exists:mfs_agents,id'],
            'mfs_number_id' => ['required','exists:mfs_numbers,id'],
            'type' => ['required','in:deposit,withdraw'],
            'amount' => ['required','numeric','min:1'],
            'trx_id' => ['required','string','max:255'],
            'currency' => ['sometimes','string'],
            'metadata' => ['sometimes','array'],
            'phone_number' => ['sometimes','string'], // for blacklist check
        ];
    }
}