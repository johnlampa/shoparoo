<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'phone' => ['required', 'min:7'],
            'email' => ['required', 'email'],

            'shipping.address1' => ['required'],
            'shipping.address2' => ['required'],
            'shipping.city' => ['required'],
            'shipping.state' => [Rule::requiredIf(fn () => $this->countryHasStates($this->input('shipping.country_code')))],
            'shipping.zipcode' => ['required'],
            'shipping.country_code' => ['required', 'exists:countries,code'],

            'billing.address1' => ['required'],
            'billing.address2' => ['required'],
            'billing.city' => ['required'],
            'billing.state' => [Rule::requiredIf(fn () => $this->countryHasStates($this->input('billing.country_code')))],
            'billing.zipcode' => ['required'],
            'billing.country_code' => ['required', 'exists:countries,code'],

        ];
    }

    protected function countryHasStates(?string $countryCode): bool
    {
        if (!$countryCode) {
            return false;
        }

        $country = Country::query()->find($countryCode);
        if (!$country || !$country->states) {
            return false;
        }

        $states = is_string($country->states)
            ? json_decode($country->states, true)
            : $country->states;

        return is_array($states) && count($states) > 0;
    }

    public function attributes()
    {
        return [
            'billing.address1' => 'address 1',
            'billing.address2' => 'address 2',
            'billing.city' => 'city',
            'billing.state' => 'state',
            'billing.zipcode' => 'zip code',
            'billing.country_code' => 'country',
            'shipping.address1' => 'address 1',
            'shipping.address2' => 'address 2',
            'shipping.city' => 'city',
            'shipping.state' => 'state',
            'shipping.zipcode' => 'zip code',
            'shipping.country_code' => 'country',
        ];
    }
}
