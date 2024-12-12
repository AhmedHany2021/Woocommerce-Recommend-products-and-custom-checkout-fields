jQuery(document).ready(function ($) {
    $('.add-to-cart-button').on('click', function (e) {
        e.preventDefault();

        const productId = $(this).data('product-id');
        const button = $(this);

        button.text('Adding...').prop('disabled', true);

        $.ajax({
            url: wcAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: productId,
                nonce: wcAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    button.text('Added to Cart');
                } else {
                    button.text('Add to Cart').prop('disabled', false);
                    alert(response.data.message || 'Failed to add the product to the cart.');
                }
            },
            error: function () {
                button.text('Add to Cart').prop('disabled', false);
                alert('An error occurred while adding the product to the cart.');
            }
        });
    });
});