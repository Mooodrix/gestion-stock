<!-- resources/views/home.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products and Stocks</title>
</head>
<body>

    <h1>Products</h1>

    <!-- Affichage des produits -->
    <div>
        @if($products->isEmpty())
            <p>No products available.</p>
        @else
            <ul>
                @foreach($products as $product)
                    <li>
                        <strong>{{ $product->name }}</strong> - ${{ $product->price }}  
                        <br>
                        Category: {{ $product->category->name }}
                        <br>
                        Stock: {{ $product->stock ? $product->stock->quantity : 'No stock available' }}
                        <br>
                        SKU: {{ $product->sku }}  <!-- Affichage du SKU tel quel -->
                        <br>
                        Description: {{ $product->description }}  <!-- Afficher la description -->
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Formulaire d'ajout d'un produit -->
    <h2>Add a Product</h2>
    <form action="{{ route('products.store') }}" method="POST">
    @csrf
    <label for="name">Product Name:</label>
    <input type="text" name="name" id="name" required>
    
    <label for="price">Price:</label>
    <input type="number" name="price" id="price" step="0.01" min="0" required> <!-- Autoriser les décimales -->
    
    <label for="category_id">Category:</label>
    <select name="category_id" id="category_id" required>
        <option value="">Select a Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    
    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea>

    <label for="size">Size:</label>
    <select name="size" id="size" required>
        <option value="XS">XS</option>
        <option value="S">S</option>
        <option value="M">M</option>
        <option value="L">L</option>
        <option value="XL">XL</option>
    </select>
    
    <input type="submit" value="Add Product">
</form>

</body>
</html>
