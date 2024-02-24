{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des articles par magasin</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Liste des articles par magasin</h1>
    @foreach($items as $store => $storeData)
        <h2>{{ $store }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Référence</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($storeData['original'] as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['reference'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html> --}}

















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles par Magasin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 0;
            padding: 0;
            box-sizing: border-box; */
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
    </style>
</head>
<body>
    <h1>Liste des Articles par Magasin du {{ now()->toDateTimeString() }} </h1>

    @foreach ($data as $store => $items)
        <h2>{{ $store }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Reference</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['reference'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
