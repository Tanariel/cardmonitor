<div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0 d-none d-xl-block">
    <div class="card h-100">
        <div class="card-header">Bestellungen</div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <tbody>
                    <tr>
                        <td>Bezahlt</td>
                        <td class="text-right">{{ $ordersByState['paid'] ?? 0 }}</td>
                        <td>Versendet</td>
                        <td class="text-right">{{ $ordersByState['sent'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Angekommen</td>
                        <td class="text-right">{{ $ordersByState['received'] ?? 0 }}</td>
                        <td>Bewertet</td>
                        <td class="text-right">{{ $ordersByState['evaluated'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Verloren</td>
                        <td class="text-right">{{ $ordersByState['lost'] ?? 0 }}</td>
                        <td>Storniert</td>
                        <td class="text-right">{{ $ordersByState['canceled'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>