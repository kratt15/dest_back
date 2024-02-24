<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des approvisionnements</title>
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
    <h2>Liste des approvisionnements du {{ now()->toDateTimeString() }}</h2>
    <table>
        <thead>
            <tr>
                <th>Mouvement</th>
                <th>Magasin</th>
                <th>Article</th>
                <th>Quantité</th>
                <th>Fournisseur</th>
                <th>Date de réception</th>
                <th>Date prévue de réception</th>
                <th>Date de création</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supply_list as $supply)
                <tr>
                    <td>{{ $supply['movement'] }}</td>
                    <td>{{ $supply['store_name'] }}</td>
                    <td>{{ $supply['item_name'] }}</td>
                    <td>{{ $supply['quantity'] }}</td>
                    <td>{{ $supply['provider_name'] }}</td>
                    <td>{{ $supply['reception_date'] }}</td>
                    <td>{{ $supply['predicted_date'] }}</td>
                    <td>{{ $supply['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

