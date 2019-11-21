<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\Rules\Rule;
use Illuminate\Http\Request;

class SortController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'rules' => 'required|array',
        ]);

        foreach ($attributes['rules'] as $key => $ruleId) {
            Rule::where('id', $ruleId)->update([
                'order_column' => ($key + 1),
            ]);
        }
    }
}
