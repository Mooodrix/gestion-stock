<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            color: #333;
        }

        .menu {
            margin-bottom: 20px;
        }

        .menu a {
            margin-right: 20px;
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            border: none;
            background-color: transparent;
            text-align: left;
            outline: none;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            background-color: #e8f0fe;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #45a049;
        }

        .depot-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .depot-box {
            width: 100px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            color: white;
            font-weight: bold;
        }

        .sizes-container {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
        }

        .size-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .size-column input {
            width: 60px;
        }

        .add-size-btn {
            background-color: #008CBA;
        }

        .remove-size-btn {
            background-color: #f44336;
        }
    </style>
</head>
<body>

    <h1>Gérer Stock et Vêtements</h1>

    <div class="menu">
        <a href="#chaussures">Chaussures</a>
        <a href="#tshirts">T-shirts</a>
        <a href="#pulls">Pulls</a>
    </div>

    <!-- Product Management Table -->
    <h2 id="chaussures">Chaussures</h2>
    <table id="products-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Categorie</th>
                <th>Taille</th>
                <th>Stock par dépôt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr data-product-id="{{ $product->id }}">
                <td>
                    <input type="text" value="{{ $product->name }}" class="editable" data-column="name">
                </td>
                <td>
                    <input type="number" value="{{ $product->price }}" class="editable" data-column="price">
                </td>
                <td>
                    <select class="editable" data-column="category_id">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </td>

                <!-- Taille et stock dynamique comme Excel -->
                <td>
                    <div class="sizes-container">
                        @php
                            $sizes = ['S', 'M', 'L', 'XL']; // Tailles en dur
                        @endphp
                        <div class="size-column">
                            @foreach($sizes as $size)
                                <input type="text" value="{{ $size }}" class="editable" data-column="size" data-product-id="{{ $product->id }}">
                            @endforeach
                        </div>
                    </div>
                    <button class="add-size-btn">Ajouter Taille</button>
                    <button class="remove-size-btn">Supprimer Taille</button>
                </td>

                <td>
                    <div class="depot-container">
                        @php
                            $depots = [
                                ['name' => 'Paris', 'color' => 'red'],
                                ['name' => 'Depot 1', 'color' => 'blue'],
                                ['name' => 'Depot 2', 'color' => 'green'],
                                ['name' => 'Depot 3', 'color' => 'yellow']
                            ];
                        @endphp
                        @foreach($depots as $depot)
                            <div class="depot-box" style="background-color: {{ $depot['color'] }};" title="{{ $depot['name'] }}">
                                {{ $depot['name'] }}
                                <input type="number" placeholder="Ex. 5" class="depot-quantity" data-depot="{{ $depot['name'] }}" data-product-id="{{ $product->id }}">
                            </div>
                        @endforeach
                    </div>
                </td>

                <td>
                    <button class="delete-product" data-product-id="{{ $product->id }}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        // Ajout de taille
        document.querySelectorAll('.add-size-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const sizeColumn = this.closest('td').querySelector('.size-column');
                const newSizeInput = document.createElement('input');
                newSizeInput.type = 'text';
                newSizeInput.value = 'New Size';  // Valeur par défaut
                sizeColumn.appendChild(newSizeInput);
            });
        });

        // Suppression de taille
        document.querySelectorAll('.remove-size-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const sizeColumn = this.closest('td').querySelector('.size-column');
                if (sizeColumn.children.length > 0) {
                    sizeColumn.removeChild(sizeColumn.lastElementChild); // Retirer la dernière taille ajoutée
                }
            });
        });

        // Handle inline updates for price, category, size, and quantity
        document.querySelectorAll('.editable').forEach(function(element) {
            element.addEventListener('change', function() {
                const row = this.closest('tr');
                const productId = row.getAttribute('data-product-id');
                const column = this.getAttribute('data-column');
                const value = this.value;

                // Send AJAX request to update the field
                let url = `/products/${productId}`;
                let data = { [column]: value };

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product updated successfully');
                    } else {
                        alert('Error updating product');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Handle product deletion
        document.querySelectorAll('.delete-product').forEach(function(button) {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');

                // Send AJAX request to delete the product
                fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully');
                        this.closest('tr').remove();
                    } else {
                        alert('Error deleting product');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
