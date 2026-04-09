<?php

namespace App\Http\Requests;

use App\Enums\CustomerStatus;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CustomerRequest extends FormRequest
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
            'status' => ['required', 'boolean'],

            'shippingAddress.address1' => ['required'],
            'shippingAddress.address2' => ['required'],
            'shippingAddress.city' => ['required'],
            'shippingAddress.state' => [Rule::requiredIf(fn () => $this->countryHasStates($this->input('shippingAddress.country_code')))],
            'shippingAddress.zipcode' => ['required'],
            'shippingAddress.country_code' => ['required', 'exists:countries,code'],

            'billingAddress.address1' => ['required'],
            'billingAddress.address2' => ['required'],
            'billingAddress.city' => ['required'],
            'billingAddress.state' => [Rule::requiredIf(fn () => $this->countryHasStates($this->input('billingAddress.country_code')))],
            'billingAddress.zipcode' => ['required'],
            'billingAddress.country_code' => ['required', 'exists:countries,code'],

        ];
    }

    public function attributes()
    {
        return [
            'billingAddress.address1' => 'address 1',
            'billingAddress.address2' => 'address 2',
            'billingAddress.city' => 'city',
            'billingAddress.state' => 'state',
            'billingAddress.zipcode' => 'zip code',
            'billingAddress.country_code' => 'country',
            'shippingAddress.address1' => 'address 1',
            'shippingAddress.address2' => 'address 2',
            'shippingAddress.city' => 'city',
            'shippingAddress.state' => 'state',
            'shippingAddress.zipcode' => 'zip code',
            'shippingAddress.country_code' => 'country',
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
}
