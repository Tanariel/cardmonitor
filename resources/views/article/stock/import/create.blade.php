<div class="modal fade" tabindex="-1" role="dialog" id="import-stock">
    <div class="modal-dialog" role="document">
        <form action="/article/stock/import" enctype="multipart/form-data" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Artikelbestand importieren</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="game_id">Spiel</label>
                        <select class="form-control" name="game_id" id="game_id">
                            <option value="1">Magic - The Gathering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="file">CSV-Datei</label>
                        <input type="file" class="form-control-file" name="file" id="file" required>
                    </div>
                    <div class="card">
                        <div class="card-header">Beispieldatei</div>
                        <div class="card-body">
                            <div>local_card_id;condition;amount;unit_price</div>
                            <div>20927-ARB-DE;EX;3;3.45</div>
                            <div>361735-C18-GB;;3;0</div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3">
                        Artikel, die nicht in der Importdatei sind, werden gelöscht!<br />
                        Wenn der unit_price 0 ist, werden nur Bestände angepasst und keine neune Artikel angelegt<br />
                        Wenn die condition leer ist wird "NM" genutzt
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