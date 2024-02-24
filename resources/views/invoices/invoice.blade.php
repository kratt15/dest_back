
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        h6 {
            color: #007bff;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .table-clear {
            width: 100%;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    Facture : {{$ref}}
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        {{ now()->toDateTimeString() }} 
                        <div class="col-sm-6">
                            <h6>De:</h6>
                            <h1>La Destinée </h1>
                            <p>Avenue Maman N'danida</p>
                            <p>Lomé, TOGO</p>
                            <p>Email: info@example.com</p>
                            <p>Téléphone: +123 456 789</p>
                        </div>

                        <div class="col-sm-6">
                            <h6>À:</h6>
                            <p>{{$name}}</p>
                            <p>{{$address}}</p>

                            <p>Téléphone: {{$phone}}</p>
                        </div>
                    </div>

                    <div class="table-responsive-sm">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items_names as $index => $item_name)
                                    <tr>
                                        <td>{{ $item_name }}</td>

                                        <td>{{ $quantities[$index] }}</td>
                                        <td>{{ $price[$index] }} FCFA</td>
                                        <td>{{ $total[$index] }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-5 ml-auto">
                            <table class="table-clear">
                                <tbody>
                                    <tr>
                                        <td class="left">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="right">
                                            <strong>{{ $dueAmount }} FCFA</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Montant payé</strong></td>
                                        <td class="right">
                                            <strong>{{ $totalAmountPaid }} FCFA</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Montant restant</strong></td>
                                        <td class="right">
                                            <strong>{{ $leftOver }} FCFA</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

