@extends('layouts.guest')

@section('content')

    <div class="masthead bg-dark p-5">

        <div class="container h-100">

            <div class="row h-100 align-items-center justify-content-center text-center">

                <div class="col-lg-10 align-self-end">
                    <h1 class="text-uppercase text-white font-weight-bold">Optimiere dein</h1>
                    <a href="https://cardmarket.com"><img alt="Cardmarket.com" class="img-fluid" src="{{ Storage::url('cardmarket/CardmarketLogoWhite1.png') }}" width="300px;"></a>
                    <h1 class="text-uppercase text-white font-weight-bold">Konto</h1>
                    <hr class="divider my-4">

                </div>

                <div class="col-lg-8 align-self-baseline">

                    <h2 class="text-light font-weight-light mb-5">Spare Zeit und steigere deine Verkäufe!</h2>
                    <h2 class="text-light font-weight-light mb-5">Bald geht es los!</h2>

                </div>

            </div>

        </div>

    </div>

    <section id="services" class="page-section">

        <div class="container">

            <h2 class="text-center mt-0">Funktionen</h2>

            <hr class="divider my-4">

            <div class="row">

                <div class="col-lg-3 col-md-6 text-center">

                    <div class="mt-5">

                        <a class="text-body text-decoration-none" href="#overview">
                            <i class="fas fa-4x fa-chart-line text-primary mb-4"></i>
                            <h3 class="h4 mb-2">Behalte den Überblick</h3>
                            <p class="text-muted mb-2">
                                Umsatz, Kosten und Gewinn auf einen Blick.
                            </p>
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 text-center">

                    <div class="mt-5">

                        <a class="text-body text-decoration-none" href="#order">
                            <i class="fas fa-4x fa-shipping-fast text-primary mb-4"></i>
                            <h3 class="h4 mb-2">Bestellungen vorbereiten</h3>
                            <p class="text-muted mb-2">
                                Bereite deine Bestellungen schneller vor.
                            </p>
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 text-center">

                    <div class="mt-5">

                        <a class="text-body text-decoration-none" href="#article">
                            <i class="fas fa-4x fa-plus-square text-primary mb-4"></i>
                            <h3 class="h4 mb-2">Artikel anlegen</h3>
                            <p class="text-muted mb-2">
                                Erstelle neue Artikel schneller ohne die Maus zu nutzen.
                            </p>
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 text-center">

                    <div class="mt-5">

                        <a class="text-body text-decoration-none" href="#price">
                            <i class="fas fa-4x fa-euro-sign text-primary mb-4"></i>
                            <h3 class="h4 mb-2">Verkaufspreise</h3>
                            <p class="text-muted mb-2">
                                Ändere die Preise aller deiner Artikel auf Knopfdruck.
                            </p>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>

@endsection