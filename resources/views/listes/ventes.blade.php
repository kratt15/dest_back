<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des ventes</title>
    <style>
         body {
            font-family: Arial, sans-serif;
             margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Liste des ventes du {{ now()->toDateTimeString() }}</h2>
    <table>
        <thead>

            {{--    'movement' => $movement,
                    'store_name' => $store_name,
                    'item_name' => $item_name,
                    'quantity' => $quantity,
                    'customer_name' => $customer_name,
                    'statut' => $statut,
                    'created_at' => $created_at, --}}
            <tr>
                <th>Mouvement</th>
                <th>Magasin</th>
                <th>Article</th>
                <th>Quantité</th>
                <th>Client</th>
                <th>Statut du paiement</th>
                <th>date d'achat</th>

            </tr>
        </thead>
        <tbody>
            @foreach($sales_list as $sales)
                <tr>
                    <td>{{ $sales['movement'] }}</td>
                    <td>{{ $sales['store_name'] }}</td>
                    <td>{{ $sales['item_name'] }}</td>
                    <td>{{ $sales['quantity'] }}</td>
                    <td>{{ $sales['customer_name'] }}</td>
                    <td>{{ $sales['statut'] }}</td>
                    <td>{{ $sales['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

