<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')

    <style>
        @page { size: A4 }
        body.A4 .sheet { width: 210mm; height: 296mm }

        tr {
            height: 10mm;
        }

        td {
            width: 70mm;
        }
    </style>
</head>
<body class="a4">
    <table class="sheet">
        <tbody>
            <tr>
                @foreach ($expansions as $expansion)
                    @if ($loop->iteration % 3 == 0)
                        </tr>
                        <tr>
                    @endif

                    <td>
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="expansion-icon mr-2" style="background-position: {{ $expansion->icon_position_string }}"></span>
                            <span class="expansion-name">{{ $expansion->name }}</span>
                        </div>
                    </td>

                @endforeach
            </tr>
        </tbody>
    </table>
</body>