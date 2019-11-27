<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Rules\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    protected $baseViewPath = 'rule';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $rules = auth()->user()
                ->rules()
                ->with('expansion')
                ->orderBy('order_column', 'ASC')
                ->paginate();

            foreach ($rules as $key => $rule) {
                $rule->articleStats = $rule->articleStats;
            }

            return $rules;
        }

        return view($this->baseViewPath . '.index')
            ->with('is_applying_rules', auth()->user()->is_applying_rules);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Rule::create($request->validate([
            'name' => 'required|string',
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function show(Rule $rule)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $rule->load([
                'expansion'
            ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function edit(Rule $rule)
    {
        $expansions = Expansion::all();

        return view($this->baseViewPath . '.edit')
            ->with('basePrices', Article::BASE_PRICES)
            ->with('expansions', $expansions)
            ->with('model', $rule)
            ->with('rarities', Card::RARITIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rule $rule)
    {
        $rule->update($request->validate([
            'base_price' => 'required|string',
            'description' => 'nullable|string',
            'expansion_id' => 'nullable|integer',
            'is_altered' => 'required|boolean',
            'is_foil' => 'required|boolean',
            'is_playset' => 'required|boolean',
            'is_signed' => 'required|boolean',
            'min_price_common_formatted' => 'required|formated_number',
            'min_price_land_formatted' => 'required|formated_number',
            'min_price_masterpiece_formatted' => 'required|formated_number',
            'min_price_mythic_formatted' => 'required|formated_number',
            'min_price_rare_formatted' => 'required|formated_number',
            'min_price_special_formatted' => 'required|formated_number',
            'min_price_time_shifted_formatted' => 'required|formated_number',
            'min_price_tip_card_formatted' => 'required|formated_number',
            'min_price_token_formatted' => 'required|formated_number',
            'min_price_uncommon_formatted' => 'required|formated_number',
            'multiplier_formatted' => 'required|formated_number',
            'name' => 'required|string',
            'price_above_formatted' => 'required|formated_number',
            'price_below_formatted' => 'required|formated_number',
            'rarity' => 'nullable|string',
        ]));

        if ($request->wantsJson()) {
            return $rule;
        }

        return redirect($rule->path);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rules\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rule $rule)
    {
        if ($isDeletable = $rule->isDeletable()) {
            $rule->delete();
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        if ($isDeletable) {
            $status = [
                'type' => 'success',
                'text' => 'Regel <b>' . $rule->name . '</b> gelöscht.',
            ];
        }
        else {
            $status = [
                'type' => 'danger',
                'text' => 'Regel <b>' . $rule->name . '</b> kann nicht gelöscht werden.',
            ];
        }

        return redirect(route($this->baseViewPath . '.index'))
            ->with('status', $status);
    }
}
