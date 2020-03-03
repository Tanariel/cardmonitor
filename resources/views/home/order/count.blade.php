<div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0 d-none d-xl-block">
    <div class="card h-100">
        <div class="card-header">{{ __('app.nav.order') }}</div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <tbody>
                    <tr>
                        <td>{{ __('order.states.paid') }}</td>
                        <td class="text-right">{{ $ordersByState['paid'] ?? 0 }}</td>
                        <td>{{ __('order.states.sent') }}</td>
                        <td class="text-right">{{ $ordersByState['sent'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('order.states.received') }}</td>
                        <td class="text-right">{{ $ordersByState['received'] ?? 0 }}</td>
                        <td>{{ __('order.states.evaluated') }}</td>
                        <td class="text-right">{{ $ordersByState['evaluated'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('order.states.lost') }}</td>
                        <td class="text-right">{{ $ordersByState['lost'] ?? 0 }}</td>
                        <td>{{ __('order.states.cancelled') }}</td>
                        <td class="text-right">{{ $ordersByState['canceled'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>