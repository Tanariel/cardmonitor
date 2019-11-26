<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\Rules\Rule;
use Illuminate\Http\Request;

class ActiveController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Rule $rule)
    {
        $rule->activate()
            ->save();

        if ($request->wantsJson()) {
            $rule->articleStats = $rule->articleStats;

            return $rule->loadCount('articles');
        }

        return redirect($rule->path)
            ->with('status', [
                'type' => 'success',
                'text' => 'Regel <b>' . $rule->name . '</b> wurde aktiviert.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rule $rule)
    {
        $rule->deactivate()
            ->save();

        if ($request->wantsJson()) {
            $rule->articleStats = $rule->articleStats;

            return $rule->loadCount('articles');
        }

        return redirect($rule->path)
            ->with('status', [
                'type' => 'success',
                'text' => 'Regel <b>' . $rule->name . '</b> wurde deaktiviert.',
            ]);
    }
}
