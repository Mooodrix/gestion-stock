<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion de Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .menu {
            margin-bottom: 20px;
            text-align: center;
        }

        .menu a {
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        table th, table td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 10px;
        }

        table th {
            background-color: #f2f2f2;
            color: #333;
        }

        table th.colored-header {
            color: black;
        }

        .depot-red { background-color: #FF9999; }    /* Lyon */
        .depot-blue { background-color: #99CCFF; }   /* Paris */
        .depot-orange { background-color: #FFCC99; } /* Colombier */
        .depot-yellow { background-color: #FFFF99; } /* Pouilly */
        .depot-pink { background-color: #FF99CC; }   /* Vaulx en Velin */

        .editable-cell {
            background-color: transparent;
            border: none;
            text-align: center;
            outline: none;
            width: 100%;
        }

        .editable-cell:focus {
            background-color: #e8f0fe;
        }

        .save-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .save-btn:hover {
            background-color: #45a049;
        }

        .sticky-column {
            position: sticky;
            left: 0;
            background-color: #f9f9f9;
            z-index: 2;
        }
    </style>
</head>
<body>

<h1>Gestion de Stock</h1>

<div class="menu">
    <a href="#chaussures">Chaussures</a>
    <a href="#tshirts">T-shirts</a>
    <a href="#pulls">Pulls</a>
</div>

<h2 id="chaussures">Chaussures</h2>

<table>
    <thead>
        <!-- Ligne d'entête (Modèles par dépôt) -->
        <tr>
            <th class="sticky-column" rowspan="2">Taille</th>
            @foreach($products as $product)
                <th colspan="{{ count($depots) }}" class="colored-header">{{ $product->name }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($products as $product)
                @foreach($depots as $depot)
                    <th class="colored-header">{{ $depot['name'] }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Tailles et stocks -->
        @foreach($sizes as $size)
            <tr>
                <td class="sticky-column">{{ $size }}</td>
                @foreach($products as $product)
                    @foreach($depots as $depot)
                        <td>
                            <input 
                                type="number" 
                                class="editable-cell" 
                                value="{{ $stocks[$size][$depot['name']][$product->id] ?? 0 }}" 
                                data-original-value="{{ $stocks[$size][$depot['name']][$product->id] ?? 0 }}" 
                                data-size="{{ $size }}" 
                                data-depot="{{ $depot['name'] }}" 
                                data-product-id="{{ $product->id }}"
                            >
                        </td>
                    @endforeach
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<button class="save-btn">Enregistrer les modifications</button>


<script>
    document.querySelector('.save-btn').addEventListener('click', function () {
        const cells = document.querySelectorAll('.editable-cell');
        const updates = [];

        cells.forEach(cell => {
            const newValue = parseInt(cell.value, 10) || 0; // S'assurer que la valeur est un entier
            const originalValue = parseInt(cell.getAttribute('data-original-value'), 10) || 0;

            // Ajouter l'entrée si la valeur a changé
            if (newValue !== originalValue) {
                updates.push({
                    size: cell.getAttribute('data-size'),
                    depot: cell.getAttribute('data-depot'),
                    product_id: cell.getAttribute('data-product-id'),
                    quantity: newValue,
                });
            }
        });

        if (updates.length === 0) {
            alert('Aucune modification détectée.');
            return;
        }

        fetch('{{ route("update-stock") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(updates), // Envoyer les modifications
        })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                alert(data.message || 'Stocks mis à jour avec succès.');
                location.reload(); // Rafraîchir la page après succès
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour :', error);
                alert('Une erreur est survenue lors de la mise à jour.');
            });
    });
</script>

</body>
</html>