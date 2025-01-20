<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h1>Product Management</h1>
    <form id="product-form">
        <input type="hidden" id="product-id" name="product_id">
        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" class="form-control" id="product_name" name="product_name" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity in Stock</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="form-group">
            <label for="price">Price per Item</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h2 class="mt-5">Submitted Products</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity in Stock</th>
            <th>Price per Item</th>
            <th>Datetime Submitted</th>
            <th>Total Value</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="product-table-body">
        <!-- Product rows will be dynamically inserted here -->
        </tbody>
    </table>
    <div id="total-sum" class="font-weight-bold"></div>
</div>

<script>
    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fetch and display products on page load
        fetchProducts();

        // Handle data from submission
        $('#product-form').on('submit', function(event) {
            // Prevent form from default submission
            event.preventDefault();

            const productId = $('#product-id').val();
            const url = productId ? `/products/${productId}` : '/products';
            const method = productId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(data) {
                    // Clear the form
                    $('#product-form')[0].reset();
                    // Clear the hidden product ID
                    $('#product-id').val('');
                    // Fetch and display updated products
                    fetchProducts();
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });
    });

    function fetchProducts() {
        $.ajax({
            url: '/products',
            method: 'GET',
            success: function(data) {
                // Clear existing rows
                $('#product-table-body').empty();
                let totalSum = 0;

                data.forEach(function(product) {
                    const totalValue = product.quantity * product.price;
                    totalSum += totalValue;

                    $('#product-table-body').append(`
                            <tr>
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>${product.price}</td>
                                <td>${product.datetime_submitted}</td>
                                <td>${totalValue.toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" data-id="${product.id}" data-name="${product.product_name}" data-quantity="${product.quantity}" data-price="${product.price}">Edit</button>
                                </td>
                            </tr>
                        `);
                });

                $('#total-sum').text(`Total Value of Products: $${totalSum.toFixed(2)}`);

                // Attach click event to edit buttons
                $('.edit-button').on('click', function() {
                    const productId = $(this).data('id');
                    const productName = $(this).data('name');
                    const productQuantity = $(this).data('quantity');
                    const productPrice = $(this).data('price');

                    // Populate the form with the selected product's data
                    $('#product-id').val(productId);
                    $('#product_name').val(productName);
                    $('#quantity').val(productQuantity);
                    $('#price').val(productPrice);
                });
            },
            error: function(xhr) {
                console.error('Error fetching products:', xhr.responseText);
            }
        });
    }
</script>
</body>
</html>
