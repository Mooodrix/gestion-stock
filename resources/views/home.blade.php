<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products and Stocks</title>
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

        form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
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

        .actions button {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .actions button:hover {
            background-color: #e53935;
        }

        .filter {
            margin-bottom: 20px;
        }

        .filter label {
            font-weight: normal;
        }
    </style>
</head>
<body>

    <h1>Manage Products and Stocks</h1>

    <!-- Formulaire pour ajouter une catégorie -->
    <h2>Add a Category</h2>
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <label for="category_name">Nom de la catégorie:</label>
        <input type="text" name="name" id="category_name" required>
        <button type="submit">>Ajouter la catégorie</button>
    </form>
        <!-- Liste des catégories -->
        <div class="col-md-6">
                <h3>Liste des catégories</h3>
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <ul class="list-group">
                    @foreach($categories as $category)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $category->name }}
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
    <!-- Formulaire pour ajouter un produit -->
    <h2>Add a New Product</h2>
    <form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Nom du produit</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="price">Prix</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
    </div>

    <div class="form-group">
        <label for="category">Catégorie</label>
        <select class="form-control" id="category" name="category_id" required>
            <option value="">Sélectionner une catégorie</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Champ de taille -->
    <div class="form-group">
        <label for="size">Taille (S, M, 32, 34, etc.)</label>
        <input type="text" class="form-control" id="size" name="size" required>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter le produit</button>
</form>


    <!-- Liste des produits avec filtre -->
    <h2>Product List</h2>
    <div class="filter">
        <label for="categoryFilter">Filter by Category:</label>
        <select id="categoryFilter">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Size</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr data-category-id="{{ $product->category->id }}">
                    <td>{{ $product->name }}</td>
                    <td>${{ $product->price }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->size }}</td>
                    <td>
                        <!-- Formulaire pour mettre à jour le stock -->
                        <form action="{{ route('stock.update', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH') <!-- Utilisation de PATCH pour la mise à jour -->
                            <input type="number" name="quantity" value="{{ $product->stock ? $product->stock->quantity : 0 }}" required min="0" style="width: 60px;">
                            <button type="submit">Update Stock</button>
                        </form>
                    </td>
                    <td class="actions">
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        // Function to update size options based on product type selection
        document.getElementById('product_type').addEventListener('change', function() {
            const sizeSelect = document.getElementById('size');
            const selectedType = this.value;

            let sizeOptions = [];
            if (selectedType === 'Clothing') {
                sizeOptions = ['XS', 'S', 'M', 'L', 'XL'];
            } else if (selectedType === 'Pants') {
                sizeOptions = ['32', '34', '36', '38', '40'];
            } else if (selectedType === 'Shoes') {
                sizeOptions = ['36', '37', '38', '39', '40', '41', '42'];
            }

            // Clear current options
            sizeSelect.innerHTML = '';

            // Add new options
            sizeOptions.forEach(function(size) {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                sizeSelect.appendChild(option);
            });
        });

        // Trigger change event to populate initial sizes
        document.getElementById('product_type').dispatchEvent(new Event('change'));
    </script>

</body>
</html>
