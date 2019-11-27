@extends('layouts.app')

@section('content')

    <h2>Artikel</h2>
    <article-table
        :conditions="{{ json_encode($conditions) }}"
        :expansions="{{ json_encode($expansions) }}"
        :is-applying-rules="{{ $is_applying_rules }}"
        :is-syncing-articles="{{ $is_syncing_articles }}"
        :languages="{{ json_encode($languages) }}"
        :rarities="{{ json_encode($rarities) }}"
        :rules="{{ json_encode($rules) }}"
        :storages="{{ json_encode($storages) }}"
        :can-pay-rule-apply="{{ $can_pay_rule_apply }}"
    ></article-table>

@endsection