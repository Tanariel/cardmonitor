@extends('layouts.app')

@section('content')

    <h2>{{ __('app.nav.order') }}</h2>
    <order-table :is-syncing-orders="{{ $is_syncing_orders }}" :states="{{ json_encode($states) }}"></order-table>

    <div class="modal fade" tabindex="-1" role="dialog" id="import-sent">
        <div class="modal-dialog" role="document">
            <form action="/order/import/sent" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bestellungen als versendet markieren</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="import_sent_file">CSV-Datei</label>
                            <input type="file" class="form-control-file" name="import_sent_file" id="import_sent_file" required>
                        </div>
                        <div class="card">
                            <div class="card-header">Beispieldatei</div>
                            <div class="card-body">
                                <div>order_id;sent</div>
                                <div>123456;1</div>
                                <div>123457;1</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Importieren</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection